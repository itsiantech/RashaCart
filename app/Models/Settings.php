<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache; 
class Settings extends Model
{
    protected $fillable = ['key', 'value'];
    protected static $settings = null;

   
    public static function getAllSettings()
    {
        if (is_null(self::$settings)) {
            self::$settings = Cache::remember('app_settings', now()->addDay(), function () {
                return self::pluck('value', 'key')->all();
            });
        }
        return self::$settings;
    }

    public static function clearCache()
    {
        Cache::forget('app_settings');
        self::$settings = null;
    }

  
    public function save(array $options = [])
    {
        parent::save($options);
        self::clearCache();
    }
}
