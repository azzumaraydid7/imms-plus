<?php

namespace App\Providers;

use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Window;
use Native\Desktop\Support\Environment;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $window = Window::open()
            ->id('main')
            ->title(config('app.name', 'IMMS'))
            ->width(1440)
            ->height(920)
            ->minWidth(1080)
            ->minHeight(720)
            ->hideMenu()
            ->rememberState();

        // macOS keeps its native traffic lights and we inset them into our own
        // title bar. Everywhere else there is no OS chrome left to keep, so the
        // window goes frameless and the deck draws its own controls.
        if (Environment::isMac()) {
            $window->titleBarHiddenInset()->trafficLightPosition(20, 18);
        } else {
            $window->frameless();
        }
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
