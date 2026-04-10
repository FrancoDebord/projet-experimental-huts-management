<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Sites (3 sites réels) ──────────────────────────────────────────────
        $this->call(SiteSeeder::class);

        // ── Cases (Site 2 → 38, Site 3 → 48) ────────────────────────────────
        $this->call(HutSeeder::class);

        // ── Utilisateur Super Admin par défaut ───────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@airid.org'],
            [
                'nom'      => 'Admin',
                'prenom'   => 'Super',
                'password' => Hash::make('password'),
                'role'     => 'super_admin',
                'active'   => true,
            ]
        );

        $this->command->info('DatabaseSeeder terminé.');
    }
}
