<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\LevelData;
use App\Models\Student;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectFeeController extends Controller
{
     public function index(Request $request)
    {
        $search = $request->input('search');
        $studentId = $request->input('student_id');
        
        $students = collect();
        $student = null;
        $feeStructures = collect();
        $recentPayments = collect();
        $totalBalance = 0;

        // 1. Handle Student Search
        if ($search) {
            $matchingStudentIds = Student::where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('student_id', 'like', "%{$search}%")
                ->pluck('id');

            $students = LevelData::with(['student', 'class', 'academicYear'])
                ->whereIn('student_id', $matchingStudentIds)
                ->get();
        }

        // 2. Handle Selected Student Data
        if ($studentId) {
            $student = LevelData::with(['class', 'academicYear'])->find($studentId);
            
            // Check if student exists before processing
            if ($student) {
                // Get applicable fee structures for this student's class
                $feeStructures = Fee::with(['feeCategory', 'academicPeriod', 'academicYear'])
                    ->where('class_id', $student->class_id)
                    ->get();
                    // dd($feeStructures);

                // Get payment history
                $recentPayments = FeePayment::where('level_data_id', $student->id)
                    ->with('fee.feeCategory')
                    ->orderBy('payment_date', 'desc')
                    ->limit(5)
                    ->get();

                // Calculate total balance (Total Fees - Total Paid)
                $totalFees = $feeStructures->sum('amount');
                $totalPaid = FeePayment::where('level_data_id', $student->id)->sum('amount_paid');
                $totalBalance = $totalFees - $totalPaid;
            } else {
                // Optionally add a flash message if student not found
                session()->flash('warning', 'Student not found with the provided ID.');
            }
        }

        return view('admin.fee-management.collect-fees.index', compact(
            'students', 
            'student', 
            'feeStructures', 
            'recentPayments', 
            'totalBalance'
        ));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fees,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:255',
        ]);
        DB::beginTransaction();

        try {
            // Check if this specific fee is already fully paid (Optional Logic)
            $structure = Fee::findOrFail($request->fee_structure_id);
            $alreadyPaid = FeePayment::where('level_data_id', $request->student_id)
                ->where('fee_id', $request->fee_structure_id)
                ->sum('amount_paid');
            
            $remaining = $structure->amount - $alreadyPaid;

            // Optional: Prevent overpayment if your business rules dictate
            /*
            if ($request->amount_paid > $remaining) {
                return back()->with('error', 'Payment amount exceeds the remaining balance for this fee category.');
            }
            */

            // Create the payment record
            $payment = new FeePayment();
            $payment->level_data_id = $request->student_id;
            $payment->fee_id = $request->fee_structure_id;
            $payment->amount_paid = $request->amount_paid;
            $payment->payment_date = $request->payment_date;
            $payment->payment_method = $request->payment_method;
            $payment->reference_no = $request->reference_no;
            $payment->remarks = $request->remarks;
            // $payment->recorded_by = auth()->id(); // Assuming admin authentication
            $payment->save();

            DB::commit();

            return redirect()
                ->route('admin.fee-management.collect-fees.index', ['student_id' => $request->student_id])
                ->with('success', 'Payment recorded successfully. Receipt #' . $payment->id);

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage())->withInput();
        }
    }


    public function printReceipt($id)
    {
        $payment = FeePayment::with(['levelData.student', 'levelData.class', 'levelData.academicYear', 'fee.feeCategory'])->findOrFail($id);
        return view('admin.fee-management.collect-fees.receipt', compact('payment'));
    }
}