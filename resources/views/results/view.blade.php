@extends('layouts.app')

@section('content')
<div class="page-content">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ $pageTitle }}</h3>
        <div>
            @if (!empty($studentResults) && auth()->user()->can('academic.read'))
            <button class="btn btn-outline-primary btn-sm me-2" onclick="window.print()">
                <i data-lucide="printer" class="me-1"></i>Print
            </button>
            @endif
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3">Filter Results</h6>
            <form method="GET" action="{{ route('results-viewer.view') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select form-select-sm">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $filters['academic_year_id'] == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Academic Period</label>
                    <select name="academic_period_id" class="form-select form-select-sm">
                        <option value="">All Periods</option>
                        @foreach($academicPeriods as $period)
                        <option value="{{ $period->id }}" {{ $filters['academic_period_id'] == $period->id ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->hasRole(['admin', 'super-admin']) || auth()->user()->can('academic.read'))
                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select form-select-sm">
                        <option value="">All Students</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $filters['student_id'] == $student->id ? 'selected' : '' }}>
                            {{ $student->first_name }} {{ $student->last_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i data-lucide="search" class="me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Display -->
    @if(!empty($studentResults) && count($studentResults) > 0)

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Students</h6>
                    <h3 class="mb-0">{{ count($studentResults) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Subjects</h6>
                    <h3 class="mb-0">{{ count($subjectsList) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title text-white">Assessments</h6>
                    <h3 class="mb-0">{{ count($assessmentNames) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Results Cards -->
    @foreach($studentResults as $studentData)
    <div class="card mb-4 result-card" data-student-id="{{ $studentData['student_id'] }}">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i data-lucide="user" class="me-2"></i>{{ $studentData['student_name'] }}
                        <small class="text-muted">#{{ $studentData['student_record']->student_id }}</small>
                    </h5>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-sm btn-outline-primary no-print" onclick="printStudentCard({{ $studentData['student_id'] }})" title="Print this student's results">
                        <i data-lucide="printer" class="me-1" style="width: 16px; height: 16px;"></i>Print
                    </button>
                    <div class="badge bg-info">
                        {{ $studentData['student_record']->status === 'active' ? '✓ Active' : '✗ Inactive' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Academic Period Info -->
            @if($studentData['academicYear'] || $studentData['academicPeriod'])
            <div class="row mb-4 pb-3 border-bottom">
                @if($studentData['academicYear'])
                <div class="col-md-3">
                    <small class="text-muted">Academic Year</small>
                    <p class="mb-0 fw-semibold">{{ $studentData['academicYear']->name }}</p>
                </div>
                @endif
                @if($studentData['academicPeriod'])
                <div class="col-md-3">
                    <small class="text-muted">Period</small>
                    <p class="mb-0 fw-semibold">{{ $studentData['academicPeriod']->name }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Subjects and Scores -->
            @if(count($studentData['subjects']) > 0)
            @foreach($studentData['subjects'] as $subjectName => $subjectData)
            <div class="mb-4">
                <h6 class="fw-semibold mb-3">{{ $subjectName }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Assessment</th>
                                <th class="text-end">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectData['assessments'] as $assessmentName => $score)
                            <tr>
                                <td>{{ $assessmentName }}</td>
                                <td class="text-end">
                                    <span class="badge bg-primary">{{ $score }}</span>
                                </td>
                            </tr>
                            @endforeach
                            <tr class="table-light fw-semibold">
                                <td>Subtotal</td>
                                <td class="text-end">
                                    <span class="badge bg-success">{{ $subjectData['total_score'] }}</span>
                                </td>
                            </tr>
                            <tr class="table-light fw-semibold text-success">
                                <td>Average</td>
                                <td class="text-end">
                                    <span class="badge bg-info">{{ number_format($subjectData['average'], 2) }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            @else
            <div class="alert alert-info" role="alert">
                <i data-lucide="info" class="me-2"></i>No results available for this student.
            </div>
            @endif
        </div>
    </div>
    @endforeach

    @else
    <!-- Empty State -->
    <div class="card">
        <div class="card-body text-center py-5">
            <i data-lucide="inbox" style="width: 64px; height: 64px;" class="text-muted mb-3"></i>
            <h5 class="text-muted">No Results Found</h5>
            <p class="text-muted mb-0">
                @if($empty ?? false)
                You don't have any wards/children assigned to your account yet.
                @else
                No results match the selected filters. Please adjust your filters and try again.
                @endif
            </p>
        </div>
    </div>
    @endif

</div>

<!-- Lucide Icons -->
<script src="{{ asset('assets/js/feather.min.js') }}"></script>

<!-- Print CSS -->
<style>
    @media print {

        /* Hide all elements by default */
        body * {
            display: none;
        }

        /* Show only the page content */
        .page-content {
            display: block !important;
            margin: 0;
            padding: 0;
        }

        /* Show only the selected result card */
        .result-card {
            display: block !important;
            page-break-inside: avoid;
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
        }

        /* Show the card header */
        .result-card .card-header {
            display: block !important;
            background: #f8f9fa !important;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 15px;
        }

        /* Show the card body */
        .result-card .card-body {
            display: block !important;
            padding: 15px 0;
        }

        /* Show all elements within the selected card */
        .result-card * {
            display: block !important;
            page-break-inside: avoid;
        }

        /* Fix flex layouts in the card */
        .result-card .d-flex {
            display: flex !important;
        }

        .result-card .d-flex.justify-content-between {
            justify-content: space-between !important;
        }

        .result-card .d-flex.align-items-center {
            align-items: center !important;
        }

        /* Show tables */
        .result-card .table,
        .result-card .table-responsive {
            display: table !important;
            width: 100% !important;
        }

        .result-card .table thead,
        .result-card .table tbody,
        .result-card .table tr,
        .result-card .table th,
        .result-card .table td {
            display: table-cell !important;
        }

        .result-card .table thead {
            display: table-header-group !important;
        }

        .result-card .table tbody {
            display: table-row-group !important;
        }

        .result-card .table tr {
            display: table-row !important;
            page-break-inside: avoid;
        }

        /* Show badges and inline elements */
        .result-card .badge,
        .result-card .text-muted,
        .result-card .fw-semibold,
        .result-card h5,
        .result-card h6,
        .result-card p,
        .result-card small {
            display: inline !important;
        }

        /* Show divs and blocks */
        .result-card .row,
        .result-card .col-md-3 {
            display: block !important;
            width: 100% !important;
            margin-bottom: 15px;
        }

        /* Hide print button on printed page */
        .no-print {
            display: none !important;
        }
    }
</style>

<script>
    feather.replace();

    /**
     * Print only the selected student's result card
     */
    function printStudentCard(studentId) {
        // Hide all result cards except the one we want to print
        const allCards = document.querySelectorAll('.result-card');
        allCards.forEach(card => {
            if (parseInt(card.getAttribute('data-student-id')) !== studentId) {
                card.style.display = 'none';
            }
        });

        // Also hide other elements
        document.querySelector('.page-content > h3')?.style.display = 'none';
        document.querySelector('.page-content > .row:nth-child(2)')?.style.display = 'none';
        document.querySelector('.page-content > .card:nth-child(3)')?.style.display = 'none';
        document.querySelectorAll('.alert').forEach(el => el.style.display = 'none');

        // Print
        window.print();

        // Show all cards again after print
        setTimeout(() => {
            allCards.forEach(card => {
                card.style.display = '';
            });
            document.querySelector('.page-content > h3')?.style.display = '';
            document.querySelector('.page-content > .row:nth-child(2)')?.style.display = '';
            document.querySelector('.page-content > .card:nth-child(3)')?.style.display = '';
            document.querySelectorAll('.alert').forEach(el => el.style.display = '');
        }, 500);
    }
</script>

@endsection