<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $validOrders = Order::where('status', '!=', 'cancelled');

        $stats = [
            'revenue' => (clone $validOrders)->sum('total'),
            'today_revenue' => (clone $validOrders)->whereDate('created_at', today())->sum('total'),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'products' => Product::count(),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'customers' => User::where('is_admin', false)->count(),
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        $from = Carbon::today()->subDays(6);
        $daily = Order::where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as day, SUM(total) as total, COUNT(*) as count')
            ->groupBy('day')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->toDateString());

        $chart = collect(range(0, 6))->map(function ($i) use ($from, $daily) {
            $date = $from->copy()->addDays($i);
            $row = $daily->get($date->toDateString());

            return [
                'label' => $date->format('D'),
                'date' => $date->format('d M'),
                'total' => (float) ($row->total ?? 0),
                'count' => (int) ($row->count ?? 0),
            ];
        });
        $chartMax = max(1, $chart->max('total'));

        $statusCounts = Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'chartMax' => $chartMax,
            'statusCounts' => $statusCounts,
            'recentOrders' => Order::latest()->limit(8)->get(),
            'lowStock' => Product::where('stock', '<=', 5)->orderBy('stock')->limit(6)->get(),
            'topProducts' => Product::orderByDesc('sold_count')->limit(5)->get(),
        ]);
    }
}
