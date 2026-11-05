<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Seeder;

/** Sayt ikonkalari (resources/icons) + Heroicons (outline) — kutubxonaga bir martalik import. */
class IconSeeder extends Seeder
{
    public function run(): void
    {
        foreach (glob(resource_path('icons/*.svg')) as $f) {
            $n = basename($f, '.svg');
            if (str_starts_with($n, 'logo') || str_starts_with($n, 'flag') || $n === 'hero-deco') {
                continue;
            }
            Icon::firstOrCreate(['name' => $n], ['label' => $n, 'group' => 'site', 'svg' => file_get_contents($f), 'is_system' => true]);
        }

        $dir = base_path('vendor/blade-ui-kit/blade-heroicons/resources/svg');
        foreach (glob($dir.'/o-*.svg') as $f) {
            $n = 'hi-'.substr(basename($f, '.svg'), 2);
            Icon::firstOrCreate(['name' => $n], ['label' => str_replace('-', ' ', substr($n, 3)), 'group' => 'heroicons', 'svg' => file_get_contents($f), 'is_system' => true]);
        }
    }
}
