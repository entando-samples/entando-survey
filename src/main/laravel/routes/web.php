<?php

Route::get('/',function(){
  dd(\Illuminate\Support\Facades\DB::getPdo());
});