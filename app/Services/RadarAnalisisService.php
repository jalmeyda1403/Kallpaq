<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class RadarAnalisisService
{
    protected NormasElPeruanoService $scraper;
    protected AIService $ai;

    public function __construct(NormasElPeruanoService $scraper, AIService $ai)
    {
        $this->scraper = $scraper;
        $this->ai = $ai;
    }

    /**
     * Ejecuta el proceso completo: Scraping -> Filtrado AI -> Resultado
     *
     * @return array Lista de normas filtradas y procesadas
     */
    public function procesarNormasDelDia(): array
    {
        // 1. Obtener todas las normas del día (Scraping)
        Log::info("RadarAnalisis: Iniciando scraping...");
        $todasLasNormas = $this->scraper->fetchNormasDelDia();

        $totalEncontradas = count($todasLasNormas);
        Log::info("RadarAnalisis: {$totalEncontradas} normas encontradas.");

        if ($totalEncontradas === 0) {
            return [];
        }

        // 2. Filtrar normas irrelevantes usando IA
        Log::info("RadarAnalisis: Iniciando filtrado inteligente con IA...");
        $indicesRelevantes = $this->ai->filtrarNormasRelevantes($todasLasNormas);

        $normasFiltradas = [];
        foreach ($indicesRelevantes as $index) {
            if (isset($todasLasNormas[$index])) {
                $normasFiltradas[] = $todasLasNormas[$index];
            }
        }

        $totalFiltradas = count($normasFiltradas);
        Log::info("RadarAnalisis: Filtrado completado. {$totalFiltradas} normas relevantes de {$totalEncontradas}.");

        return $normasFiltradas;
    }
}
