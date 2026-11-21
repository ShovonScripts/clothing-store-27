<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Services\SettingsService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    protected SettingsService $settingsService;

    public function mount(SettingsService $settingsService): void
    {
        $this->settingsService = $settingsService;
        $this->form->fill($this->settingsService->getAll());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Store Information'))
                    ->schema([
                        TextInput::make('store_name')
                            ->label(__('Store Name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('store_phone')
                            ->label(__('Store Phone'))
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('whatsapp_number')
                            ->label(__('WhatsApp Number'))
                            ->tel()
                            ->maxLength(20)
                            ->helperText(__('Format: 8801XXXXXXXXX')),
                        TextInput::make('facebook_url')
                            ->label(__('Facebook URL'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('instagram_url')
                            ->label(__('Instagram URL'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('tiktok_url')
                            ->label(__('TikTok URL'))
                            ->url()
                            ->maxLength(255),
                        TextInput::make('messenger_url')
                            ->label(__('Messenger URL'))
                            ->url()
                            ->maxLength(255),
                        Textarea::make('store_address')
                            ->label(__('Store Address'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('Announcement Bar'))
                    ->schema([
                        Toggle::make('announcement_enabled')
                            ->label(__('Enabled')),
                        TextInput::make('announcement_text')
                            ->label(__('Announcement Text'))
                            ->maxLength(500)
                            ->placeholder(__('Enter announcement message')),
                    ])->columns(2),

                Section::make(__('Payments'))
                    ->schema([
                        TextInput::make('bkash_number')
                            ->label(__('bKash Number'))
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->helperText(__('Personal bKash number to receive payments')),
                        Textarea::make('bkash_instructions_bn')
                            ->label(__('bKash Instructions (Bengali)'))
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('bkash_instructions_en')
                            ->label(__('bKash Instructions (English)'))
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('bkash_verify_hours')
                            ->label(__('Verification Hours'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(24)
                            ->helperText(__('Hours after which unverified bKash orders auto-expire')),
                        Toggle::make('cod_enabled')
                            ->label(__('Enable Cash on Delivery'))
                            ->default(true),
                        Toggle::make('bkash_enabled')
                            ->label(__('Enable bKash Send Money'))
                            ->default(true),
                    ])->columns(2),

                Section::make(__('Delivery'))
                    ->schema([
                        TextInput::make('delivery_inside_dhaka')
                            ->label(__('Inside Dhaka (৳)'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(70),
                        TextInput::make('delivery_outside_dhaka')
                            ->label(__('Outside Dhaka (৳)'))
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(130),
                        TextInput::make('free_delivery_over')
                            ->label(__('Free Delivery Over (৳)'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText(__('0 to disable')),
                    ])->columns(3),

                Section::make(__('Order Limits'))
                    ->schema([
                        TextInput::make('max_pending_orders_per_phone_per_day')
                            ->label(__('Max Pending Orders/Phone/Day'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(3),
                        TextInput::make('min_order_amount')
                            ->label(__('Minimum Order Amount (৳)'))
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ])->columns(2),

                Section::make(__('Inventory'))
                    ->schema([
                        TextInput::make('low_stock_threshold')
                            ->label(__('Low Stock Threshold'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(5),
                    ])->columns(1),

                Section::make(__('Maintenance'))
                    ->schema([
                        Toggle::make('maintenance_orders_paused')
                            ->label(__('Pause Order Intake'))
                            ->helperText(__('When enabled, customers cannot place new orders')),
                    ])->columns(1),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($key === 'settings_tabs') {
                continue;
            }

            $setting = Setting::where('key', $key)->first();
            $type = $setting?->type ?? 'string';

            $this->settingsService->set($key, $value, $type);
        }

        $this->settingsService->clearCache();

        Notification::make()
            ->title(__('Settings saved successfully'))
            ->success()
            ->send();
    }
}
