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
        //
        // SIMPLE_LAYOUT_START fires as the very first thing inside
        // <div class="fi-simple-layout">, immediately before .fi-simple-main-ctn
        // (verified against Filament 5.x's own
        // components/layout/simple.blade.php) -- so the markup we inject here
        // lands as a real DOM sibling *before* the form column, which is what
        // makes the flex split-screen layout below possible without touching
        // any Blade view.
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn () => Blade::render(<<<'HTML'
            <style>
                :root {
                    --lt-accent: {{ $accentColor }};
                }

                .fi-simple-layout {
                    display: flex;
                    min-height: 100vh;
                    background: #0b0e14;
                }

                .lt-side-panel {
                    flex: 1 1 50%;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    padding: 4rem;
                    position: relative;
                    overflow: hidden;
                    @if ($backgroundImageUrl)
                        background-image: url('{{ $backgroundImageUrl }}');
                        background-size: cover;
                        background-position: center;
                    @else
                        background:
                            radial-gradient(circle at 20% 20%, color-mix(in srgb, var(--lt-accent) 40%, transparent), transparent 50%),
                            radial-gradient(circle at 80% 80%, color-mix(in srgb, var(--lt-accent) 25%, transparent), transparent 50%),
                            linear-gradient(160deg, #11151f 0%, #1a1f2e 100%);
                        background-size: 160% 160%, 160% 160%, 100% 100%;
                        animation: lt-drift 22s ease-in-out infinite alternate;
                    @endif
                }

                @keyframes lt-drift {
                    0%   { background-position: 0% 0%, 100% 100%, 0% 0%; }
                    100% { background-position: 30% 40%, 70% 60%, 0% 0%; }
                }

                .lt-side-panel-eyebrow {
                    font-size: .8rem;
                    letter-spacing: .12em;
                    text-transform: uppercase;
                    color: var(--lt-accent);
                    font-weight: 700;
                    margin: 0 0 1rem;
                }

                .lt-side-panel-heading {
                    font-size: clamp(1.75rem, 3vw, 2.75rem);
                    font-weight: 700;
                    color: #ffffff;
                    line-height: 1.15;
                    max-width: 28rem;
                    margin: 0 0 1rem;
                }

                .lt-side-panel-tagline {
                    font-size: 1.05rem;
                    color: rgba(255, 255, 255, .65);
                    max-width: 26rem;
                    margin: 0;
                }

                .fi-simple-main-ctn {
                    flex: 1 1 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 2rem;
                }

                /* Deliberately flat, not "glass on top of a nested background" --
                   .fi-simple-page-content has its own opaque background baked
                   into Filament's CSS, which peeked out past rounded corners as
                   a visible second box. Making both fully transparent removes
                   the seam entirely instead of fighting it. */
                .fi-simple-page,
                .fi-simple-page-content {
                    background: transparent !important;
                    box-shadow: none !important;
                    border: none !important;
                }

                .fi-simple-page {
                    width: 100%;
                    max-width: 26rem;
                }

                .fi-simple-header-heading {
                    background: linear-gradient(135deg, #ffffff, var(--lt-accent));
                    -webkit-background-clip: text;
                    background-clip: text;
                    -webkit-text-fill-color: transparent;
                }

                .fi-simple-page button[type="submit"] {
                    background: var(--lt-accent) !important;
                }

                @media (max-width: 900px) {
                    .lt-side-panel {
                        display: none;
                    }
                }
            </style>

            <div class="lt-side-panel">
                <p class="lt-side-panel-eyebrow">Welcome</p>
                <h2 class="lt-side-panel-heading">{{ $sidePanelHeading }}</h2>
                <p class="lt-side-panel-tagline">{{ $sidePanelTagline }}</p>
            </div>
            HTML, [
                'accentColor' => config('login-theme.accent_color', '#6366f1'),
                'backgroundImageUrl' => config('login-theme.background_image_url'),
                'sidePanelHeading' => config('login-theme.side_panel_heading', 'Manage your servers with ease.'),
                'sidePanelTagline' => config('login-theme.side_panel_tagline', 'Everything you need to run your game servers, in one place.'),
            ]),
            scopes: Login::class,
        );
    }
}
