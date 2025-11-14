<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function revenue(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfYear()->format('Y-m-d'));

        $revenue = Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', '>=', $startDate)
            ->whereDate('paid_at', '<=', $endDate)
            ->select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $totalRevenue = $revenue->sum('revenue');
        $totalInvoices = $revenue->sum('count');

        return view('reports.revenue', compact('revenue', 'totalRevenue', 'totalInvoices', 'startDate', 'endDate'));
    }

    public function client(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfYear()->format('Y-m-d'));

        $clients = \App\Models\Client::where('user_id', $user->id)
            ->withSum(['invoices as total_revenue' => function ($query) use ($startDate, $endDate) {
                $query->where('status', 'paid')
                      ->whereDate('paid_at', '>=', $startDate)
                      ->whereDate('paid_at', '<=', $endDate);
            }], 'total')
            ->withCount(['invoices as invoice_count' => function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            }])
            ->having('total_revenue', '>', 0)
            ->orderByDesc('total_revenue')
            ->get();

        return view('reports.client', compact('clients', 'startDate', 'endDate'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'invoices');
        $format = $request->get('format', 'csv');

        $invoices = Invoice::where('user_id', $user->id)
            ->with('client')
            ->latest()
            ->get();

        if ($format === 'csv') {
            $filename = 'invoices_' . now()->format('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($invoices) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Invoice #', 'Client', 'Issue Date', 'Due Date', 'Status', 'Total']);

                foreach ($invoices as $invoice) {
                    fputcsv($file, [
                        $invoice->invoice_number,
                        $invoice->client->name,
                        $invoice->issue_date->format('Y-m-d'),
                        $invoice->due_date->format('Y-m-d'),
                        $invoice->status,
                        $invoice->total,
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Export format not supported.');
    }
}

