<?php

namespace App\Providers;

use App\Models\Setting\SeoSetting;
use App\Models\Setting\ShopSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('image64', function ($attribute, $value, $parameters, $validator) {
            $type = explode('/', explode(':', substr($value, 0, strpos($value, ';')))[1])[1];
            if (in_array($type, $parameters)) {
                return true;
            }
            return false;
        });

        Validator::replacer('image64', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':values', join(",", $parameters), $message);
        });

        // sharing  data in all view

        $shop_info = cache()->remember('shop-info', 43000, function () {
            return ShopSetting::orderBy('id', 'desc')->first();
        });

        $seo_info = cache()->remember('seo-info', 43000, function () {
            return SeoSetting::orderBy('id', 'desc')->first();
        });

        View::share([
            'shop_info' => $shop_info,
            'seo_info'  => $seo_info,
        ]);

        // Set PWA settings from database
        try {
            $pwaSetting = \App\Models\Setting\PwaSetting::first();
            if ($pwaSetting) {
                config(['laravelpwa.manifest.name' => $pwaSetting->app_name]);
                config(['laravelpwa.manifest.short_name' => $pwaSetting->app_short_name]);
            }
        } catch (\Exception $e) {
            // If the table doesn't exist or any other error, we fall back to the env values.
            // We can log the error if we want, but for now, we just ignore.
        }

        
    }
}
