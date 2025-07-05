<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// ==================== AUTH ROUTES ====================
Route::prefix('auth')->group(function () {
    // Public auth routes
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // Protected auth routes (requires Sanctum authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
    });
});

// ==================== CATEGORY ROUTES ====================
// Public category routes
Route::get('/categories', [CategoryController::class, 'index']);

// Admin-only category routes (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
});

// ==================== POST ROUTES ====================
// Protected post routes (requires authentication) - MUST BE BEFORE PUBLIC DYNAMIC ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('posts')->group(function () {
        Route::get('/all', [PostController::class, 'getAllForAdmin']);      // Admin endpoint
        Route::get('/my-posts', [PostController::class, 'getMyPosts']);     // Writer endpoint
        Route::post('/', [PostController::class, 'store']);
        Route::get('/user/{userId}', [PostController::class, 'getByUserIdWithPagination']);
        Route::patch('/{id}/toggle-draft-published', [PostController::class, 'toggleDraftPublished']);

        // Note: Using POST instead of PUT because sending a file (coverImage) with PUT caused errors.
        // POST works correctly with multipart/form-data, so I switched to avoid these issues.
        Route::post('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
});

// Public post routes - SPECIFIC ROUTES BEFORE DYNAMIC ROUTES
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
Route::get('/posts/{id}', [PostController::class, 'show']);

// ==================== COMMENT ROUTES ====================
Route::prefix('comments')->group(function () {
    // Public comment routes
    Route::get('/post/{postId}', [CommentController::class, 'getPostComments'])->name('comments.post');

    // Protected comment routes (requires authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('comments.index');
        Route::post('/', [CommentController::class, 'store'])->name('comments.store');
        Route::put('/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // Admin-only comment routes
        Route::patch('/{commentId}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::patch('/{commentId}/reject', [CommentController::class, 'reject'])->name('comments.reject');
    });
}); 