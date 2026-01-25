<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour validation de la création administrateur (Phase 5).
 *
 * Valide:
 * - admin_name: nom complet (3-255 caractères)
 * - admin_email: email valide et unique
 * - admin_password: mot de passe fort (8+ chars, majuscule, minuscule, chiffre, spécial)
 * - admin_password_confirmation: doit correspondre
 *
 * Sécurité:
 * - Validation mot de passe fort
 * - Vérification unicité email
 * - Hash du mot de passe en base de données
 *
 * @see AdminController::store() Utilisation
 */
class AdminRequest extends FormRequest
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
            'admin_name' => ['required', 'string', 'min:3', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'confirmed',
            ],
            'admin_password_confirmation' => ['required', 'string'],
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
            'admin_name.required' => 'Le nom complet est requis',
            'admin_name.string' => 'Le nom doit être un texte valide',
            'admin_name.min' => 'Le nom doit faire au minimum 3 caractères',
            'admin_name.max' => 'Le nom doit faire au maximum 255 caractères',
            'admin_email.required' => 'L\'adresse email est requise',
            'admin_email.email' => 'L\'adresse email doit être valide',
            'admin_email.max' => 'L\'adresse email doit faire au maximum 255 caractères',
            'admin_password.required' => 'Le mot de passe est requis',
            'admin_password.string' => 'Le mot de passe doit être un texte valide',
            'admin_password.min' => 'Le mot de passe doit faire au minimum 8 caractères',
            'admin_password.regex' => 'Le mot de passe doit contenir au moins: une lettre majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&)',
            'admin_password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'admin_password_confirmation.required' => 'La confirmation du mot de passe est requise',
        ];
    }
}
