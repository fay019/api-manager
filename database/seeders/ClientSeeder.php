<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing clients
        Client::query()->delete();

        $clients = [
            [
                'name' => 'Fayçal Moussouni',
                'first_name' => 'Fayçal',
                'last_name' => 'Moussouni',
                'email' => 'fay019@gmail.com',
                'password' => bcrypt('password123'),
                'company_name' => 'Moussouni Dev',
                'phone' => '+33612345678',
                'country' => 'FR',
                'timezone' => 'Europe/Paris',
                'language' => 'fr',
                'billing_email' => 'billing@moussouni.dev',
                'contact_email' => 'fay019@gmail.com',
                'is_active' => true,
                'activated_at' => now(),
                'address_json' => json_encode(['street' => '123 Rue de Paris', 'city' => 'Paris', 'postal_code' => '75001']),
            ],
            [
                'name' => 'Jean Dupont',
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@example.com',
                'password' => bcrypt('password123'),
                'company_name' => 'Dupont SAS',
                'phone' => '+33612345679',
                'country' => 'FR',
                'timezone' => 'Europe/Paris',
                'language' => 'fr',
                'billing_email' => 'billing@dupont.fr',
                'contact_email' => 'jean.dupont@example.com',
                'is_active' => true,
                'activated_at' => now(),
                'address_json' => json_encode(['street' => '456 Rue de Lyon', 'city' => 'Lyon', 'postal_code' => '69001']),
            ],
            [
                'name' => 'Sarah Johnson',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@techcorp.com',
                'password' => bcrypt('password123'),
                'company_name' => 'TechCorp US',
                'phone' => '+14155551234',
                'country' => 'US',
                'timezone' => 'America/New_York',
                'language' => 'en',
                'billing_email' => 'accounting@techcorp.com',
                'contact_email' => 'sarah.johnson@techcorp.com',
                'is_active' => true,
                'activated_at' => now(),
                'address_json' => json_encode(['street' => '789 Tech Ave', 'city' => 'San Francisco', 'postal_code' => '94105']),
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        echo "✅ Created " . count($clients) . " seed clients\n";
    }
}
