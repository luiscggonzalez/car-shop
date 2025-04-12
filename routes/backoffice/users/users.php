<?php

use App\Http\Controllers\Backoffice\Users\UserPostController;
use Illuminate\Support\Facades\Route;

Route::post('/users', UserPostController::class)
    ->name('users.create');
