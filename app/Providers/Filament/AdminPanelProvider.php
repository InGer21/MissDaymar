<?php

namespace App\Providers\Filament;

use App\Filament\Resources\SalesOrders\SalesOrderResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->passwordReset()
            ->darkMode(false)
            ->topNavigation()
            ->maxContentWidth(Width::Full)
            ->font('Instrument Sans')
            ->colors([
                'primary' => Color::hex('#004CB0'),
                'warning' => Color::hex('#E5EC00'),
            ])
            ->brandName('Miss Daymar')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.75rem')
            ->favicon(asset('favicon.png'))
            ->globalSearch()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                // Atajo directo al formulario de nuevo pedido: es la acción
                // más frecuente del módulo de Ventas, no debería requerir
                // entrar al listado primero.
                NavigationItem::make('Creación de Pedidos')
                    ->group('Ventas')
                    ->icon(Heroicon::OutlinedPlusCircle)
                    ->sort(2)
                    ->url(fn (): string => SalesOrderResource::getUrl('create'))
                    ->isActiveWhen(fn (): bool => request()->routeIs(SalesOrderResource::getRouteBaseName().'.create'))
                    ->visible(fn (): bool => SalesOrderResource::canCreate()),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
