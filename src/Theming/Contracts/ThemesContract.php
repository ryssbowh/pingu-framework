<?php

namespace Pingu\Theming\Contracts; 

interface ThemesContract
{
    /**
     * Return $filename path located in themes folder
     *
     * @param string $filename
     * 
     * @return string
     */
    public function themes_path($filename = null): string;

    /**
     * Return list of registered themes
     *
     * @return array
     */
    public function all(): array;

    /**
     * Check if @themeName is registered
     *
     * @return bool
     */
    public function exists(string $themeName): bool;

    /**
     * set a theme given a request
     *
     * @param Illuminate\Http\Request $request
     * 
     * @return ?Theme
     */
    public function setByRequest(Request $request);

    /**
     * Set theme by its name
     *
     * @param string $themeName
     * @param bool $setAssets
     * 
     * @return Theme
     */
    public function setByName(?string $themeName, bool $setAssets = true);

    /**
     * Get current theme
     *
     * @return Theme
     */
    public function current(): ?ThemeContract;

    /**
     * Find a theme by it's name
     *
     * @return ThemeContract
     */
    public function find(string $themeName): ThemeContract;

    /**
     * Register a new theme
     *
     * @return ThemeContract
     */
    public function add(ThemeContract $theme): ThemeContract;

    /**
     * Get current theme views path
     * 
     * @return array
     */
    public function getViewPaths(): array;

    /**
     * Is the cache enabled for themes
     * 
     * @return bool
     */
    public function cacheEnabled(): bool;

    /**
     * Rebuidls theme cache
     */
    public function rebuildCache();

    /**
     * Scan all folders inside the themes path & config/themes.php
     * If a "theme.json" file is found then load it and setup theme
     */
    public function scanThemes();

    /*
     * Return url of current theme for a filename
     */
    public function url(string $filename): string;
}