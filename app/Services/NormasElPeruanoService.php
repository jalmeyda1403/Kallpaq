<?php

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class NormasElPeruanoService
{
    protected Client $http;
    // URL AJAX directa que devuelve el listado de normas
    protected string $baseUrl = 'https://diariooficial.elperuano.pe/Normas/LoadNormasLegales?Length=0';

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'X-Requested-With' => 'XMLHttpRequest', // Importante para que el servidor responda como AJAX
            ],
            'verify' => false,
        ]);
    }

    /**
     * Obtiene las normas listadas en la página de "Normas" y las normaliza.
     *
     * @return array Lista de normas normalizadas
     */
    public function fetchNormasDelDia(): array
    {
        try {
            Log::info("Iniciando scraping de El Peruano (AJAX): {$this->baseUrl}");

            // 1) Descargar HTML del endpoint AJAX
            $resp = $this->http->get($this->baseUrl);
            $html = (string) $resp->getBody();

            // 2) Parsear con DomCrawler
            $crawler = new Crawler($html);

            // 3) Selección de items (AJAX devuelve articles directos)
            $items = $crawler->filter('article.edicionesoficiales_articulos');

            Log::info("Items encontrados: " . $items->count());

            $normas = [];

            foreach ($items as $domElement) {
                try {
                    $itemCrawler = new Crawler($domElement);

                    // EXTRAER campos
                    // Organismo: h4
                    $organismo_emisor = $this->firstText($itemCrawler, ['.ediciones_texto h4']);

                    // Título y Número: h5 a
                    $tituloNode = $itemCrawler->filter('.ediciones_texto h5 a');
                    $tituloFull = $tituloNode->count() ? trim($tituloNode->text()) : '';
                    $link = $tituloNode->count() ? $tituloNode->attr('href') : null;

                    // Normalizar link (a veces viene relativo)
                    if ($link && !str_starts_with($link, 'http')) {
                        $link = 'https://diariooficial.elperuano.pe' . $link;
                    }

                    // Separar Número de Título si es posible (ej: "LEY N° 32507")
                    $numero_norma = $tituloFull;
                    $titulo = $tituloFull; // A veces el título real está en el resumen

                    // Fecha: p b (Fecha: 27/11/2025)
                    $fecha_text = $this->firstText($itemCrawler, ['.ediciones_texto p b']);
                    if ($fecha_text) {
                        $fecha_text = str_replace('Fecha:', '', $fecha_text);
                    }

                    // Resumen/Texto: Segundo p dentro de ediciones_texto
                    // El primer p es la fecha, el segundo es el resumen/título descriptivo
                    $resumenNode = $itemCrawler->filter('.ediciones_texto p')->eq(1);
                    $texto_completo = $resumenNode->count() ? trim($resumenNode->text()) : '';

                    // Si el título es solo el número (corto), usar el resumen como título descriptivo
                    if (strlen($titulo) < 30 && !empty($texto_completo)) {
                        $titulo = substr($texto_completo, 0, 150) . (strlen($texto_completo) > 150 ? '...' : '');
                    }

                    $fecha_publicacion = $this->normalizeFecha($fecha_text) ?? Carbon::now()->toDateString();
                    $resumen_ia = $this->generarResumenSimple($texto_completo ?: $titulo);
                    $nivel_relevancia = $this->calcularRelevancia($titulo . ' ' . $texto_completo);

                    $norma = [
                        'titulo' => $titulo ?: 'Sin título',
                        'numero_norma' => $numero_norma ?: 'S/N',
                        'organismo_emisor' => $organismo_emisor ?: 'Desconocido',
                        'fecha_publicacion' => $fecha_publicacion,
                        'resumen_ia' => $resumen_ia,
                        'texto_completo' => $texto_completo ?: '',
                        'nivel_relevancia' => $nivel_relevancia,
                        'estado' => 'Pendiente',
                        'obligacion_principal' => $this->detectarObligacionPrincipal($texto_completo ?: $titulo),
                        'url_fuente' => $link
                    ];

                    $normas[] = $norma;

                } catch (\Exception $e) {
                    Log::error("Error procesando item: " . $e->getMessage());
                    continue;
                }
            }

            return $normas;

        } catch (\Exception $e) {
            Log::error("Error general en NormasElPeruanoService: " . $e->getMessage());
            throw $e;
        }
    }

    protected function firstText(Crawler $crawler, array $selectors): ?string
    {
        foreach ($selectors as $sel) {
            try {
                $node = $crawler->filter($sel);
                if ($node->count()) {
                    $text = trim($node->first()->text());
                    if (!empty($text)) {
                        return $text;
                    }
                }
            } catch (\Exception $e) {
            }
        }
        return null;
    }

    protected function normalizeFecha(?string $text): ?string
    {
        if (empty($text))
            return null;
        $text = trim($text);

        if (preg_match('/(\d{2}[\/\-]\d{2}[\/\-]\d{4})/', $text, $m)) {
            try {
                return Carbon::createFromFormat('d/m/Y', $m[1])->toDateString();
            } catch (\Exception $e) {
            }
            try {
                return Carbon::createFromFormat('d-m-Y', $m[1])->toDateString();
            } catch (\Exception $e) {
            }
        }
        if (preg_match('/(\d{4}\-\d{2}\-\d{2})/', $text, $m)) {
            return $m[1];
        }
        try {
            return Carbon::parse($text)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function generarResumenSimple(string $texto, int $sentences = 2): string
    {
        $texto = strip_tags($texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        $oraciones = preg_split('/(?<=[.?!])\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY);
        $selected = array_slice($oraciones, 0, $sentences);
        $res = trim(implode(' ', $selected));
        return $res ?: 'No disponible';
    }

    protected function calcularRelevancia(string $texto): string
    {
        $texto = mb_strtolower($texto);
        $alta = ['emergencia', 'sanción', 'inconstitucional', 'concurso', 'obligación', 'prohibición', 'pena', 'multas', 'seguridad', 'salud', 'trabajo', 'ambiente'];
        $countAlta = 0;
        foreach ($alta as $k) {
            if (mb_strpos($texto, $k) !== false)
                $countAlta++;
        }
        if ($countAlta >= 2)
            return 'Alta';
        if ($countAlta === 1)
            return 'Media';
        return 'Baja';
    }

    protected function detectarObligacionPrincipal(string $texto): string
    {
        $texto = strip_tags($texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        $matches = [];
        if (preg_match('/(deberá|deberán|obligatorio|obligan a|es obligatorio|debe|dispón|dispone que|se obliga)/i', $texto, $m)) {
            preg_match('/([^.!?]*(' . $m[0] . ')[^.!?]*[.!?]?)/i', $texto, $or);
            if (!empty($or[0])) {
                return trim($or[0]);
            }
        }
        $sentences = preg_split('/(?<=[.?!])\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY);
        return trim($sentences[0] ?? 'Obligación no identificada');
    }
}
