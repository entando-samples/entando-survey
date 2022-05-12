<?php

use Illuminate\Support\Facades\Route;

Route::view('{query?}',  'welcome')
    ->where('query', '.*')
    ->name('login');
