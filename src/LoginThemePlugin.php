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
                ->label('Background image URL')
                ->url()
                ->placeholder('https://example.com/your-background.jpg')
                ->helperText('Leave blank to use the built-in animated gradient instead.')
                ->default(fn () => config('login-theme.background_image_url')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'LOGINTHEME_ACCENT_COLOR' => filled($data['accent_color'] ?? null) ? $data['accent_color'] : '#6366f1',
            'LOGINTHEME_BACKGROUND_IMAGE_URL' => $data['background_image_url'] ?? '',
        ]);

        Notification::make()
            ->title(trans('admin/setting.save_success'))
            ->success()
            ->send();
    }
}
