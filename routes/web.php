<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Welcome Page
Route::view('/', 'welcome');

// --- Student CRUD Management System ---

// 1. Create
Route::get('add', [UserController::class, 'create'])->name('students.create');
Route::post('add', [UserController::class, 'add'])->name('students.store');

// 2. Read (Unified Dynamic List & Search Pipeline)
Route::get('list', [UserController::class, 'search'])->name('students.search');
// Add this if you want to access the list via the root or a cleaner URL
Route::get('search', [UserController::class, 'search']); 
Route::get('search/ajax', [UserController::class, 'ajaxSearch'])->name('students.search.ajax');

// 3. Update
Route::get('edit/{id}', [UserController::class, 'edit'])->name('students.edit');
Route::post('edit/{id}', [UserController::class, 'editStudent'])->name('students.update');

// 4. Delete (Updated to POST to work with standard HTML forms)
Route::post('delete/{id}', [UserController::class, 'delete'])->name('students.delete');

// 5. Utilities (Import/Export)
Route::post('import', [UserController::class, 'import'])->name('students.import');
Route::get('export', [UserController::class, 'export'])->name('students.export');