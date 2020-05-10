<?php

namespace Pingu\Theming\Contracts; 

interface ThemeContract
{
    /**
     * Get layouts defined by this theme
     * 
     * @return array
     */
    public function getLayouts(): array;

    /**
     * Get regions defined for a layout
     * 
     * @param string $layout
     * 
     * @return array
     */
    public function getRegions(string $layout): array;

    /**
     * Get the views path for this theme
     * 
     * @return array
     */
    public function getViewPaths(): array;

    /**
     * Get the public url for a file
     * 
     * @param string $url
     * 
     * @return string
     */
    public function url(string $url): string;

    /**
     * Get a file path within the theme
     * 
     * @param string $sub
     * 
     * @return string
     */
    public function getPath(string $sub = ''): string;

    /**
     * Get the parent theme
     *
     * @return ?ThemeContract
     */
    public function getParent(): ?ThemeContract;

    /**
     * Set the parent theme
     * 
     * @param ThemeContract $parent
     */
    public function setParent(Theme $parent): ThemeContract;

    /**
     * Install a memory theme on disk
     * 
     * @param boolean $clearPaths
     */
    public function install(bool $clearPaths = false);

    /**
     * Sets a setting
     * 
     * @param string $key
     * @param mixed $value
     */
    public function setSetting(string $key, $value);

    /**
     * Get a setting for a theme.
     * 
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    public function getSetting(string $key, $default = null);

    /**
     * Loads settings from an array
     * 
     * @param array  $settings
     */
    public function loadSettings(array $settings = []);
}