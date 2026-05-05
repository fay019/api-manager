<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Ai\AiDisabledException;
use App\Exceptions\Ai\OllamaInvalidResponseException;
use App\Exceptions\Ai\OllamaModelNotFoundException;
use App\Exceptions\Ai\OllamaTimeoutException;
use App\Exceptions\Ai\OllamaUnauthorizedException;
use App\Exceptions\Ai\OllamaUnavailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateAiRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Ai\AiConfigurationService;
use App\Services\Ai\OllamaService;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    public function __construct(
        private OllamaService $ollama,
        private AiConfigurationService $config,
    ) {}

    public function health(): JsonResponse
    {
        // Wenn die IA nicht aktiv ist, gib "unavailable" zurück, aber keinen Fehler
        if (! $this->config->isActive()) {
            return ApiResponse::success(['status' => 'unavailable']);
        }

        // Überprüfe Ollama-Verbindung
        $isHealthy = $this->ollama->isHealthy();

        return ApiResponse::success([
            'status' => $isHealthy ? 'ok' : 'unavailable',
        ]);
    }

    public function models(): JsonResponse
    {
        try {
            // Wenn die IA nicht aktiv ist, gib einen Fehler zurück
            if (! $this->config->isActive()) {
                throw new AiDisabledException('Der KI-Service ist nicht aktiv');
            }

            $models = $this->ollama->listModels();

            return ApiResponse::success(['models' => $models]);
        } catch (AiDisabledException) {
            return ApiResponse::error('AI_DISABLED', 'Der KI-Service ist nicht verfügbar', [], 503);
        } catch (OllamaUnauthorizedException) {
            return ApiResponse::error('AI_UNAUTHORIZED', 'Internal token missing or invalid', [], 403);
        } catch (OllamaUnavailableException) {
            return ApiResponse::error('AI_PROVIDER_ERROR', 'Der KI-Server ist nicht verfügbar', [], 503);
        } catch (OllamaTimeoutException) {
            return ApiResponse::error('AI_TIMEOUT', 'Die Anfrage zum KI-Server hat das Zeitlimit überschritten', [], 504);
        } catch (OllamaInvalidResponseException) {
            return ApiResponse::error('AI_INVALID_RESPONSE', 'Der KI-Server hat mit einer ungültigen Antwort geantwortet', [], 502);
        }
    }

    public function generate(GenerateAiRequest $request): JsonResponse
    {
        try {
            // Wenn die IA nicht aktiv ist, gib einen Fehler zurück
            if (! $this->config->isActive()) {
                throw new AiDisabledException('Der KI-Service ist nicht aktiv');
            }

            $prompt = $request->input('prompt');
            $model = $request->resolvedModel();

            $result = $this->ollama->generate($prompt, $model);

            return ApiResponse::success(
                [
                    'model' => $result['model'],
                    'response' => $result['response'],
                    'done' => $result['done'],
                ],
                [
                    'duration_ms' => $result['duration_ms'],
                    'prompt_eval_count' => $result['prompt_eval_count'],
                    'eval_count' => $result['eval_count'],
                ]
            );
        } catch (AiDisabledException) {
            return ApiResponse::error('AI_DISABLED', 'Der KI-Service ist nicht verfügbar', [], 503);
        } catch (OllamaUnauthorizedException) {
            return ApiResponse::error('AI_UNAUTHORIZED', 'Internal token missing or invalid', [], 403);
        } catch (OllamaTimeoutException) {
            return ApiResponse::error('AI_TIMEOUT', 'Der KI-Server hat nicht in der erwarteten Zeit geantwortet', [], 504);
        } catch (OllamaUnavailableException) {
            return ApiResponse::error('AI_PROVIDER_ERROR', 'Der KI-Server ist nicht verfügbar', [], 503);
        } catch (OllamaInvalidResponseException) {
            return ApiResponse::error('AI_INVALID_RESPONSE', 'Der KI-Server hat mit einer ungültigen Antwort geantwortet', [], 502);
        } catch (OllamaModelNotFoundException) {
            return ApiResponse::error('AI_MODEL_NOT_FOUND', 'Das angeforderte Modell wurde auf dem KI-Server nicht gefunden', [], 422);
        }
    }

    public function test(): JsonResponse
    {
        try {
            if (! $this->config->isActive()) {
                throw new AiDisabledException('Der KI-Service ist nicht aktiv');
            }

            // Einfacher Test: sende "Bonjour" an das Standardmodell
            $model = $this->config->getDefaultModel();
            $result = $this->ollama->generate('Bonjour', $model);

            return ApiResponse::success(
                [
                    'model' => $result['model'],
                    'response' => $result['response'],
                    'done' => $result['done'],
                ],
                [
                    'duration_ms' => $result['duration_ms'],
                    'prompt_eval_count' => $result['prompt_eval_count'],
                    'eval_count' => $result['eval_count'],
                ]
            );
        } catch (AiDisabledException) {
            return ApiResponse::error('AI_DISABLED', 'Der KI-Service ist nicht verfügbar', [], 503);
        } catch (OllamaUnauthorizedException) {
            return ApiResponse::error('AI_UNAUTHORIZED', 'Internal token missing or invalid', [], 403);
        } catch (OllamaTimeoutException) {
            return ApiResponse::error('AI_TIMEOUT', 'Der KI-Server hat nicht in der erwarteten Zeit geantwortet', [], 504);
        } catch (OllamaUnavailableException) {
            return ApiResponse::error('AI_PROVIDER_ERROR', 'Der KI-Server ist nicht verfügbar', [], 503);
        } catch (OllamaInvalidResponseException) {
            return ApiResponse::error('AI_INVALID_RESPONSE', 'Der KI-Server hat mit einer ungültigen Antwort geantwortet', [], 502);
        } catch (OllamaModelNotFoundException) {
            return ApiResponse::error('AI_MODEL_NOT_FOUND', 'Das Standard-Modell wurde auf dem KI-Server nicht gefunden', [], 422);
        }
    }
}
