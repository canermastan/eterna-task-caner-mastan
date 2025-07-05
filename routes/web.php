<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Tüm API rotaları 'api' prefix'i altında gruplanmıştır
*/
Route::prefix('api')->group(function () {
    
    // ==================== AUTH ROUTES ====================
    Route::prefix('auth')->group(function () {
        // Public auth routes
        Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
        
        // Protected auth routes (requires authentication)
        Route::middleware('auth:web')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('/me', [AuthController::class, 'me'])->name('auth.me');
        });
    });

    // ==================== CATEGORY ROUTES ====================
    // Public category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    
    // Admin-only category routes (protected)
    Route::middleware('auth:web')->group(function () {
        Route::prefix('categories')->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });
    });

    // ==================== POST ROUTES ====================
    // Public post routes
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
    
    // Protected post routes (requires authentication)
    Route::middleware('auth:web')->group(function () {
        Route::prefix('posts')->group(function () {
            Route::get('/all', [PostController::class, 'getAllForAdmin']);      // Admin endpoint
            Route::get('/my-posts', [PostController::class, 'getMyPosts']);     // Writer endpoint
            Route::post('/', [PostController::class, 'store']);
            Route::patch('/{id}/toggle-draft-published', [PostController::class, 'toggleDraftPublished']);
            Route::post('/{id}', [PostController::class, 'update']);
            Route::delete('/{id}', [PostController::class, 'destroy']);
            Route::get('/user/{userId}', [PostController::class, 'getByUserIdWithPagination']);
        });
    });
    
    // Public post detail route (must be after specific routes)
    Route::get('/posts/{id}', [PostController::class, 'show']);

    // ==================== COMMENT ROUTES ====================
    Route::prefix('comments')->group(function () {
        // Public comment routes
        Route::get('/post/{postId}', [CommentController::class, 'getPostComments'])->name('comments.post');
    
        // Protected comment routes (requires authentication)
        Route::middleware('auth:web')->group(function () {
            Route::get('/', [CommentController::class, 'index'])->name('comments.index');
            Route::post('/', [CommentController::class, 'store'])->name('comments.store');
            Route::put('/{comment}', [CommentController::class, 'update'])->name('comments.update');
            Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
            
            // Admin-only comment routes
            Route::patch('/{commentId}/approve', [CommentController::class, 'approve'])->name('comments.approve');
            Route::patch('/{commentId}/reject', [CommentController::class, 'reject'])->name('comments.reject');
        });
    }); 
});

/*
|--------------------------------------------------------------------------
| WEB Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

// Catch-all route for Vue Router SPA
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');