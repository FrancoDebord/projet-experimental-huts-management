<?php

namespace Database\Seeders;

use App\Models\Hut;
use App\Models\Site;
use Illuminate\Database\Seeder;

class HutSeeder extends Seeder
{
    /**
     * Configuration des cases par site.
     * Site 1 (abandonné) → aucune case créée.
     * Site 2 (actif)      → 38 cases (1-38), toutes disponibles.
     * Site 3 (actif)      → 48 cases (1-48), toutes disponibles.
     */
    private const SITE_HUTS = [
        'Site 2' => 38,
        'Site 3' => 48,
    ];

    public function run(): void
    {
        foreach (self::SITE_HUTS as $siteName => $hutCount) {
            $site = Site::where('name', $siteName)->first();

            if (!$site) {
                $this->command->warn("Site « {$siteName} » introuvable — lancer SiteSeeder d'abord.");
                continue;
            }

            $created = 0;
            for ($n = 1; $n <= $hutCount; $n++) {
                $hutName = "{$site->name} Case {$n}";

                Hut::firstOrCreate(
                    ['site_id' => $site->id, 'number' => $n],
                    [
                        'name'   => $hutName,
                        'status' => 'available',
                        'notes'  => null,
                    ]
                );
                $created++;
            }

            $this->command->info("{$siteName} : {$created} case(s) créée(s) (1 à {$hutCount}).");
        }
    }
}
