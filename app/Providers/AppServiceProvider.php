<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Request;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;


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
    public function boot()
    {
        // Listen for the BuildingMenu event
        $this->app['events']->listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Get the current URL path

            $event->menu->add([
                'header' => '',  // This creates a horizontal line separator
            ]);

            $currentPath = Request::path(); // Gets the current route path like 'subject' or 'subject/subject-detail'

            // Display 'Subject' only on the /subject route
            if (Request::is('subject')) {
                $event->menu->add([
                    'text' => 'Subject',
                    'route' => 'subject.index',
                    'icon'  => 'fas fa-user',
                ]);
            }

            // Display 'Subject Detail' only on the /subject-detail route
            if (Request::is('subject/subject-detail')) {
                $event->menu->add([
                    'text' => 'Subject Detail',
                    'route' => 'subject.detail',
                    'icon'  => 'fas fa-info-circle',
                ]);
            }
        });
    }
}
