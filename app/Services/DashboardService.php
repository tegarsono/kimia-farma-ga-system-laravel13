<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;


class DashboardService
{
    public function getImageSetting(string $key, string $default = ''): string
    {
        try {
            $setting = DB::table('image_settings')
                ->where('image_key', $key)
                ->first();
        } catch (\Exception $e) {
            return $default;
        }

        if (!$setting) {
            return $default;
        }

        if ($setting->image_type === 'url') {
            return $setting->image_value;
        }

        return url('storage/' . $setting->image_value);
    }
}

