<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetFactory;
use PhpOffice\PhpWord\IOFactory as WordFactory;

class ChatbotKnowledgeService
{
    protected $docsPath = 'chatbot_docs';

    public function getRelevantContext(string $query): string
    {
        $files = Storage::files($this->docsPath);
        $context = "";
        $foundFiles = 0;

        // Simple keyword matching to select relevant files
        // In a real production system, we would use vector embeddings (e.g., Pinecone, pgvector)
        $keywords = $this->extractKeywords($query);

        foreach ($files as $file) {
            if ($foundFiles >= 3)
                break; // Limit to 3 files to avoid token limits

            $filename = basename($file);
            $isRelevant = false;

            foreach ($keywords as $keyword) {
                if (stripos($filename, $keyword) !== false) {
                    $isRelevant = true;
                    break;
                }
            }

            // If query is generic or no keywords matched, maybe include "Manual" or "Guia" files
            if (empty($keywords) && (stripos($filename, 'manual') !== false || stripos($filename, 'guia') !== false)) {
                $isRelevant = true;
            }

            if ($isRelevant) {
                $content = $this->parseFile($file);
                if ($content) {
                    $context .= "Documento: $filename\nContenido:\n" . substr($content, 0, 2000) . "\n\n"; // Limit content per file
                    $foundFiles++;
                }
            }
        }

        return $context;
    }

    protected function extractKeywords(string $query): array
    {
        // Remove stop words and split
        $stopWords = ['el', 'la', 'los', 'las', 'un', 'una', 'de', 'del', 'y', 'o', 'que', 'en', 'por', 'para', 'con', 'como', 'quiero', 'ver', 'dame', 'muestrame'];
        $words = explode(' ', strtolower($query));
        return array_filter($words, function ($w) use ($stopWords) {
            return strlen($w) > 3 && !in_array($w, $stopWords);
        });
    }

    protected function parseFile(string $path): ?string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $fullPath = Storage::path($path);

        try {
            switch ($extension) {
                case 'pdf':
                    $parser = new PdfParser();
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
                            $data[] = implode(", ", $cells);
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
                                    $text .= $element->getText() . "\n";
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
            Log::error("Error parsing file $path: " . $e->getMessage());
            return null;
        }
    }
}
