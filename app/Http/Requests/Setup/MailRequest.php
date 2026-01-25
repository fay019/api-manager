<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour validation de la configuration email (Phase 4).
 *
 * Valide:
 * - MAIL_DRIVER: smtp, sendmail, mailgun, ses, postmark, log
 * - MAIL_HOST: serveur SMTP
 * - MAIL_PORT: port SMTP
 * - MAIL_USERNAME: identifiant SMTP
 * - MAIL_PASSWORD: mot de passe SMTP
 * - MAIL_ENCRYPTION: null, tls, ssl
 * - MAIL_FROM_ADDRESS: adresse email par défaut
 * - MAIL_FROM_NAME: nom par défaut
 *
 * @see MailController::store() Utilisation
 */
class MailRequest extends FormRequest
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
     * Les validations sont conditionnelles au driver:
     * - smtp: tous les champs requis
     * - sendmail: juste path
     * - log: aucun champ requis
     * - autres: dépend du provider
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $driver = $this->input('mail_driver', 'log');

        $baseRules = [
            'mail_driver' => ['required', 'in:smtp,sendmail,mailgun,ses,postmark,log'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required', 'string', 'min:1', 'max:255'],
        ];

        if ($driver === 'smtp') {
            return array_merge($baseRules, [
                'mail_host' => ['required', 'string', 'min:1', 'max:255'],
                'mail_port' => ['required', 'integer', 'min:1', 'max:65535'],
                'mail_username' => ['required', 'string', 'min:1', 'max:255'],
                'mail_password' => ['nullable', 'string'],
                'mail_encryption' => ['nullable', 'in:tls,ssl'],
            ]);
        }

        if ($driver === 'sendmail') {
            return array_merge($baseRules, [
                'mail_path' => ['required', 'string'],
            ]);
        }

        if ($driver === 'log') {
            return $baseRules;
        }

        // Pour les autres drivers (mailgun, ses, postmark)
        return array_merge($baseRules, [
            'mail_secret' => ['nullable', 'string'],
        ]);
    }

    /**
     * Récupère les messages d'erreur personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mail_driver.required' => 'Le type de serveur email est requis',
            'mail_driver.in' => 'Le type de serveur email est invalide',
            'mail_host.required' => 'Le serveur SMTP est requis',
            'mail_host.string' => 'Le serveur SMTP doit être un texte valide',
            'mail_port.required' => 'Le port SMTP est requis',
            'mail_port.integer' => 'Le port SMTP doit être un nombre',
            'mail_port.min' => 'Le port SMTP doit être au minimum 1',
            'mail_port.max' => 'Le port SMTP doit être au maximum 65535',
            'mail_username.required' => 'Le nom d\'utilisateur SMTP est requis',
            'mail_username.string' => 'Le nom d\'utilisateur SMTP doit être un texte valide',
            'mail_password.string' => 'Le mot de passe SMTP doit être un texte valide',
            'mail_from_address.required' => 'L\'adresse email source est requise',
            'mail_from_address.email' => 'L\'adresse email source doit être valide',
            'mail_from_name.required' => 'Le nom source est requis',
            'mail_from_name.string' => 'Le nom source doit être un texte valide',
            'mail_encryption.in' => 'Le chiffrement doit être: tls ou ssl',
            'mail_path.required' => 'Le chemin du serveur sendmail est requis',
            'mail_secret.string' => 'La clé secrète doit être un texte valide',
        ];
    }
}
