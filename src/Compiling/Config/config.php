<?php 

return [
    'onRuntime' => (env('APP_ENV') == 'local'),
    'folder' => storage_path('generated')
];