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
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Configurar views de autenticação usando componentes Livewire
        Fortify::loginView(function () {
            return app(\App\Livewire\Auth\Login::class);
        });

        Fortify::registerView(function () {
            return app(\App\Livewire\Auth\Register::class);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return app(\App\Livewire\Auth\ForgotPassword::class);
        });

        Fortify::resetPasswordView(function (Request $request) {
            return app(\App\Livewire\Auth\ResetPassword::class, ['token' => $request->route('token')]);
        });

        Fortify::verifyEmailView(function () {
            return app(\App\Livewire\Auth\VerifyEmail::class);
        });

        Fortify::confirmPasswordView(function () {
            return app(\App\Livewire\Auth\ConfirmPassword::class);
        });

        // Configurar views de 2FA
        Fortify::twoFactorChallengeView(function () {
            return app(\App\Livewire\Auth\TwoFactorChallenge::class);
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
