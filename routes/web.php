<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

    Route::prefix('tasks')->group(function () {
        Route::get('/{task}', 'App\Http\Controllers\TasksController@view')->name('view');
    });
