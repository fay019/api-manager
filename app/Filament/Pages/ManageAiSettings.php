<?php

namespace App\Filament\Pages;

use App\Models\AiSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;
use UnitEnum;

class ManageAiSettings extends Page
{
    use InteractsWithForms;

    protected static UnitEnum|string|null $navigationGroup = 'IA Manager';

    protected static ?string $navigationLabel = 'AI Configuration';

    protected static ?string $title = 'AI Configuration';

    protected string $view = 'filament.pages.manage-ai-settings';

    public ?array $data = [];

    public function getTitle(): string
    {
        return 'AI Configuration';
    }

    public function mount(): void
    {
        $settings = AiSetting::getInstance();

        $this->form->fill([
            'base_url' => $settings->base_url,
            'default_model' => $settings->default_model,
            'allowed_models' => implode(',', $settings->allowed_models ?? []),
            'timeout' => $settings->timeout,
            'is_active' => $settings->is_active,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ollama Configuration')
                    ->description('Configure your Ollama AI server connection')
                    ->schema([
                        TextInput::make('base_url')
                            ->label('Ollama Base URL')
                            ->url()
                            ->required()
                            ->placeholder('https://ia.fayotech.com'),

                        TextInput::make('default_model')
                            ->label('Default Model')
                            ->required()
                            ->placeholder('llama3.2:3b'),

                        TextInput::make('allowed_models')
                            ->label('Allowed Models (comma-separated)')
                            ->required()
                            ->placeholder('llama3.2:3b,llama2:7b')
                            ->helperText('Separate multiple models with commas'),

                        TextInput::make('timeout')
                            ->label('Timeout (seconds)')
                            ->numeric()
                            ->minValue(60)
                            ->maxValue(600)
                            ->required()
                            ->default(120),

                        Toggle::make('is_active')
                            ->label('Service Active')
                            ->default(true),
                    ]),

                Section::make()
                    ->schema([
                        Action::make('save')
                            ->label('Save Configuration')
                            ->action('save')
                            ->keyBindings(['mod+s']),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test Connection')
                ->action('testConnection'),

            Action::make('fetchModels')
                ->label('Fetch Models')
                ->action('fetchModels'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Parse allowed_models from comma-separated string to array
        $allowedModels = array_filter(array_map('trim', explode(',', $data['allowed_models'] ?? '')));

        $settings = AiSetting::getInstance();
        $settings->update([
            'base_url' => $data['base_url'],
            'default_model' => $data['default_model'],
            'allowed_models' => $allowedModels,
            'timeout' => $data['timeout'],
            'is_active' => $data['is_active'],
        ]);

        Notification::make()
            ->success()
            ->title('AI Configuration')
            ->body('Settings saved successfully.')
            ->send();
    }

    public function testConnection(): void
    {
        try {
            $data = $this->form->getState();
            $response = Http::timeout(10)->get($data['base_url'].'/api/tags');

            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('Connection Test')
                    ->body('Successfully connected to Ollama')
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Connection Test')
                    ->body('Ollama returned an error: '.$response->status())
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Connection Test')
                ->body('Failed to connect to Ollama: '.$e->getMessage())
                ->send();
        }
    }

    public function fetchModels(): void
    {
        try {
            $data = $this->form->getState();
            $response = Http::timeout(10)->get($data['base_url'].'/api/tags');

            if (! $response->successful()) {
                Notification::make()
                    ->danger()
                    ->title('Fetch Models')
                    ->body('Failed to fetch models from Ollama')
                    ->send();

                return;
            }

            $responseData = $response->json();
            $models = [];

            if (isset($responseData['models']) && is_array($responseData['models'])) {
                $models = array_map(fn ($m) => $m['name'] ?? null, $responseData['models']);
                $models = array_filter($models);
            }

            if (empty($models)) {
                Notification::make()
                    ->warning()
                    ->title('Fetch Models')
                    ->body('No models found on the Ollama server')
                    ->send();

                return;
            }

            $this->form->fill([
                'allowed_models' => implode(',', $models),
            ]);

            Notification::make()
                ->success()
                ->title('Fetch Models')
                ->body(count($models).' models loaded from Ollama. Click Save to apply.')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Fetch Models')
                ->body('Error fetching models: '.$e->getMessage())
                ->send();
        }
    }
}
