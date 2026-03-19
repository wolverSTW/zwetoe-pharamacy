<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    // protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                // Left Column: Basic Info
                Forms\Components\Section::make('Basic Information')
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->native(false)
                            ->live(), // Status ပြောင်းတာနဲ့ rejection field တန်းပေါ်အောင်လို့ပါ

                        Forms\Components\Textarea::make('reject_reason')
                            ->label('Reason for Rejection')
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->required(fn ($get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ])->columns(2),

                // Right Column: Profile Picture
                Forms\Components\Section::make('Profile')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->image()
                            ->avatar()
                            ->directory('customers'),
                    ]),

                // Bottom: Address Section
                Forms\Components\Section::make('Delivery Address')
                    ->description('ဝယ်သူ၏ ပစ္စည်းပို့ဆောင်ရန် လိပ်စာအချက်အလက်')
                    ->schema([
                        Forms\Components\TextInput::make('region')->placeholder('တိုင်းဒေသကြီး'),
                        Forms\Components\TextInput::make('township')->placeholder('မြို့နယ်'),
                        Forms\Components\TextInput::make('town')->placeholder('မြို့'),
                        Forms\Components\TextInput::make('street')->placeholder('လမ်းအမည်'),
                        Forms\Components\TextInput::make('house_number')
                            ->placeholder('အိမ်အမှတ်')
                            ->columnSpanFull(),
                    ])->columns(2)->collapsible(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->circular()
                    ->label('Photo'),
                
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                
                // Filament v3 uses TextColumn with badge()
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('full_address')
                    ->label('Address')
                    ->getStateUsing(function ($record) {
                        $addressParts = array_filter([
                            $record->house_number,
                            $record->street,
                            $record->township,
                            $record->town,
                            $record->region
                        ]);
                        return count($addressParts) > 0 ? implode(', ', $addressParts) : 'No address set';
                    })
                    ->limit(30),
            ])
            ->actions([
                // Quick Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Customer $record) {
                        $record->update(['status' => 'approved', 'reject_reason' => null]);
                        Notification::make()
                            ->title('Customer Approved Successfully')
                            ->success()
                            ->send();
                    }),

                // Quick Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->required()
                            ->label('Please provide a reason for rejection')
                    ])
                    ->action(function (Customer $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'reject_reason' => $data['reject_reason']
                        ]);
                        Notification::make()
                            ->title('Customer Rejected')
                            ->danger()
                            ->send();
                    }),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}