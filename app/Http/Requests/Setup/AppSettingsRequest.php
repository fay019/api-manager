<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour validation des paramètres applicatifs (Phase 2).
 *
 * Valide:
 * - APP_NAME: min 3, max 255 caractères
 * - APP_URL: URL valide
 * - APP_ENV: in [local, staging, production]
 * - APP_DEBUG: boolean
 * - TIMEZONE: timezone valide PHP
 * - LOCALE: in [fr, en, es, ...]
 *
 * @see AppSettingsController::store() Utilisation
 */
class AppSettingsRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * Toujours true pour le setup (pas d'auth requise yet).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Récupère les règles de validation pour cette requête.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'min:3', 'max:255'],
            'app_url' => ['required', 'url'],
            'app_env' => ['required', 'in:local,staging,production'],
            'app_debug' => ['nullable', 'boolean'],
            'timezone' => ['required', 'timezone'],
            'locale' => ['required', 'string', 'in:fr,en,es'],
        ];
    }

    /**
     * Récupère les messages d'erreur personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'app_name.required' => 'Le nom de l\'application est requis',
            'app_name.min' => 'Le nom doit faire au minimum 3 caractères',
            'app_name.max' => 'Le nom doit faire au maximum 255 caractères',
            'app_url.required' => 'L\'URL de l\'application est requise',
            'app_url.url' => 'L\'URL doit être valide (ex: http://example.com)',
            'app_env.required' => 'L\'environnement est requis',
            'app_env.in' => 'L\'environnement doit être: local, staging ou production',
            'timezone.required' => 'Le fuseau horaire est requis',
            'timezone.timezone' => 'Le fuseau horaire est invalide',
            'locale.required' => 'La langue est requise',
            'locale.in' => 'La langue doit être: fr, en ou es',
        ];
    }
}
