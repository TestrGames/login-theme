<?php

namespace Lisak\LoginTheme\Providers;

use App\Filament\Pages\Auth\Login;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class LoginThemePluginProvider extends ServiceProvider
{
    public function boot(): void
    {
        // App\Filament\Pages\Auth\Login is one shared class rendered for all
        // three panels (admin/app/server) -- Pelican never overrides ->login()
        // per panel, so scoping to it here restyles every login screen at once.
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn () => Blade::render(<<<'HTML'
            <style>
                :root {
                    --lt-accent: {{ $accentColor }};
                }

                .fi-simple-layout {
                    min-height: 100vh;
                    @if ($backgroundImageUrl)
                        background-image: url('{{ $backgroundImageUrl }}');
                        background-size: cover;
                        background-position: center;
                    @else
                        background:
                            radial-gradient(circle at 15% 20%, color-mix(in srgb, var(--lt-accent) 35%, transparent), transparent 45%),
                            radial-gradient(circle at 85% 80%, color-mix(in srgb, var(--lt-accent) 22%, transparent), transparent 45%),
                            linear-gradient(135deg, #0a0e1a 0%, #131829 55%, #0a0e1a 100%);
                        background-size: 160% 160%, 160% 160%, 100% 100%;
                        animation: lt-drift 22s ease-in-out infinite alternate;
                    @endif
                }

                @keyframes lt-drift {
                    0%   { background-position: 0% 0%, 100% 100%, 0% 0%; }
                    100% { background-position: 30% 40%, 70% 60%, 0% 0%; }
                }

                .fi-simple-page {
                    overflow: hidden;
                    background: rgba(255, 255, 255, 0.06) !important;
                    backdrop-filter: blur(24px) saturate(160%);
                    -webkit-backdrop-filter: blur(24px) saturate(160%);
                    border: 1px solid rgba(255, 255, 255, 0.12);
                    border-radius: 1.25rem;
                    box-shadow:
                        0 8px 40px rgba(0, 0, 0, 0.5),
                        0 0 0 1px rgba(255, 255, 255, 0.03) inset;
                }

                /* Filament gives this inner wrapper its own opaque background,
                   which otherwise pokes out past .fi-simple-page's rounded
                   corners and reads as a second, mismatched box. */
                .fi-simple-page-content {
                    background: transparent !important;
                }

                .fi-simple-header-heading {
                    background: linear-gradient(135deg, #ffffff, var(--lt-accent));
                    -webkit-background-clip: text;
                    background-clip: text;
                    -webkit-text-fill-color: transparent;
                }

                .fi-simple-page input {
                    background: rgba(255, 255, 255, 0.05) !important;
                }

                .fi-simple-page button[type="submit"] {
                    background: var(--lt-accent) !important;
                }
            </style>
            HTML, [
                'accentColor' => config('login-theme.accent_color', '#6366f1'),
                'backgroundImageUrl' => config('login-theme.background_image_url'),
            ]),
            scopes: Login::class,
        );
    }
}
