<?php

use Illuminate\Support\Facades\Route;

// require __DIR__ . '/api/v1.php';
Route::get('/healthcheck', function () {
  return 'Hello World';
});
