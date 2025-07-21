<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CancerController;
use App\Http\Controllers\BeneficiariesController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\LgaController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\MinistryController;
use App\Http\Controllers\CadreController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\JAMBController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\HallController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Route::middleware(['cors'])->group(function () {
    // Public routes
    Route::post('/users/register', [AuthController::class, 'candidateRegister']);
    Route::post('/users/login', [AuthController::class, 'login']);
    Route::post('/users/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/users/profile', [AuthController::class, 'profile'])->middleware('auth.jwt'); // Use auth.jwt instead of auth:api

    Route::post('/verify-jamb', [JAMBController::class, 'verifyJAMB']);
    Route::get('/jamb', [JAMBController::class, 'index']);


    // Protected routes with JWT authentication
    Route::middleware(['auth.jwt'])->group(function () {
        Route::get('/user', function () {
            $user = auth()->user();
            return response()->json([
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'otherNames' => $user->otherNames,
                'email' => $user->email,
                'role' => $user->role,
                'id' => $user->id,
                'message' => 'User authenticated successfully',
            ]);
        });

        // Application routes
    Route::post('/jamb/upload', [JAMBController::class, 'upload']);
    Route::delete('/jamb/{jambId}', [JAMBController::class, 'destroy']);
    Route::get('/jamb/search', [JambController::class, 'search']);
       
    Route::post('/apply', [ApplicationController::class, 'apply']);
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/application/status/{email}', [ApplicationController::class, 'status']);
    Route::get('/my-slips', [ApplicationController::class, 'mySlips']);
    Route::get('/application/status', [ApplicationController::class, 'applicationStatus']);
    Route::put('/application/{applicationId}/change-batch', [ApplicationController::class, 'changeBatch']);
    Route::get('/attendance', [ApplicationController::class, 'attendance']);
    Route::post('/attendance/print', [ApplicationController::class, 'printAttendance'])->name('attendance.print');

    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->middleware('auth.jwt');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->middleware('auth.jwt');
    Route::get('/my-payments', [PaymentController::class, 'myPayments']);
    Route::get('/payments', [PaymentController::class, 'index']);
    
    Route::get('/all-batches', [BatchController::class, 'batches']);
    Route::get('/batches', [BatchController::class, 'index']);
    Route::post('/batches', [BatchController::class, 'store']);
    Route::put('/batches/{batchId}', [BatchController::class, 'update']);
    Route::delete('/batches/{batchId}', [BatchController::class, 'destroy']);
    Route::get('/rebatched', [ApplicationController::class, 'rebatched_candidates']);
    Route::patch('/batches/{batchId}/verification', [BatchController::class, 'changeStatus']);
    
     Route::get('/halls', [HallController::class, 'index']);
     Route::get('/all-halls', [HallController::class, 'halls']);
    Route::post('/halls', [HallController::class, 'store']);
    Route::put('/halls/{hallId}', [HallController::class, 'update']);
    Route::patch('/halls/{hallId}/status', [HallController::class, 'changeStatus']);
    Route::delete('/halls/{hallId}', [HallController::class, 'destroy']);

    Route::get('/batched-applicants', function () {
        return \App\Models\BatchAssignment::with('applicants')->latest()->paginate(20);
    });
    
    Route::get('/verify/slip', function () {
        return "This is authentic!";
    })->name('verify.slip');
    
    Route::get('/all-payments', [PaymentController::class, 'all_payments']);
    Route::get('/analytics', [ApplicationController::class, 'analytics']);
    
    Route::get('/applicant/verify/{identifier}', [VerificationController::class, 'verifyCandidate']);
    Route::post('/applicant/mark-present', [VerificationController::class, 'markPresent']);
});


Route::get('/application/slip/{applicationId}', [PDFController::class, 'generateExamSlip']);
        Route::get('analytics/total-users', [AnalyticsController::class, 'getTotalBeneficiaries']);

    Route::options('{any}', function () {
    return response()->json([], 200);
})->where('any', '.*');
