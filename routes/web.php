<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SocialConnectionController;
use App\Http\Controllers\SocialPostController;
use App\Http\Controllers\SocialProviderController;
use Illuminate\Support\Facades\Route;
Route::get('/login',[AuthController::class,'showLogin'])->name('login');
Route::post('/login',[AuthController::class,'login'])->name('login.store');
Route::get('/register',[AuthController::class,'showRegister'])->name('register');
Route::post('/register',[AuthController::class,'register'])->name('register.store');
Route::middleware('admin.session')->group(function(){
 Route::post('/logout',[AuthController::class,'logout'])->name('logout');
 Route::get('/',DashboardController::class)->name('dashboard');
 Route::resource('/products',ProductController::class)->except(['show']);
 Route::get('/connections',[SocialConnectionController::class,'index'])->name('connections.index');
 Route::get('/connections/{provider}/oauth',[SocialConnectionController::class,'oauth'])->name('social.oauth');
 Route::get('/connections/{provider}/callback',[SocialConnectionController::class,'callback'])->name('social.oauth.callback');
 Route::post('/connections/{connection}/toggle',[SocialConnectionController::class,'toggle'])->name('connections.toggle');
 Route::delete('/connections/{connection}',[SocialConnectionController::class,'destroy'])->name('connections.destroy');
 Route::get('/posts',[SocialPostController::class,'index'])->name('posts.index');
 Route::get('/posts/create',[SocialPostController::class,'create'])->name('posts.create');
 Route::post('/posts',[SocialPostController::class,'store'])->name('posts.store');
 Route::get('/posts/{post}',[SocialPostController::class,'show'])->name('posts.show');
 Route::post('/posts/{post}/publish',[SocialPostController::class,'publish'])->name('posts.publish');
 Route::get('/api/readiness',[SocialProviderController::class,'readiness'])->name('social.readiness');
});
