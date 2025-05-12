<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TopicController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserPostController;
use App\Http\Controllers\API\AdminPostController;
use App\Http\Controllers\API\SavedPostController;


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::get('/topics', [TopicController::class, 'index']);
Route::get('/users', action: [UserController::class, 'listAll']);
Route::get('/users/{role?}', [UserController::class, 'getUsersByRole']);
Route::get('/posts/approved', [UserPostController::class, 'approvedPosts']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |----------------------------------------------------------------------
    | User Routes (Authenticated)
    |----------------------------------------------------------------------
    */

    // Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/user/edit-profile', [UserController::class, 'editProfile']);


    // Feedback
    Route::post('/feedback', [FeedbackController::class, 'store']);

    // User Posts
    Route::post('/posts/create', [UserPostController::class, 'create']);
    Route::get('/posts/my', [UserPostController::class, 'myPosts']);
    Route::get('/posts/my/{id}', [UserPostController::class, 'view']);
    Route::post('/posts/edit/{Post_Id}', [UserPostController::class, 'edit']);
    Route::delete('/posts/delete/{Post_Id}', [UserPostController::class, 'delete']);

    // Saved Post
    Route::post('/posts/save/{postId}', [SavedPostController::class, 'save']);
    Route::delete('/posts/unsave/{postId}', [SavedPostController::class, 'unsave']);
    Route::get('/posts/saved', [SavedPostController::class, 'savedPosts']);


    /*
    |----------------------------------------------------------------------
    | Admin Routes (Authenticated, Role-Checked in Controllers)
    |----------------------------------------------------------------------
    */    Route::post('/topics', action: [TopicController::class, 'store']);



    // Feedback (admin access only)
    Route::get('/feedback', [FeedbackController::class, 'index']);

    // Admin Post Management
    Route::prefix('admin/posts')->group(function () {
        Route::post('/create', [AdminPostController::class, 'create']);
        Route::post('/approve/{Post_Id}', [AdminPostController::class, 'approve']);
        Route::delete('/delete/{Post_Id}', [AdminPostController::class, 'delete']);
        Route::get('/approved', [AdminPostController::class, 'approved']);
        Route::get('/pending', [AdminPostController::class, 'pending']);
        Route::get('/all', [AdminPostController::class, 'all']);
        Route::get('/hcountsave', [AdminPostController::class, 'topSavedPost']);
    Route::get('/topic/{Topic_Id}', [AdminPostController::class, 'filterByTopic']);

    // Topic Management

    });

    // User Role Management (Admin)
    Route::post('/users/make-admin/{user_id}', [UserController::class, 'promoteToAdmin']);
    Route::post('/users/demote-user/{user_id}', [UserController::class, 'demoteToUser']);
});
