<?php

function pingu_installed()
{
    return file_exists(storage_path('installed'));
}

function pingu_installed_time($format = 'd/m/Y')
{
    if (pingu_installed()) {
        $timestamp = (int)trim(file_get_contents(storage_path('installed')));
        return (new DateTime)->setTimestamp($timestamp)->format($format);
    }
    return '';
}