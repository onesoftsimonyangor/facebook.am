<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Route::group(['prefix' => 'oauth', 'middleware' => ['api']], function () {
                Route::post('/token', [
                    'uses' => 'Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
                    'as' => 'passport.token',
                ]);

                Route::get('/tokens', [
                    'uses' => 'Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@forUser',
                    'as' => 'passport.tokens.index',
                ]);

                Route::delete('/tokens/{token_id}', [
                    'uses' => 'Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController@destroy',
                    'as' => 'passport.tokens.destroy',
                ]);

                Route::post('/clients', [
                    'uses' => 'Laravel\Passport\Http\Controllers\ClientController@store',
                    'as' => 'passport.clients.store',
                ]);

                Route::get('/clients', [
                    'uses' => 'Laravel\Passport\Http\Controllers\ClientController@forUser',
                    'as' => 'passport.clients.index',
                ]);

                Route::put('/clients/{client_id}', [
                    'uses' => 'Laravel\Passport\Http\Controllers\ClientController@update',
                    'as' => 'passport.clients.update',
                ]);

                Route::delete('/clients/{client_id}', [
                    'uses' => 'Laravel\Passport\Http\Controllers\ClientController@destroy',
                    'as' => 'passport.clients.destroy',
                ]);

                Route::get('/scopes', [
                    'uses' => 'Laravel\Passport\Http\Controllers\ScopeController@all',
                    'as' => 'passport.scopes.index',
                ]);

                Route::get('/personal-access-tokens', [
                    'uses' => 'Laravel\Passport\Http\Controllers\PersonalAccessTokenController@forUser',
                    'as' => 'passport.personal.tokens.index',
                ]);

                Route::post('/personal-access-tokens', [
                    'uses' => 'Laravel\Passport\Http\Controllers\PersonalAccessTokenController@store',
                    'as' => 'passport.personal.tokens.store',
                ]);

                Route::delete('/personal-access-tokens/{token_id}', [
                    'uses' => 'Laravel\Passport\Http\Controllers\PersonalAccessTokenController@destroy',
                    'as' => 'passport.personal.tokens.destroy',
                ]);
            });
        }

        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
