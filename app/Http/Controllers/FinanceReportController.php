<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\FeePayment;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\AcademicYear;
use App\Models\LevelData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceReportController extends Controller
{
    /**
     * Generate finance report
     */
    public function financeReport(Request $request)
    {
        $query = FeePayment::with(['levelData.student', 'levelData.classModel', 'levelData.academicYear', 'fee.feeCategory']);

        // Apply filters
        if ($request->filled('class_id')) {
            $query->whereHas('levelData', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->whereHas('levelData', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->filled('fee_category_id')) {
            $query->whereHas('fee', function ($q) use ($request) {
                $q->where('fee_category_id', $request->fee_category_id);
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('levelData.student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'payment_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $payments = $query->paginate(20);

        // Calculate statistics
        $totalCollected = FeePayment::sum('amount_paid');
        $totalPayments = FeePayment::count();

        // Get filtered totals
        $filteredTotal = clone $query;
        $filteredTotal = $filteredTotal->sum('amount_paid');

        // Get payment methods
        $paymentMethods = FeePayment::distinct()->pluck('payment_method')->filter();

        // Get filter options
        $classes = ClassModel::all();
        $academicYears = AcademicYear::all();
        $feeCategories = FeeCategory::all();

        // Revenue breakdown by category
        $revenueByCategory = DB::table('fee_payments')
            ->join('fees', 'fee_payments.fee_id', '=', 'fees.id')
            ->join('fee_categories', 'fees.fee_category_id', '=', 'fee_categories.id')
            ->select('fee_categories.name', DB::raw('SUM(fee_payments.amount_paid) as total'))
            ->groupBy('fee_categories.id', 'fee_categories.name')
            ->get();

        // Revenue breakdown by payment method
        $revenueByMethod = DB::table('fee_payments')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount_paid) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.finance-report', compact(
            'payments',
            'classes',
            'academicYears',
            'feeCategories',
            'paymentMethods',
            'totalCollected',
            'totalPayments',
            'filteredTotal',
            'revenueByCategory',
            'revenueByMethod'
        ));
    }
}
