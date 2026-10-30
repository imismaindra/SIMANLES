<?php

namespace App\Providers;

use App\Services\CabangContextService;
use Illuminate\Support\Facades\View;
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
        View::composer(['layouts.admin', 'admin.*'], function ($view) {
            $contextService = app(CabangContextService::class);
            $context = $contextService->getContext();

            $data = $view->getData();

            if (! array_key_exists('currentCabang', $data)) {
                $view->with('currentCabang', $context['currentCabang']);
            }
            if (! array_key_exists('daftarCabang', $data)) {
                $view->with('daftarCabang', $context['daftarCabang']);
            }
            if (! array_key_exists('viewRole', $data)) {
                $view->with('viewRole', $context['viewRole']);
            }
        });
    }
}
