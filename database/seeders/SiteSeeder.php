<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            [
                'name'    => 'Site 1',
                'village' => null,
                'city'    => null,
                'status'  => 'abandoned',
                'notes'   => 'Site abandonné — plus en service.',
            ],
            [
                'name'    => 'Site 2',
                'village' => null,
                'city'    => null,
                'status'  => 'active',
                'notes'   => null,
            ],
            [
                'name'    => 'Site 3',
                'village' => null,
                'city'    => null,
                'status'  => 'active',
                'notes'   => null,
            ],
        ];

        foreach ($sites as $data) {
            Site::firstOrCreate(['name' => $data['name']], $data);
        }

        $this->command->info('SiteSeeder : 3 sites créés (Site 1 abandonné, Site 2 actif, Site 3 actif).');
    }
}
