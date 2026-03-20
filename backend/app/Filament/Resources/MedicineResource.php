<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static ?string $navigationIcon  = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Products Management';
    protected static ?string $navigationLabel = 'Medicines';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = Medicine::where('stock_quantity', '<=', 5)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Low stock medicines';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('General medicine information and classification.')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('generic_name')
                            ->label('Generic (Scientific) Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sku_code')
                            ->label('SKU / Medicine Code')
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Pricing & Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('buy_price')
                            ->numeric()
                            ->prefix('MMK')
                            ->required(),

                        Forms\Components\TextInput::make('sell_price')
                            ->label('Selling Price')
                            ->numeric()
                            ->prefix('MMK')
                            ->required(),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label('Available Stock')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\DatePicker::make('expiry_date')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Media & Status')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->saveUploadedFileUsing(function (UploadedFile $file, Get $get): string {
                                $categoryId = $get('category_id');
                                $dir = 'medicines';
                                if ($categoryId) {
                                    $category = \App\Models\Category::find($categoryId);
                                    if ($category && $category->slug) {
                                        $dir .= "/{$category->slug}";
                                    }
                                }

                                $baseName = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                                $filename = $baseName . '.webp';
                                $tempPath = tempnam(sys_get_temp_dir(), 'webp');

                                if (str_contains($file->getMimeType(), 'webp')) {
                                    return Storage::disk('public')->putFileAs($dir, $file, $filename);
                                }

                                try {
                                    $manager = new ImageManager(new Driver());
                                    $image = $manager->read($file->getRealPath());
                                    $image->cover(600, 600);
                                    $image->toWebp(80)->save($tempPath);
                                    $storedPath = Storage::disk('public')->putFileAs($dir, new \Illuminate\Http\File($tempPath), $filename);
                                    @unlink($tempPath);
                                    return $storedPath;
                                } catch (\Throwable $e) {
                                    $ext = $file->getClientOriginalExtension();
                                    $safeName = $baseName . '.' . $ext;
                                    return Storage::disk('public')->putFileAs($dir, $file, $safeName);
                                }
                            })
                            ->maxSize(2048)
                            ->previewable(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Available for sale')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->disk('public')
                    ->visibility('public')
                    ->circular()
                    ->size(40),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (Medicine $record): string => $record->generic_name ?? ''),

                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sell_price')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' MMK')
                    ->sortable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 0  => 'danger',
                        $state <= 5  => 'danger',
                        $state <= 15 => 'warning',
                        default      => 'success',
                    })
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? '⚠ Out' : $state . ' units')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->date('d-M-Y')
                    ->color(fn (?string $state): string => $state && \Carbon\Carbon::parse($state)->isPast()
                        ? 'danger'
                        : (\Carbon\Carbon::parse($state)->diffInDays(now()) < 90 ? 'warning' : 'gray'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name'),
                Filter::make('low_stock')
                    ->label('Critical / Low Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 10))
                    ->indicator('Low Stock'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Availability'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_availability')
                        ->label('Toggle Availability')
                        ->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each(fn ($record) => $record->update(['is_active' => !$record->is_active])))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('stock_quantity', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMedicines::route('/'),
            'create' => Pages\CreateMedicine::route('/create'),
            'edit'   => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}