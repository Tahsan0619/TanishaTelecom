<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum(['orders as orders_total' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'total')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user): View
    {
        abort_if($user->is_admin, 404);

        $orders = $user->orders()->withCount('items')->latest()->get();

        return view('admin.customers.show', compact('user', 'orders'));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        $user->tokens()->delete();
        $user->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted. Their orders were kept.');
    }
}
