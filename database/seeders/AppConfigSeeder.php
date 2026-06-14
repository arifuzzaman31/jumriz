<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $splashImages = [
            ['width' => 640, 'height' => 1136, 'icon' => 'images/icons/splash-640x1136.png'],
            ['width' => 750, 'height' => 1334, 'icon' => 'images/icons/splash-750x1334.png'],
            ['width' => 828, 'height' => 1792, 'icon' => 'images/icons/splash-828x1792.png'],
            ['width' => 1125, 'height' => 2436, 'icon' => 'images/icons/splash-1125x2436.png'],
            ['width' => 1242, 'height' => 2208, 'icon' => 'images/icons/splash-1242x2208.png'],
            ['width' => 1242, 'height' => 2688, 'icon' => 'images/icons/splash-1242x2688.png'],
            ['width' => 1536, 'height' => 2048, 'icon' => 'images/icons/splash-1536x2048.png'],
            ['width' => 1668, 'height' => 2224, 'icon' => 'images/icons/splash-1668x2224.png'],
            ['width' => 1668, 'height' => 2388, 'icon' => 'images/icons/splash-1668x2388.png'],
            ['width' => 2048, 'height' => 2732, 'icon' => 'images/icons/splash-2048x2732.png'],
        ];

        foreach ($splashImages as $image) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'splash.' . $image['width'] . 'x' . $image['height']],
                ['value' => '/assets/' . $image['icon']]
            );
        }
    }
}