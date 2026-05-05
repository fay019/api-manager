<?php

namespace App\Services\Ai;

use App\Exceptions\Ai\OllamaInvalidResponseException;
use App\Exceptions\Ai\OllamaModelNotFoundException;
use App\Exceptions\Ai\OllamaTimeoutException;
use App\Exceptions\Ai\OllamaUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class OllamaService
{
    public function __construct(private AiConfigurationService $config) {}

    public function listModels(): array
    {
        // Cache des Modellenlisten für 60 Sekunden
        $cacheKey = $this->config->getCacheKey();

        return Cache::remember($cacheKey, 60, function () {
            try {
                $response = Http::timeout(10)->get(
                    $this->config->getBaseUrl().'/api/tags'
                );

                if (! $response->successful()) {
                    throw new OllamaUnavailableException('Ollama /api/tags antwortet nicht erfolgreich');
                }

                $data = $response->json();

                if (! isset($data['models']) || ! is_array($data['models'])) {
                    throw new OllamaInvalidResponseException('Ungültige Struktur von /api/tags');
                }

                // Filtere auf erlaubte Modelle
                $allowedModels = $this->config->getAllowedModels();
                $filteredModels = array_filter(
                    $data['models'],
                    fn ($model) => in_array($model['name'] ?? null, $allowedModels)
                );

                return array_values($filteredModels);
            } catch (ConnectionException $e) {
                throw new OllamaTimeoutException('Verbindung zu Ollama hat ausgefallen');
            }
        });
    }

    public function generate(string $prompt, string $model): array
    {
        // Überprüfe, dass das Modell erlaubt ist
        $allowedModels = $this->config->getAllowedModels();
        if (! in_array($model, $allowedModels)) {
            throw new OllamaModelNotFoundException("Modell '{$model}' ist nicht in der erlaubten Liste");
        }

        try {
            $response = Http::timeout($this->config->getTimeout())->post(
                $this->config->getBaseUrl().'/api/generate',
                [
                    'model' => $model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]
            );

            if (! $response->successful()) {
                if ($response->status() >= 500) {
                    throw new OllamaUnavailableException('Ollama Server antwortet mit Fehler');
                }

                throw new OllamaInvalidResponseException('Ollama antwortet mit Status '.$response->status());
            }

            $data = $response->json();

            if (! isset($data['done']) || $data['done'] !== true) {
                throw new OllamaInvalidResponseException('Generierung wurde nicht abgeschlossen');
            }

            // Konvertiere total_duration von Nanosekunden zu Millisekunden
            $totalDurationNs = $data['total_duration'] ?? 0;
            $durationMs = (int) ($totalDurationNs / 1_000_000);

            return [
                'model' => $data['model'] ?? $model,
                'response' => $data['response'] ?? '',
                'done' => true,
                'duration_ms' => $durationMs,
                'prompt_eval_count' => $data['prompt_eval_count'] ?? 0,
                'eval_count' => $data['eval_count'] ?? 0,
            ];
        } catch (ConnectionException|RequestException $e) {
            if (str_contains($e->getMessage(), 'timeout')) {
                throw new OllamaTimeoutException('Ollama antwortet nicht in der erwarteten Zeit');
            }

            throw new OllamaUnavailableException('Verbindung zu Ollama fehlgeschlagen');
        }
    }

    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(10)->get(
                $this->config->getBaseUrl().'/api/tags'
            );

            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }
}
