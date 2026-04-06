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
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Role-based redirect after successful login (regular or 2FA).
        // Binds a custom LoginResponse so Fortify calls our closure instead of
        // its default "redirect()->intended(config('fortify.home'))".
        $redirectBasedOnRole = function (Request $request) {
            $user = auth()->user();
            if ($user && $user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user && $user->isStaff()) {
                return redirect()->route('staff.dashboard');
            }
            return redirect()->route('webpage.home');
        };

        $this->app->instance(LoginResponseContract::class, new class ($redirectBasedOnRole) implements LoginResponseContract {
            public function __construct(private $redirect) {}
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : ($this->redirect)($request);
            }
        });

        $this->app->instance(TwoFactorLoginResponseContract::class, new class ($redirectBasedOnRole) implements TwoFactorLoginResponseContract {
            public function __construct(private $redirect) {}
            public function toResponse($request)
            {
                return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : ($this->redirect)($request);
            }
        });

        $this->app->instance(RegisterResponseContract::class, new class implements RegisterResponseContract {
            public function toResponse($request)
            {
                // After registration redirect customers to homepage
                return $request->wantsJson()
                    ? response()->json(['status' => 'created'])
                    : redirect()->route('webpage.home');
            }
        });
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

        // Point Fortify's login/register views to our existing custom Livewire auth pages.
        // These are GET redirects so the user never sees Jetstream's default auth UI.
        Fortify::loginView(fn () => redirect()->route('auth.login'));
        Fortify::registerView(fn () => redirect()->route('auth.register'));

        // Two-factor challenge uses the published + customised Meharahouse view.
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
