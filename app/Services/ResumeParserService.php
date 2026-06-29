<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use ZipArchive;

class ResumeParserService
{
    /**
     * Parse text from a PDF or DOCX file.
     *
     * @param string $absolutePath Absolute path to the file on disk.
     * @param string $mimeType Optional MIME type to enforce parsing logic.
     * @return string Extracted text or an empty string.
     */
    public function parse(string $absolutePath, string $mimeType = ''): string
    {
        if (!file_exists($absolutePath)) {
            Log::error("ResumeParserService: File not found at {$absolutePath}");
            return '';
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        
        try {
            if ($extension === 'pdf' || str_contains($mimeType, 'pdf')) {
                return $this->parsePdf($absolutePath);
            } elseif ($extension === 'docx' || str_contains($mimeType, 'wordprocessingml.document')) {
                return $this->parseDocx($absolutePath);
            }
        } catch (Exception $e) {
            Log::error("ResumeParserService: Failed to parse {$absolutePath}. Error: " . $e->getMessage());
        }

        // Return empty string if format is unsupported or an error occurred
        return '';
    }

    /**
     * Parse PDF file using Smalot/PdfParser
     */
    private function parsePdf(string $path): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        return trim($pdf->getText());
    }

    /**
     * Parse DOCX file by reading the zipped word/document.xml
     */
    private function parseDocx(string $path): string
    {
        $zip = new ZipArchive();
        $text = '';
        
        if ($zip->open($path) === true) {
            // Find the document.xml file inside the zip archive
            if (($index = $zip->locateName('word/document.xml')) !== false) {
                $xmlData = $zip->getFromIndex($index);
                $zip->close();
                
                // Replace paragraph tags with newlines for better readability
                $xmlData = str_replace(['</w:p>', '</w:br>'], "\n", $xmlData);
                
                // Strip all other XML tags
                $text = strip_tags($xmlData);
                
                // Decode HTML entities if any
                $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            } else {
                $zip->close();
            }
        }
        
        return trim($text);
    }
}
