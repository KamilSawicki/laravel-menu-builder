<?php


namespace KamilSawicki\LaravelMenuBuilder\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelMenuBuilderProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->callAfterResolving('blade.compiler', function ($blade) {
            $blade->directive('renderMenu', function(string $menuClass) {
                return (new $menuClass())->render();
            });
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'lmb');
    }
}
