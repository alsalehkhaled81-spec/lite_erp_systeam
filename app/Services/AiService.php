<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    /**
     * Send a chat request to the AI proxy.
     *
     * @param array $messages
     * @return array|null
     */
    public function chat(array $messages): ?array
    {
        $url = config('ai.api_url');
        $key = config('ai.api_key');
        $model = config('ai.model');

        try {
            $response = Http::withHeaders([
                'x-litellm-api-key' => $key,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'model' => $model,
                'messages' => $messages,
                'max_tokens' => config('ai.max_tokens', 4096),
                'temperature' => config('ai.temperature', 0.7),
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('AI Service Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('AI Service Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Process an uploaded file (Livewire TemporaryUploadedFile) and convert it to the format required by the AI.
     *
     * @param mixed $file
     * @return array|null
     */
    public function processUploadedFile($file): ?array
    {
        if (!$file) return null;

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();
        
        // Correct MIME for certain files
        $mimeType = $this->resolveMimeType($extension, $mimeType);
        
        // Handle standard media (images, audio, video) natively supported by Gemini / Multimodal
        if (str_starts_with($mimeType, 'image/') || str_starts_with($mimeType, 'audio/') || str_starts_with($mimeType, 'video/')) {
            $base64 = base64_encode(file_get_contents($path));
            return [
                'type' => 'image_url',
                'image_url' => [
                    'url' => "data:{$mimeType};base64,{$base64}"
                ]
            ];
        }

        // Handle text/documents by embedding content directly
        $textTypes = ['txt', 'json', 'xml', 'md', 'csv', 'js', 'yaml', 'php', 'html', 'css'];
        if (str_starts_with($mimeType, 'text/') || in_array($extension, $textTypes)) {
            $content = file_get_contents($path);
            return [
                'type' => 'text',
                'text' => "File Name: {$file->getClientOriginalName()}\n\nContent:\n{$content}"
            ];
        }

        // Fallback for unsupported or complex files (could try base64 for download by AI, but text is safer here)
        return null;
    }

    /**
     * Resolve missing or incorrect MIME types based on extension.
     */
    private function resolveMimeType($extension, $originalMime)
    {
        $map = [
            'weba' => 'audio/webm',
            'ogg'  => 'audio/ogg',
            'oga'  => 'audio/ogg',
            'm4a'  => 'audio/mp4',
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'flac' => 'audio/flac',
            'aac'  => 'audio/aac',
        ];

        return $map[$extension] ?? $originalMime;
    }
}
