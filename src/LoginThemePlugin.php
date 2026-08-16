<?php

namespace Lisak\LoginTheme;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class LoginThemePlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'login-theme';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getSettingsFormData(): array
    {
        return config('login-theme');
    }

    /** @return \Filament\Schemas\Components\Component[] */
    public function getSettingsForm(): array
    {
        return [
            ColorPicker::make('accent_color')
                ->label('Accent color')
                ->default(fn () => config('login-theme.accent_color')),
            TextInput::make('background_image_url')
                ->label('Side panel background image URL')
                ->url()
                ->placeholder('https://example.com/your-background.jpg')
                ->helperText('Leave blank to use the built-in animated gradient instead.')
                ->default(fn () => config('login-theme.background_image_url')),
            TextInput::make('side_panel_heading')
                ->label('Side panel heading')
                ->default(fn () => config('login-theme.side_panel_heading')),
            TextInput::make('side_panel_tagline')
                ->label('Side panel tagline')
                ->default(fn () => config('login-theme.side_panel_tagline')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'LOGINTHEME_ACCENT_COLOR' => filled($data['accent_color'] ?? null) ? $data['accent_color'] : '#6366f1',
            'LOGINTHEME_BACKGROUND_IMAGE_URL' => $data['background_image_url'] ?? '',
            'LOGINTHEME_SIDE_PANEL_HEADING' => filled($data['side_panel_heading'] ?? null)
                ? $data['side_panel_heading']
                : 'Manage your servers with ease.',
            'LOGINTHEME_SIDE_PANEL_TAGLINE' => filled($data['side_panel_tagline'] ?? null)
                ? $data['side_panel_tagline']
                : 'Everything you need to run your game servers, in one place.',
        ]);

        Notification::make()
            ->title(trans('admin/setting.save_success'))
            ->success()
            ->send();
    }
}
