<?php

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Mail\ContactReply;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // Set Filament locale based on message's language
        if ($this->record?->language) {
            app()->setLocale($this->record->language);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getReplyAction(),
            DeleteAction::make(),
        ];
    }

    private function getReplyAction(): Action
    {
        return Action::make('reply')
            ->label(__('filament.contact.reply_action'))
            ->icon('heroicon-m-arrow-left')
            ->color('info')
            ->form([
                Textarea::make('reply_message')
                    ->label(__('filament.contact.reply_field_label'))
                    ->required()
                    ->minLength(10)
                    ->maxLength(2000)
                    ->rows(6),

                Select::make('reply_language')
                    ->label(__('filament.contact.reply_language_label'))
                    ->options([
                        'fr' => __('filament.contact.lang_fr'),
                        'en' => __('filament.contact.lang_en'),
                        'de' => __('filament.contact.lang_de'),
                    ])
                    ->default($this->record->language ?? 'fr')
                    ->required(),
            ])
            ->action(function (array $data): void {
                try {
                    $this->record->update([
                        'reply_message' => $data['reply_message'],
                        'replied_at' => now(),
                        'replied_by' => auth()->user()->name ?? 'Admin',
                        'status' => 'replied',
                    ]);

                    Mail::send(new ContactReply(
                        $this->record,
                        $this->record->email,
                        $data['reply_language']
                    ));

                    Notification::make()
                        ->success()
                        ->title(__('filament.contact.reply_success_title'))
                        ->body(__('filament.contact.reply_success_body').' '.$this->record->email)
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title(__('filament.contact.reply_error_title'))
                        ->body(__('filament.contact.reply_error_body').' '.$e->getMessage())
                        ->send();
                }
            })
            ->after(fn () => $this->redirect(route('filament.admin.resources.contact-messages.index')))
            ->visible(fn () => is_null($this->record->replied_at));
    }
}
