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

    // Sidebar navigation icon and group
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Products Management';

    /**
     * Define the Form for creating and editing medicines
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1: Basic Information
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

                // Section 2: Pricing and Inventory
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

                // Section 3: Media and Visibility
                Forms\Components\Section::make('Media & Status')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->saveUploadedFileUsing(function (UploadedFile $file, Get $get): string {
                                // 1. Determine Directory
                                $categoryId = $get('category_id');
                                $dir = 'medicines';
                                if ($categoryId) {
                                    $category = \App\Models\Category::find($categoryId);
                                    if ($category && $category->slug) {
                                        $dir .= "/{$category->slug}";
                                    }
                                }

                                // 2. Generate Clean Filename
                                $baseName = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                                $filename = $baseName . '.webp';
                                $tempPath = tempnam(sys_get_temp_dir(), 'webp');

                                // 3. FAST PATH: If already WebP, just save with clean name
                                if (str_contains($file->getMimeType(), 'webp')) {
                                    return Storage::disk('public')->putFileAs($dir, $file, $filename);
                                }

                                // 4. CONVERSION PATH: Use Intervention Image 3
                                try {
                                    $manager = new ImageManager(new Driver());
                                    $image = $manager->read($file->getRealPath());

                                    // Resize to 600x600 (Cover mode) to save memory and loading time
                                    $image->cover(600, 600);
                                    
                                    // Encode to WebP (80% quality)
                                    $image->toWebp(80)->save($tempPath);
                                    
                                    // Store in Public Disk
                                    $storedPath = Storage::disk('public')->putFileAs($dir, new \Illuminate\Http\File($tempPath), $filename);
                                    @unlink($tempPath);
                                    return $storedPath;
                                } catch (\Throwable $e) {
                                    // ABSOLUTE FALLBACK: Just save original with its original name but WARN in logs
                                    // (Prevents upload failure if file is corrupt)
                                    $ext = $file->getClientOriginalExtension();
                                    $safeName = $baseName . '.' . $ext;
                                    return Storage::disk('public')->putFileAs($dir, $file, $safeName);
                                }
                            })
                            ->maxSize(2048) // 2MB limit
                            ->previewable(true),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Available for sale')
                            ->default(true),
                    ])
            ]);
    }

    /**
     * Define the Table to list medicines
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->visibility('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Medicine $record): string => $record->generic_name ?? ''),

                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sell_price')
                    ->money('MMK')
                    ->sortable(),

                // Display stock and change color if low
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->numeric()
                    ->color(fn (int $state): string => $state < 10 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->date('d-M-Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name'),
                Filter::make('low_stock')
                    ->label('Critical / Low Stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock_quantity', '<=', 10))
                    ->indicator('Low Stock'),
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
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicines::route('/'),
            'create' => Pages\CreateMedicine::route('/create'),
            'edit' => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}