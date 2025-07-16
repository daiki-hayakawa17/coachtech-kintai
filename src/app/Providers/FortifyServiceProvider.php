<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\Auth\CustomAuthenticatedSessionController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\Auth\LoginRequest as CustomLoginRequest;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\Auth\RegisterRequest as CustomRegisterRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(
            FortifyLoginRequest::class,
            CustomLoginRequest::class
        );

        $this->app->bind(
            FortifyRegisterRequest::class,
            CustomRegisterRequest::class
        );

        Fortify::createUsersUsing(CreateNewUser::class);
        
        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return request()->is('/admin/login') ? view('admin.login') : view('auth.login');
        });

        Route::middleware('web')->post('/register', [RegisterController::class, 'register']);
        Route::middleware('web')->post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
        Route::middleware('web')->post('/login', [CustomAuthenticatedSessionController::class, 'store']);
        Route::middleware('web')->post('/logout', [CustomAuthenticatedSessionController::class, 'destroy']);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
