<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationLabel = 'Staff & Admins';
    protected static ?int    $navigationSort  = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Section::make('Profile Picture')
                            ->columnSpan(1)
                            ->schema([
                                Forms\Components\FileUpload::make('avatar_url')
                                    ->label('')
                                    ->image()
                                    ->avatar()
                                    ->directory('avatars')
                                    ->imageEditor(),
                            ]),

                        Forms\Components\Section::make('User Account Details')
                            ->description('Manage user credentials and access levels.')
                            ->columnSpan(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->autocomplete('none')
                                    ->extraAttributes(['autocomplete' => 'new-password']),

                                Forms\Components\TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->placeholder('09xxxxxxxxx'),

                                Forms\Components\Select::make('role')
                                    ->options([
                                        User::ROLE_ADMIN => 'Admin',
                                        User::ROLE_STAFF => 'Staff',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->autocomplete('new-password')
                                    ->required(fn (string $context): bool => $context === 'create'),

                                Forms\Components\Toggle::make('is_approve')
                                    ->label('Account Approved')
                                    ->default(false)
                                    ->onColor('success'),
                            ])->columns(2)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->label('User Information')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (User $record): string => $record->email),

                // Fixed: using TextColumn->badge() instead of deprecated BadgeColumn
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'danger',
                        User::ROLE_STAFF => 'warning',
                        default          => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN => 'heroicon-m-shield-check',
                        User::ROLE_STAFF => 'heroicon-m-user-group',
                        default          => 'heroicon-m-user',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->icon('heroicon-m-phone')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('is_approve')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        User::ROLE_ADMIN => 'Admin',
                        User::ROLE_STAFF => 'Staff',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approve')
                    ->label('Approval Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Users Found')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}