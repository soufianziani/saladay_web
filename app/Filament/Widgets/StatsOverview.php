<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get completed orders with their items
        $completedOrders = Order::where('status', 'complete')
            ->with(['orderItems.product'])
            ->get();

        // Calculate total sales (revenue)
        $totalSales = $completedOrders->sum('total');

        // Calculate total earnings (profit)
        $totalEarnings = $completedOrders->sum(function ($order) {
            return $order->orderItems->sum(function ($item) {
                return ($item->product->earn ?? 0) * $item->quantity;
            });
        });

        // Calculate profit percentage
        $profitPercentage = $totalSales > 0 ? ($totalEarnings / $totalSales) * 100 : 0;

        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'complete')->count();
        $cancelledOrders = Order::where('status', 'cancel')->count();

        return [
            Stat::make('Total Sales', number_format($totalSales, 2) . ' MAD')
                ->description('Revenue from completed orders')
                ->color('primary'),
            
            Stat::make('Total Earnings', number_format($totalEarnings, 2) . ' MAD')
                ->description(number_format($profitPercentage, 1) . '% profit margin')
                ->color('success'),
            
            Stat::make('Total Products', $totalProducts)
                ->description('Available products')
                ->color('info'),

            Stat::make('Orders Status', '')
                ->description("Pending: $pendingOrders | Completed: $completedOrders | Cancelled: $cancelledOrders")
                ->color('warning'),
        ];
    }
}