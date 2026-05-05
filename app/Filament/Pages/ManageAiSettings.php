<?php

namespace App\Filament\Pages;

use App\Models\AiSetting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use UnitEnum;

class ManageAiSettings extends Page implements HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static UnitEnum|string|null $navigationGroup = 'IA Manager';

    protected static ?string $navigationLabel = 'AI Configuration';

    protected static ?string $title = 'AI Configuration';

    protected string $view = 'filament.pages.manage-ai-settings';

    public ?array $data = [];

    public ?bool $hasToken = null;

    public ?array $testResult = null;

    public ?string $testError = null;

    public bool $showModal = false;

    public function getTitle(): string
    {
        return 'AI Configuration';
    }

    public function mount(): void
    {
        $settings = AiSetting::getInstance();
        $this->hasToken = ! empty($settings->ia_token_hash);

        $decryptedToken = '';
        if ($this->hasToken) {
            try {
                $decryptedToken = Crypt::decryptString($settings->ia_token_hash);
            } catch (\Exception) {
                $decryptedToken = '';
            }
        }

        $this->form->fill([
            'base_url' => $settings->base_url,
            'default_model' => $settings->default_model,
            'allowed_models' => implode(',', $settings->allowed_models ?? []),
            'timeout' => $settings->timeout,
            'is_active' => $settings->is_active,
            'ia_token' => $decryptedToken,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.ia_settings.section_config'))
                    ->description(__('filament.ia_settings.section_config_desc'))
                    ->schema([
                        TextInput::make('base_url')
                            ->label(__('filament.ia_settings.base_url'))
                            ->url()
                            ->required()
                            ->placeholder(__('filament.ia_settings.base_url_placeholder')),

                        TextInput::make('default_model')
                            ->label(__('filament.ia_settings.default_model'))
                            ->required()
                            ->placeholder(__('filament.ia_settings.default_model_placeholder')),

                        TextInput::make('allowed_models')
                            ->label(__('filament.ia_settings.allowed_models'))
                            ->required()
                            ->placeholder(__('filament.ia_settings.allowed_models_placeholder'))
                            ->helperText(__('filament.ia_settings.allowed_models_help')),

                        TextInput::make('timeout')
                            ->label(__('filament.ia_settings.timeout'))
                            ->numeric()
                            ->minValue(60)
                            ->maxValue(600)
                            ->required()
                            ->default(120),

                        Toggle::make('is_active')
                            ->label(__('filament.ia_settings.is_active'))
                            ->default(true),
                    ]),

                Section::make(__('filament.ia_settings.section_token'))
                    ->description(__('filament.ia_settings.section_token_desc'))
                    ->schema([
                        TextInput::make('ia_token')
                            ->label(__('filament.ia_settings.ia_token'))
                            ->password()
                            ->revealable()
                            ->placeholder(__('filament.ia_settings.ia_token_placeholder'))
                            ->helperText(fn () => $this->hasToken ? __('filament.ia_settings.token_set') : __('filament.ia_settings.token_new')),
                    ]),

                Section::make()
                    ->schema([
                        Action::make('save')
                            ->label(__('filament.ia_settings.save'))
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
                ->label(__('filament.ia_settings.test_connection'))
                ->icon('heroicon-o-bolt')
                ->color('info')
                ->action('testConnection'),

            Action::make('fetchModels')
                ->label(__('filament.ia_settings.fetch_models'))
                ->action('fetchModels')
                ->icon('heroicon-o-arrow-path')
                ->color('success'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Parse allowed_models from comma-separated string to array
        $allowedModels = array_filter(array_map('trim', explode(',', $data['allowed_models'] ?? '')));

        $settings = AiSetting::getInstance();
        $updateData = [
            'base_url' => $data['base_url'],
            'default_model' => $data['default_model'],
            'allowed_models' => $allowedModels,
            'timeout' => $data['timeout'],
            'is_active' => $data['is_active'],
        ];

        // Only update token if provided
        if (! empty($data['ia_token'])) {
            $updateData['ia_token_hash'] = $data['ia_token'];
        }

        $settings->update($updateData);

        // Refresh token status display
        $settings->refresh();
        $this->hasToken = ! empty($settings->ia_token_hash);

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
            $baseUrl = rtrim($data['base_url'], '/');
            $model = $data['default_model'];

            $response = Http::withHeaders([
                'X-INTERNAL-AI-TOKEN' => config('ai.ollama.internal_token'),
            ])->timeout($data['timeout'] ?? 120)->post(
                $baseUrl.'/api/generate',
                [
                    'model' => $model,
                    'prompt' => 'Bonjour',
                    'stream' => false,
                ]
            );

            if ($response->successful()) {
                $responseData = $response->json();
                $this->testResult = [
                    'model' => $responseData['model'] ?? $model,
                    'response' => $responseData['response'] ?? '',
                    'duration_ms' => (int) (($responseData['total_duration'] ?? 0) / 1_000_000),
                    'prompt_eval_count' => $responseData['prompt_eval_count'] ?? 0,
                    'eval_count' => $responseData['eval_count'] ?? 0,
                ];
                $this->testError = null;
            } else {
                $this->testError = 'Ollama returned an error: '.$response->status();
                $this->testResult = null;
            }
        } catch (\Exception $e) {
            $this->testError = 'Failed to connect to Ollama: '.$e->getMessage();
            $this->testResult = null;
        }

        $this->showModal = true;
    }

    public function fetchModels(): void
    {
        try {
            $data = $this->form->getState();
            $response = Http::withHeaders([
                'X-INTERNAL-AI-TOKEN' => config('ai.ollama.internal_token'),
            ])->timeout(10)->get($data['base_url'].'/api/tags');

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
