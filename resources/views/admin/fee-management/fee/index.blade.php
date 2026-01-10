@extends('layouts.app')

@section('title', 'Fee Structures')

@section('content')

<div class="card mb-4 p-4">
    <h6 class="mb-0 text-uppercase fw-bold text-primary">Fee Management</h6>
</div>

@include('includes.message')

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form id="filterForm" method="GET" action="{{ route('admin.fee-management.fees.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" class="form-select">
                    <option value="">All Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Class</label>
                <select name="class_id" id="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Category</label>
                <select name="fee_category_id" id="fee_category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($feeCategories as $cat)
                        <option value="{{ $cat->id }}" {{ request('fee_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title mb-0">Fee Structure List</h6>
                    <a class="btn btn-primary" href="{{ route('admin.fee-management.fees.create') }}">
                        <i data-lucide="plus-circle" class="me-1" style="width: 18px;"></i> Configure New Fee
                    </a>
                </div>

                <div class="table-responsive">
                    <div id="loading-spinner" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading data...</p>
                    </div>
                    
                    <table id="feeTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fee Category</th>
                                <th>Academic Year</th>
                                <th>Academic Period</th>
                                <th>Class</th>
                                <th>Amount</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="feeTableBody">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                    
                    <div id="noDataMessage" class="text-center py-5 text-muted d-none">
                        <i data-lucide="receipt" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                        <p class="mb-0">No fee structures found matching your criteria.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initial load of data
    loadFeeData();

    // Filter form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadFeeData();
    });

    // Reset button click
    $('#resetBtn').on('click', function() {
        $('#academic_year_id').val('');
        $('#class_id').val('');
        $('#fee_category_id').val('');
        loadFeeData();
    });

    // Change events for filter fields
    $('#academic_year_id, #class_id, #fee_category_id').on('change', function() {
        loadFeeData();
    });

    function loadFeeData() {
        // Show loading spinner
        $('#loading-spinner').removeClass('d-none');
        $('#feeTable').addClass('d-none');
        $('#noDataMessage').addClass('d-none');

        // Get filter values
        const academicYearId = $('#academic_year_id').val();
        const classId = $('#class_id').val();
        const feeCategoryId = $('#fee_category_id').val();

        // Make AJAX request
        $.ajax({
            url: '{{ route("admin.fee-management.fees.index") }}',
            method: 'GET',
            data: {
                academic_year_id: academicYearId,
                class_id: classId,
                fee_category_id: feeCategoryId,
                ajax: true // Flag to indicate AJAX request
            },
            success: function(response) {
                // Hide loading spinner
                $('#loading-spinner').addClass('d-none');
                
                if (response.html) {
                    $('#feeTableBody').html(response.html);
                    $('#feeTable').removeClass('d-none');
                    $('#noDataMessage').addClass('d-none');
                } else {
                    $('#feeTable').addClass('d-none');
                    $('#noDataMessage').removeClass('d-none');
                }
            },
            error: function(xhr, status, error) {
                // Hide loading spinner
                $('#loading-spinner').addClass('d-none');
                
                // Show error message
                $('#noDataMessage').html(
                    '<div class="alert alert-danger">Error loading data. Please try again.</div>'
                ).removeClass('d-none');
                $('#feeTable').addClass('d-none');
                
                console.error('Error:', error);
            }
        });
    }

    // Handle delete confirmation
    $(document).on('submit', '.delete-form', function(e) {
        e.preventDefault();
        
        if (confirm('Delete this fee configuration permanently?')) {
            const form = $(this);
            const url = form.attr('action');
            
            $.ajax({
                url: url,
                method: 'DELETE',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the data
                        loadFeeData();
                        
                        // Show success message (optional)
                        showToast('Fee structure deleted successfully', 'success');
                    }
                },
                error: function(xhr) {
                    showToast('Error deleting fee structure', 'error');
                }
            });
        }
    });

    function showToast(message, type) {
        // You can implement a toast notification here
        // For simplicity, using alert for now
        alert(message);
    }
});
</script>
@endpush