<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Shop Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('orderItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, Forms\Set $set, $context) =>
                                        $context === 'create' ? $set('price', \App\Models\Product::find($state)?->price ?? 0) : null
                                    ),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, Forms\Set $set) {
                                        $items = collect($get('../../orderItems'))
                                            ->map(fn ($item) => $item['quantity'] * ($item['price'] ?? 0))
                                            ->sum();
                                        $discount = $get('../../discount') ?? 0;
                                        $total = $items - $discount;
                                        $set('../../total', $total);
                                        
                                        $mnt_recu = $get('../../mnt_recu') ?? 0;
                                        $set('../../mnt_rendu', max(0, $mnt_recu - $total));
                                    }),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('DH')
                                    ->disabled()
                                    ->reactive(),
                            ])
                            ->columns(3)
                    ])->columnSpanFull(),
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Forms\Components\TextInput::make('total')
                            ->required()
                            ->numeric()
                            ->prefix('DH')
                            ->disabled(),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->default(0)
                            ->prefix('DH')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, Forms\Set $set) {
                                $items = collect($get('../orderItems'))
                                    ->map(fn ($item) => $item['quantity'] * ($item['price'] ?? 0))
                                    ->sum();
                                $total = $items - ($state ?? 0);
                                $set('../total', $total);
                                
                                $mnt_recu = $get('../mnt_recu') ?? 0;
                                $set('../mnt_rendu', max(0, $mnt_recu - $total));
                            }),
                        Forms\Components\TextInput::make('mnt_recu')
                            ->required()
                            ->numeric()
                            ->prefix('DH')
                            ->label('Montant Reçu')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, Forms\Set $set) {
                                $total = $get('../total') ?? 0;
                                $set('../mnt_rendu', max(0, ($state ?? 0) - $total));
                            }),
                        Forms\Components\TextInput::make('mnt_rendu')
                            ->numeric()
                            ->prefix('DH')
                            ->disabled()
                            ->label('Montant Rendu'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'complete' => 'Complete',
                                'cancel' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('total')
                    ->money('mad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->money('mad')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mnt_recu')
                    ->money('mad')
                    ->label('Montant Reçu')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mnt_rendu')
                    ->money('mad')
                    ->label('Montant Rendu')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'complete',
                        'danger' => 'cancel',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'complete' => 'Complete',
                        'cancel' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DateTimePicker::make('created_from'),
                        Forms\Components\DateTimePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                 
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                 
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                 
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
