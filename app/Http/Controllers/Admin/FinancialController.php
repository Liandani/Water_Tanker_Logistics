<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FinancialTransaction;
use App\Models\TariffSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['order.customer', 'creator'])->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $totalIncome = (float) FinancialTransaction::where('type', 'income')->sum('amount');
        $totalExpense = (float) FinancialTransaction::where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $transactions = $query->paginate(15)->withQueryString();

        return view('admin.financial.index', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netProfit'
        ));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1000'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:500'],
            'receipt_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('receipt_photo')) {
            $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
        }

        $expense = FinancialTransaction::create([
            'type' => 'expense',
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'],
            'receipt_photo' => $photoPath,
            'created_by' => Auth::id(),
        ]);

        ActivityLog::log('EXPENSE_CREATED', "Mencatat pengeluaran {$expense->category} sebesar Rp " . number_format($expense->amount, 0, ',', '.'));

        return back()->with('success', 'Data pengeluaran berhasil dicatat.');
    }

    public function destroyExpense(FinancialTransaction $transaction)
    {
        if ($transaction->type !== 'expense') {
            return back()->with('error', 'Pemasukan otomatis pesanan tidak dapat dihapus manual.');
        }

        $amount = number_format($transaction->amount, 0, ',', '.');
        $cat = $transaction->category;
        $transaction->delete();

        ActivityLog::log('EXPENSE_DELETED', "Menghapus pengeluaran {$cat} (Rp {$amount}).");

        return back()->with('success', 'Data pengeluaran berhasil dihapus.');
    }

    public function reports(Request $request)
    {
        $filterType = $request->input('period', 'monthly'); // daily, monthly, custom
        $today = Carbon::today();

        $query = FinancialTransaction::with(['order.customer'])->latest('transaction_date');

        if ($filterType === 'daily') {
            $date = $request->input('date', $today->toDateString());
            $query->whereDate('transaction_date', $date);
            $reportTitle = "Laporan Keuangan Harian - " . Carbon::parse($date)->translatedFormat('d F Y');
            $startDate = $date;
            $endDate = $date;
        } elseif ($filterType === 'monthly') {
            $month = $request->input('month', $today->format('Y-m'));
            $cMonth = Carbon::parse($month . '-01');
            $startDate = $cMonth->copy()->startOfMonth()->toDateString();
            $endDate = $cMonth->copy()->endOfMonth()->toDateString();
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
            $reportTitle = "Laporan Keuangan Bulanan - " . $cMonth->translatedFormat('F Y');
        } else {
            $startDate = $request->input('start_date', $today->copy()->subDays(30)->toDateString());
            $endDate = $request->input('end_date', $today->toDateString());
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
            $reportTitle = "Laporan Keuangan Periode " . Carbon::parse($startDate)->format('d/m/Y') . " s/d " . Carbon::parse($endDate)->format('d/m/Y');
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        return view('admin.financial.reports', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'filterType',
            'startDate',
            'endDate',
            'reportTitle'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filterType = $request->input('period', 'monthly');
        $today = Carbon::today();

        $query = FinancialTransaction::with(['order.customer'])->orderBy('transaction_date', 'asc');

        if ($filterType === 'daily') {
            $date = $request->input('date', $today->toDateString());
            $query->whereDate('transaction_date', $date);
            $reportTitle = "Laporan Keuangan Harian - " . Carbon::parse($date)->translatedFormat('d F Y');
        } elseif ($filterType === 'monthly') {
            $month = $request->input('month', $today->format('Y-m'));
            $cMonth = Carbon::parse($month . '-01');
            $query->whereBetween('transaction_date', [$cMonth->copy()->startOfMonth(), $cMonth->copy()->endOfMonth()]);
            $reportTitle = "Laporan Keuangan Bulanan - " . $cMonth->translatedFormat('F Y');
        } else {
            $startDate = $request->input('start_date', $today->copy()->subDays(30)->toDateString());
            $endDate = $request->input('end_date', $today->toDateString());
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
            $reportTitle = "Laporan Keuangan Periode " . Carbon::parse($startDate)->format('d/m/Y') . " s/d " . Carbon::parse($endDate)->format('d/m/Y');
        }

        $transactions = $query->get();
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpense;
        $tariff = TariffSetting::current();

        $pdf = Pdf::loadView('admin.financial.pdf', compact(
            'transactions',
            'totalIncome',
            'totalExpense',
            'netProfit',
            'reportTitle',
            'tariff'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Keuangan-' . date('Ymd-His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filterType = $request->input('period', 'monthly');
        $today = Carbon::today();

        $query = FinancialTransaction::with(['order.customer'])->orderBy('transaction_date', 'asc');

        if ($filterType === 'daily') {
            $date = $request->input('date', $today->toDateString());
            $query->whereDate('transaction_date', $date);
        } elseif ($filterType === 'monthly') {
            $month = $request->input('month', $today->format('Y-m'));
            $cMonth = Carbon::parse($month . '-01');
            $query->whereBetween('transaction_date', [$cMonth->copy()->startOfMonth(), $cMonth->copy()->endOfMonth()]);
        } else {
            $startDate = $request->input('start_date', $today->copy()->subDays(30)->toDateString());
            $endDate = $request->input('end_date', $today->toDateString());
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        $transactions = $query->get();

        $filename = "Laporan-Keuangan-" . date('Ymd-His') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Tanggal', 'Tipe', 'Kategori', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Keterangan', 'Order Ref'];

        $callback = function () use ($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $totalIncome = 0;
            $totalExpense = 0;

            foreach ($transactions as $t) {
                $income = $t->type === 'income' ? $t->amount : 0;
                $expense = $t->type === 'expense' ? $t->amount : 0;
                $totalIncome += $income;
                $totalExpense += $expense;

                fputcsv($file, [
                    $t->id,
                    $t->transaction_date->format('d/m/Y'),
                    ucfirst($t->type),
                    $t->category,
                    $income,
                    $expense,
                    $t->description,
                    $t->order ? $t->order->order_number : '-'
                ]);
            }

            fputcsv($file, ['', '', '', 'TOTAL', $totalIncome, $totalExpense, '', '']);
            fputcsv($file, ['', '', '', 'LABA BERSIH', $totalIncome - $totalExpense, '', '', '']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

