@extends('layouts.app')

@section('title', 'Finance Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Finance Report</h6>
        <div>
            <a href="{{ route('reports.finance', request()->query()) }}" class="btn btn-primary btn-sm me-2">
                <i data-lucide="refresh-cw" style="width: 14px;" class="me-1"></i>Refresh
            </a>
            <button onclick="window.print()" class="btn btn-success btn-sm">
                <i data-lucide="printer" style="width: 14px;" class="me-1"></i>Print
            </button>
        </div>
    </div>
</div>

@include('includes.message')

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Collected</h6>
                <h3 class="mb-0 text-success">₦{{ number_format($totalCollected, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Total Transactions</h6>
                <h3 class="mb-0 text-primary">{{ $totalPayments }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Filtered Total</h6>
                <h3 class="mb-0 text-info">₦{{ number_format($filteredTotal, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-uppercase fw-bold small mb-2">Avg Per Transaction</h6>
                <h3 class="mb-0 text-warning">
                    @if($totalPayments > 0)
                    ₦{{ number_format($totalCollected / $totalPayments, 2) }}
                    @else
                    ₦0.00
                    @endif
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Breakdown -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Revenue by Category</h6>
            </div>
            <div class="card-body">
                @if($revenueByCategory->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByCategory as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td class="text-end fw-bold">₦{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Revenue by Payment Method</h6>
            </div>
            <div class="card-body">
                @if($revenueByMethod->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByMethod as $item)
                            <tr>
                                <td>{{ ucfirst($item->payment_method) }}</td>
                                <td class="text-end">{{ $item->count }}</td>
                                <td class="text-end fw-bold">₦{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted text-center mb-0">No data available</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('reports.finance') }}" class="filter-form">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Search</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="Student name..." value="{{ request('search') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="fee_category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($feeCategories as $category)
                        <option value="{{ $category->id }}" {{ request('fee_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Method</label>
                    <select name="payment_method" class="form-select">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                            {{ ucfirst($method) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;" class="me-1"></i>Filter
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <a href="{{ route('reports.finance') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i data-lucide="x" style="width: 14px;" class="me-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="18%">
                        <a href="{{ route('reports.finance', array_merge(request()->query(), ['sort_by' => 'payment_date', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'payment_date' ? 'desc' : 'asc'])) }}">
                            Date
                            @if(request('sort_by') == 'payment_date')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="15%">Student</th>
                    <th width="12%">Class</th>
                    <th width="15%">Fee Type</th>
                    <th width="12%">Method</th>
                    <th width="12%">
                        <a href="{{ route('reports.finance', array_merge(request()->query(), ['sort_by' => 'amount_paid', 'sort_order' => request('sort_order') == 'asc' && request('sort_by') == 'amount_paid' ? 'desc' : 'asc'])) }}">
                            Amount
                            @if(request('sort_by') == 'amount_paid')
                            <i data-lucide="{{ request('sort_order') == 'asc' ? 'arrow-up' : 'arrow-down' }}" style="width: 14px;"></i>
                            @endif
                        </a>
                    </th>
                    <th width="16%">Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                    <td>
                        <strong>{{ $payment->levelData->student->first_name }} {{ $payment->levelData->student->last_name }}</strong>
                        <br><small class="text-muted">{{ $payment->levelData->student->student_id }}</small>
                    </td>
                    <td>{{ $payment->levelData->classModel->name ?? 'N/A' }}</td>
                    <td>{{ $payment->fee->feeCategory->name ?? 'N/A' }}</td>
                    <td><span class="badge bg-info">{{ ucfirst($payment->payment_method) }}</span></td>
                    <td class="fw-bold">₦{{ number_format($payment->amount_paid, 2) }}</td>
                    <td>
                        <small>{{ $payment->reference_no ?? '-' }}</small>
                        @if($payment->remarks)
                        <br><small class="text-muted">{{ Str::limit($payment->remarks, 30) }}</small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        No payment records found matching the criteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>

@endsection