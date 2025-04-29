<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.edit', $record))
                    ->icon('heroicon-m-eye'),
            ]);
    }
}