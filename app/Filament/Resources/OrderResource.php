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
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('DH')
                    ->disabled(),
                Forms\Components\TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->prefix('DH'),
                Forms\Components\TextInput::make('mnt_recu')
                    ->required()
                    ->numeric()
                    ->prefix('DH')
                    ->label('Montant Reçu'),
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
