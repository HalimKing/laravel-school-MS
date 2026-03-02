<?php

if (!function_exists('setting')) {
    /**
     * Get or set application settings
     * 
     * @param string $key Setting key
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed
     */
    function setting($key, $default = null)
    {
        try {
            // Try to get from cache first
            $cache = app('cache');
            $cacheKey = 'system_setting_' . $key;

            if ($cache->has($cacheKey)) {
                return $cache->get($cacheKey);
            }

            // Try to get from database
            $setting = \App\Models\SystemSetting::where('key', $key)->first();

            if ($setting) {
                // Cache for 1 hour
                $cache->put($cacheKey, $setting->value, \Carbon\Carbon::now()->addHour());
                return $setting->value;
            }

            return $default;
        } catch (\Exception $e) {
            // If database is not accessible, return default
            return $default;
        }
    }
}

if (!function_exists('setSetting')) {
    /**
     * Set an application setting in the database
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return void
     */
    function setSetting($key, $value)
    {
        try {
            \App\Models\SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            // Clear cache
            app('cache')->forget('system_setting_' . $key);
        } catch (\Exception $e) {
            // Log error but don't throw
            \Log::error('Failed to set setting: ' . $e->getMessage());
        }
    }
}
