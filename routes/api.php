<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DocumentController;

Route::post('/login', [EmployeeController::class, 'login']);
Route::post('/employees', [EmployeeController::class, 'addEmployee']);
Route::post('/get-employees', [EmployeeController::class, 'showEmployee']);
Route::post('/put-employees', [EmployeeController::class, 'updateEmployee']);
Route::post('/delete-employees', [EmployeeController::class, 'deleteEmployee']);
Route::get('/employees', [EmployeeController::class, 'index']);

Route::post('/documents', [DocumentController::class, 'addDocument']);
Route::post('/put-documents', [DocumentController::class, 'updateDocument']);
Route::post('/delete-documents', [DocumentController::class, 'deleteDocument']);
Route::get('/documents', [DocumentController::class, 'index']);
