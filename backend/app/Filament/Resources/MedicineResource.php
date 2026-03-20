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

                                // 2. Generate Clean WebP Filename
                                $baseName = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                                $filename = $baseName . '.webp';
                                $tempPath = tempnam(sys_get_temp_dir(), 'webp');

                                // 3. Convert to WebP using Intervention Image
                                try {
                                    $manager = new ImageManager(new Driver());
                                    $image = $manager->read($file->getRealPath());
                                    
                                    // 4. Encode to WebP and save to temp
                                    $image->toWebp(80)->save($tempPath);
                                } catch (\Throwable $e) {
                                    // FALLBACK: If conversion fails, save original file with .webp extension (risky, but user wants it)
                                    // Actually, better to just save original as original if it fails.
                                    $ext = $file->getClientOriginalExtension();
                                    $safeName = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
                                    return Storage::disk('public')->putFileAs($dir, $file, $safeName);
                                }

                                // 5. Store in Public Disk
                                $storedPath = Storage::disk('public')->putFileAs($dir, new \Illuminate\Http\File($tempPath), $filename);
                                
                                // 6. Cleanup
                                @unlink($tempPath);

                                return $storedPath;
                            })
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('600')
                            ->maxSize(1024)
                            // ->imageEditor() // Allows cropping/rotating
                            ->preserveFilenames()
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
                    ->sortable(),

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
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter by Category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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