<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (for Hermes Orchestrator API key)
        $admin = User::firstOrCreate(
            ['email' => 'admin@deficards.io'],
            [
                'first_name' => 'Admin',
                'last_name' => 'DeFiCard',
                'name' => 'Admin DeFiCard',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Admin123!')),
                'is_admin' => true,
                'phone' => '+0000000000',
            ]
        );

        // Create a Sanctum token for the admin (this is the Hermes API key)
        $admin->tokens()->where('name', 'hermes-orchestrator')->delete();
        $token = $admin->createToken('hermes-orchestrator', ['*']);

        echo "\n══════════════════════════════════════════════\n";
        echo "  🚀 DeFiCard API — Ready!\n";
        echo "══════════════════════════════════════════════\n";
        echo "  Admin Email: admin@deficards.io\n";
        echo "  Admin Password: " . env('ADMIN_PASSWORD', 'Admin123!') . "\n";
        echo "  \n";
        echo "  🔑 HERMES API KEY (copy this):\n";
        echo "  {$token->plainTextToken}\n";
        echo "  \n";
        echo "  Use this key in the Authorization header:\n";
        echo "  Authorization: Bearer {$token->plainTextToken}\n";
        echo "══════════════════════════════════════════════\n\n";
    }
}
