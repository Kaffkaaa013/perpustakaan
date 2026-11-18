<?php

namespace App\Providers;

use App\Models\Peminjaman;
use App\Models\User;
use App\Policies\PeminjamanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        // Force HTTPS di production (Vercel)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Soal 3: Gate kelola-buku (hanya admin dan petugas)
        Gate::define('kelola-buku', function (User $user) {
            return $user->hasRole(['admin', 'petugas']);
        });

        // Soal 4: Policy Peminjaman
        Gate::policy(Peminjaman::class, PeminjamanPolicy::class);
    }
}
