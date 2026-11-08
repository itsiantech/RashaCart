<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Add-on directory
    |--------------------------------------------------------------------------
    |
    | Every add-on lives in its own folder inside this directory and is
    | described by an addon.json manifest. Change it in tests to point the
    | AddonManager at fixture add-ons.
    |
    */

    'path' => env('ADDONS_PATH', base_path('addons')),

    /*
    |--------------------------------------------------------------------------
    | Core version
    |--------------------------------------------------------------------------
    |
    | Value compared against each manifest's "requires_core" constraint.
    | Bump this when a release changes the hook API in a breaking way.
    |
    */

    'core_version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | Active add-on cache
    |--------------------------------------------------------------------------
    |
    | The boot routine must stay cheap on every request, so the list of active
    | slugs is cached and invalidated whenever an add-on status changes.
    |
    */

    'cache_key' => 'addons.active_slugs',

];
