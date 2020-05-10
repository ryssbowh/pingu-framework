<?php

return [
    'views_path' => 'views',
    'asset_path' => 'assets',
    /**
     * Public path for themes assets linkage
     */
    'public_path' => 'themes',

    /**
     * Folder for module views within themes
     */
    'modules_namespaced_views' => 'modules',

    'vendor_namespaced_views' => 'vendor',

    /*
    |--------------------------------------------------------------------------
    | Set behavior if an asset is not found in a Theme hierarchy.
    | Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
    |--------------------------------------------------------------------------
    */

    'asset_not_found' => (env('APP_ENV') == 'local') ? 'THROW_EXCEPTION' : 'LOG_ERROR',

    /*
    |--------------------------------------------------------------------------
    | Do we want a theme activated by default? Can be set at runtime with:
    | Theme::set('theme-name');
    |--------------------------------------------------------------------------
    */

    'default' => null,

    /*
    |--------------------------------------------------------------------------
    | Cache theme.json configuration files that are located in each theme's folder
    | in order to avoid searching theme settings in the filesystem for each request
    |--------------------------------------------------------------------------
    */

    'cache' => false,
    'hooksCacheKey' => 'themes.hooks-methods',
    /**
     * Back end theme
     */
    'adminTheme' => 'Admin',
    /**
     * Front end theme
     */
    'frontTheme' => 'Front',
];
