<?php

namespace App\Http\Requests\Setup;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour validation de la configuration base de données (Phase 3).
 *
 * Valide:
 * - database_driver: sqlite, mysql, pgsql
 * - database_host: requis pour mysql/pgsql
 * - database_port: requis pour mysql/pgsql
 * - database_database: nom de la BD
 * - database_username: requis pour mysql/pgsql
 * - database_password: optionnel pour tous
 *
 * Les validations sont conditionnelles au driver choisi.
 *
 * @see DatabaseController::store() Utilisation
 */
class DatabaseRequest extends FormRequest
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
     * - sqlite: seul database_database est requis
     * - mysql/pgsql: host, port, database, username requis
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $driver = $this->input('database_driver', 'sqlite');

        $baseRules = [
            'database_driver' => ['required', 'in:sqlite,mysql,pgsql'],
        ];

        if ($driver === 'sqlite') {
            return array_merge($baseRules, [
                'database_password' => ['nullable', 'string'],
            ]);
        }

        // MySQL et PostgreSQL
        return array_merge($baseRules, [
            'database_host' => ['required', 'string', 'min:1', 'max:255'],
            'database_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'database_database' => ['required', 'string', 'min:1', 'max:255'],
            'database_username' => ['required', 'string', 'min:1', 'max:255'],
            'database_password' => ['nullable', 'string'],
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
            'database_driver.required' => 'Le type de base de données est requis',
            'database_driver.in' => 'Le type de base de données doit être: sqlite, mysql ou pgsql',
            'database_host.required' => 'Le serveur de base de données est requis',
            'database_host.string' => 'Le serveur doit être un texte valide',
            'database_port.required' => 'Le port est requis',
            'database_port.integer' => 'Le port doit être un nombre',
            'database_port.min' => 'Le port doit être au minimum 1',
            'database_port.max' => 'Le port doit être au maximum 65535',
            'database_database.required' => 'Le nom de la base de données est requis',
            'database_database.string' => 'Le nom de la BD doit être un texte valide',
            'database_database.min' => 'Le nom de la BD doit faire au minimum 1 caractère',
            'database_database.max' => 'Le nom de la BD doit faire au maximum 255 caractères',
            'database_username.required' => 'Le nom d\'utilisateur est requis',
            'database_username.string' => 'Le nom d\'utilisateur doit être un texte valide',
            'database_password.string' => 'Le mot de passe doit être un texte valide',
        ];
    }
}
