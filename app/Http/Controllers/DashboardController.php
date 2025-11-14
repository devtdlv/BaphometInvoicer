<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Revenue statistics
        $totalRevenue = Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('total');
        
        $monthlyRevenue = Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');
        
        $pendingAmount = Invoice::where('user_id', $user->id)
            ->whereIn('status', ['sent', 'overdue'])
            ->sum('total');
        
        $overdueAmount = Invoice::where('user_id', $user->id)
            ->where('status', 'overdue')
            ->sum('total');
        
        // Invoice statistics
        $totalInvoices = Invoice::where('user_id', $user->id)->count();
        $paidInvoices = Invoice::where('user_id', $user->id)->where('status', 'paid')->count();
        $sentInvoices = Invoice::where('user_id', $user->id)->where('status', 'sent')->count();
        $draftInvoices = Invoice::where('user_id', $user->id)->where('status', 'draft')->count();
        
        // Quote statistics
        $totalQuotes = Quote::where('user_id', $user->id)->count();
        $acceptedQuotes = Quote::where('user_id', $user->id)->where('status', 'accepted')->count();
        $pendingQuotes = Quote::where('user_id', $user->id)->where('status', 'sent')->count();
        
        // Client statistics
        $totalClients = Client::where('user_id', $user->id)->count();
        
        // Recent activity
        $recentInvoices = Invoice::where('user_id', $user->id)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();
        
        $recentQuotes = Quote::where('user_id', $user->id)
            ->with('client')
            ->latest()
            ->take(5)
            ->get();
        
        // Revenue chart data (last 6 months)
        $revenueChart = Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();
        
        // Overdue invoices
        $overdueInvoices = Invoice::where('user_id', $user->id)
            ->where('status', 'sent')
            ->where('due_date', '<', now())
            ->with('client')
            ->orderBy('due_date')
            ->take(5)
            ->get();
        
        // Top clients by revenue
        $topClients = Client::where('user_id', $user->id)
            ->withSum(['invoices as total_revenue' => function ($query) {
                $query->where('status', 'paid');
            }], 'total')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();
        
        return view('dashboard.index', compact(
            'totalRevenue',
            'monthlyRevenue',
            'pendingAmount',
            'overdueAmount',
            'totalInvoices',
            'paidInvoices',
            'sentInvoices',
            'draftInvoices',
            'totalQuotes',
            'acceptedQuotes',
            'pendingQuotes',
            'totalClients',
            'recentInvoices',
            'recentQuotes',
            'revenueChart',
            'overdueInvoices',
            'topClients'
        ));
    }
}

