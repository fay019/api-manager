<?php

namespace App\Http\Requests;

use App\Http\Responses\ApiResponse;
use App\Services\Ai\AiConfigurationService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GenerateAiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'min:1', 'max:10000'],
            'model' => ['nullable', 'string', Rule::in(app(AiConfigurationService::class)->getAllowedModels())],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        // Retourne une réponse JSON pour les erreurs de validation
        throw new ValidationException($validator, ApiResponse::unprocessable($validator->errors()->toArray()));
    }

    public function resolvedModel(): string
    {
        // Benutze das angegebene Modell oder das Standardmodell
        return $this->input('model') ?? app(AiConfigurationService::class)->getDefaultModel();
    }
}
