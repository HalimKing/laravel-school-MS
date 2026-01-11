@extends('layouts.app')

@section('title', 'Collect Fees')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="d-flex align-items-center mb-0 text-uppercase fw-bold">
        <span class="text-muted">Finance</span>
        <i data-lucide="chevron-right" class="mx-2" style="width: 16px;"></i>
        <span>Fee Collection</span>
    </h6>
</div>

@include('includes.message')

<div class="row">
    <!-- Left Column: Student Search & Selection -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-3">Find Student</h6>
                <form action="{{ route('admin.fee-management.collect-fees.index') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" name="search" class="form-control" placeholder="Admission No. or Name" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i data-lucide="search" style="width: 16px;"></i>
                        </button>
                    </div>
                </form>

                <div class="student-results-list" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($students) && $students->count() > 0)
                        @foreach($students as $s)
                            <a href="{{ route('admin.fee-management.collect-fees.index', ['student_id' => $s->id]) }}" 
                               class="d-flex align-items-center p-2 mb-2 border rounded text-decoration-none {{ isset($student) && $student->id == $s->id ? 'bg-primary text-white border-primary' : ' hover-bg-light' }}">
                                <div class="avatar-sm me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center " style="width: 35px; height: 35px;">
                                        <i data-lucide="user" class="{{ isset($student) && $student->id == $s->id ? 'text-primary' : 'text-secondary' }}" style="width: 18px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold small">{{ $s->student->first_name }} {{ $s->student->last_name }}</p>
                                    <small class="{{ isset($student) && $student->id == $s->id ? 'text-white-50' : 'text-muted' }}">{{ $s->student->student_id }}</small>
                                    <span class="badge bg-success ms-1" style="font-size: 0.7rem;">{{ $s->class->name ?? 'N/A' }}</span>
                                </div>
                            </a>
                        @endforeach
                    @elseif(request('search'))
                        <div class="text-center py-4">
                            <p class="text-muted small">No students found.</p>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted small italic">Search for a student to begin collection.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(isset($student))
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title small text-uppercase text-muted">Summary</h6>
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-primary">{{ $student->student->first_name }} {{ $student->student->last_name }}</h5>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Admission No:</span>
                        <span class="small fw-bold">{{ $student->student->student_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small">Class:</span>
                        <span class="small fw-bold">{{ $student->class->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-top pt-2">
                        <span class="small fw-bold">Total Outstanding:</span>
                        <span class="small fw-bold text-danger">₵{{ number_format($totalBalance, 2) }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Collection Form -->
    <div class="col-md-8">
        @if(isset($student))
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-4 border-bottom pb-2">Record New Payment</h6>
                    
                    <form action="{{ route('admin.fee-management.collect-fees.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fee Structure <span class="text-danger">*</span></label>
                                <select name="fee_structure_id" id="fee_structure_id" class="form-select @error('fee_structure_id') is-invalid @enderror" required>
                                    <option value="">Select Applicable Fee</option>
                                    @foreach($feeStructures as $fee)
                                        <option value="{{ $fee->id }}" data-amount="{{ $fee->amount }}">
                                            {{ $fee->feeCategory->name }} ({{ $fee->academicYear->name }}) - ₵{{ number_format($fee->amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Amount Paid <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₵</span>
                                    <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control @error('amount_paid') is-invalid @enderror" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference / Transaction ID</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="Receipt or Txn Number">
                        </div>

                        <div class="p-3 rounded mb-4 d-flex justify-content-between align-items-center border">
                            <div>
                                <p class="text-muted small mb-0">Balance Remaining for this Fee:</p>
                                <h4 class="mb-0 fw-bold" id="calc_balance">₵0.00</h4>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i data-lucide="save" class="me-2" style="width: 20px;"></i> Post & Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent History -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">Payment History</h6>
                        <span class="badge bg-info text-dark">Showing last 10</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="">
                                <tr>
                                    <th class="small fw-bold">Date</th>
                                    <th class="small fw-bold">Category</th>
                                    <th class="small fw-bold">Method</th>
                                    <th class="small fw-bold text-end">Amount</th>
                                    <th class="small fw-bold text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td class="small">{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td class="small fw-medium">{{ $payment->fee->feeCategory->name }}</td>
                                        <td class="small text-capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                        <td class="small text-end fw-bold text-success">₵{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.fee-management.collect-fees.receipt', $payment->id) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Print Receipt">
                                                <i data-lucide="printer" style="width: 14px;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">No payments recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-none text-center py-5">
                <div class="card-body py-5">
                    <i data-lucide="credit-card" class="text-muted mb-3" style="width: 80px; height: 80px; opacity: 0.2;"></i>
                    <h5 class="text-muted">Selection Required</h5>
                    <p class="text-muted mx-auto" style="max-width: 400px;">Search for a student using the sidebar to record a fee payment or view their transaction history.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeSelect = document.getElementById('fee_structure_id');
    const amountInput = document.getElementById('amount_paid');
    const balanceDisplay = document.getElementById('calc_balance');

    function updateBalance() {
        if (!feeSelect || !amountInput || !balanceDisplay) return;

        const selectedOption = feeSelect.options[feeSelect.selectedIndex];
        const totalFee = parseFloat(selectedOption.getAttribute('data-amount')) || 0;
        const paid = parseFloat(amountInput.value) || 0;
        
        const balance = Math.max(0, totalFee - paid);
        balanceDisplay.textContent = '₵' + balance.toLocaleString('en-US', { minimumFractionDigits: 2 });
        
        balanceDisplay.className = balance > 0 ? 'mb-0 fw-bold text-danger' : 'mb-0 fw-bold text-success';
    }

    if(feeSelect) {
        feeSelect.addEventListener('change', updateBalance);
        amountInput.addEventListener('input', updateBalance);
    }
});
</script>
@endsection