<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group([
    'prefix' => 'backoffice/',
    'middleware' => [
        'auth:sanctum'
    ],
], function () {
    require __DIR__ . '/backoffice/users/users.php';
});
