<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});
Route::resource('task', TaskController::class);
// routes/web.php
Route::post('/tasks/update-status', [TaskController::class, 'updateStatus'])->name('task.updateStatus');
