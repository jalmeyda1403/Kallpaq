<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;
use PhpOffice\PhpWord\IOFactory as WordFactory;
use Smalot\PdfParser\Parser as PdfParser;

class ChatbotKnowledgeService
{
    protected $docsPath = 'chatbot_docs';

    public function getRelevantContext(string $query): string
    {
        $files = Storage::files($this->docsPath);
        $context = '';
        $foundFiles = 0;
        $maxFiles = 5; // Increased limit

        $keywords = $this->extractKeywords($query);

        // Sort files to prioritize those matching keywords in filename
        usort($files, function ($a, $b) use ($keywords) {
            $scoreA = 0;
            $scoreB = 0;
            $nameA = basename($a);
            $nameB = basename($b);

            foreach ($keywords as $keyword) {
                if (stripos($nameA, $keyword) !== false) {
                    $scoreA++;
                }
                if (stripos($nameB, $keyword) !== false) {
                    $scoreB++;
                }
            }

            return $scoreB <=> $scoreA; // Descending order
        });

        foreach ($files as $file) {
            if ($foundFiles >= $maxFiles) {
                break;
            }

            $filename = basename($file);
            $isRelevant = false;

            // 1. Check direct keyword match in filename
            foreach ($keywords as $keyword) {
                if (stripos($filename, $keyword) !== false) {
                    $isRelevant = true;
                    break;
                }
            }

            // 2. Include Core Documents (Manuals, System Specs, Policies)
            // This ensures files like "Sistema de Gestion.txt" are always included as context base
            if (! $isRelevant) {
                $coreTerms = ['manual', 'guia', 'sistema', 'gestion', 'politica', 'objetivo', 'kallpaq', 'plan'];
                foreach ($coreTerms as $term) {
                    if (stripos($filename, $term) !== false) {
                        $isRelevant = true;
                        break;
                    }
                }
            }

            if ($isRelevant) {
                $content = $this->parseFile($file);
                if ($content) {
                    $context .= "Documento: $filename\nContenido:\n".substr($content, 0, 3000)."\n\n"; // Increased char limit
                    $foundFiles++;
                }
            }
        }

        return $context;
    }

    protected function extractKeywords(string $query): array
    {
        // Remove stop words and split
        $stopWords = ['el', 'la', 'los', 'las', 'un', 'una', 'de', 'del', 'y', 'o', 'que', 'en', 'por', 'para', 'con', 'como', 'quiero', 'ver', 'dame', 'muestrame', 'cual', 'es', 'son'];
        $words = explode(' ', strtolower($query));

        return array_filter($words, function ($w) use ($stopWords) {
            return strlen($w) >= 3 && ! in_array($w, $stopWords); // Allow 3 chars (e.g. ISO)
        });
    }

    protected function parseFile(string $path): ?string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $fullPath = Storage::path($path);

        try {
            switch ($extension) {
                case 'pdf':
                    $parser = new PdfParser;
                    $pdf = $parser->parseFile($fullPath);

                    return $pdf->getText();

                case 'txt':
                case 'csv':
                    return file_get_contents($fullPath);

                case 'xlsx':
                case 'xls':
                    if (class_exists(SpreadsheetFactory::class)) {
                        $spreadsheet = SpreadsheetFactory::load($fullPath);
                        $sheet = $spreadsheet->getActiveSheet();
                        $data = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $cells = [];
                            foreach ($cellIterator as $cell) {
                                $cells[] = $cell->getValue();
                            }
                            $data[] = implode(', ', $cells);
                        }

                        return implode("\n", $data);
                    }

                    return null;

                case 'docx':
                case 'doc':
                    if (class_exists(WordFactory::class)) {
                        $phpWord = WordFactory::load($fullPath);
                        $text = '';
                        foreach ($phpWord->getSections() as $section) {
                            foreach ($section->getElements() as $element) {
                                if (method_exists($element, 'getText')) {
                                    $text .= $element->getText()."\n";
                                }
                            }
                        }

                        return $text;
                    }

                    return null;

                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error parsing file $path: ".$e->getMessage());

            return null;
        }
    }
}
