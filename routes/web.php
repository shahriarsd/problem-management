<?php

use App\Http\Controllers\ProblemController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/problems');

Route::resource('problems', ProblemController::class);
