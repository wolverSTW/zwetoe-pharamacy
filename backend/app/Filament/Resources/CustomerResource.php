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
    protected static ?string $navigationIcon  = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Customer::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending customer approvals';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
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
                                'pending'  => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->native(false)
                            ->live(),

                        Forms\Components\Textarea::make('reject_reason')
                            ->label('Reason for Rejection')
                            ->visible(fn ($get) => $get('status') === 'rejected')
                            ->required(fn ($get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Profile')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->image()
                            ->avatar()
                            ->directory('customers'),
                    ]),

                Forms\Components\Section::make('Delivery Address')
                    ->description('Customer shipping address details')
                    ->schema([
                        Forms\Components\TextInput::make('region')->placeholder('Region'),
                        Forms\Components\TextInput::make('township')->placeholder('Township'),
                        Forms\Components\TextInput::make('town')->placeholder('Town'),
                        Forms\Components\TextInput::make('street')->placeholder('Street'),
                        Forms\Components\TextInput::make('house_number')
                            ->placeholder('House Number')
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
                    ->size(40)
                    ->label(''),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): string => $record->email ?? ''),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending'  => 'heroicon-m-clock',
                        'approved' => 'heroicon-m-check-circle',
                        'rejected' => 'heroicon-m-x-circle',
                        default    => 'heroicon-m-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Spent')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state) . ' MMK' : '—')
                    ->color('success')
                    ->weight('semibold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_address')
                    ->label('Address')
                    ->getStateUsing(function ($record) {
                        $parts = array_filter([
                            $record->house_number, $record->street,
                            $record->township, $record->town, $record->region,
                        ]);
                        return count($parts) > 0 ? implode(', ', $parts) : '—';
                    })
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending Verification',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
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

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->required()
                            ->label('Reason for rejection'),
                    ])
                    ->action(function (Customer $record, array $data) {
                        $record->update([
                            'status'        => 'rejected',
                            'reject_reason' => $data['reject_reason'],
                        ]);
                        Notification::make()
                            ->title('Customer Rejected')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->emptyStateHeading('No Customers Yet')
            ->emptyStateIcon('heroicon-o-users');
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
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}