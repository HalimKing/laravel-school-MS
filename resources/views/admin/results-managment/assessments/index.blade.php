@extends('layouts.app')

@section('title', 'Assessment Structures')

@section('content')

<div class="card mb-4 p-4">
<h6 class="mb-0 text-uppercase fw-bold">Assessment Structures</h6>
</div>

@include('includes.message')

<!-- Stats Summary -->

<div class="row mb-4">
<div class="col-md-4 grid-margin stretch-card">
<div class="card border-start border-primary border-4">
<div class="card-body">
<small class="text-uppercase fw-bold text-primary d-block mb-1">Active Configurations</small>
<h3 class="mb-0">24 Subjects</h3>
</div>
</div>
</div>
<div class="col-md-4 grid-margin stretch-card">
<div class="card border-start border-success border-4">
<div class="card-body">
<small class="text-uppercase fw-bold text-success d-block mb-1">Compliant (100% Weight)</small>
<h3 class="mb-0">22</h3>
</div>
</div>
</div>
<div class="col-md-4 grid-margin stretch-card">
<div class="card border-start border-warning border-4">
<div class="card-body">
<small class="text-uppercase fw-bold text-warning d-block mb-1">Action Required</small>
<h3 class="mb-0">2</h3>
</div>
</div>
</div>
</div>

<div class="row">
<div class="col-md-12 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-3">
<h6 class="card-title mb-0">Subject Grading Structures</h6>
<a class="btn btn-primary" href="{{ route('admin.results-management.assessments.create') }}">
<i data-lucide="plus" class="me-1" style="width: 18px;"></i> Configure New Subject
</a>
</div>

            <div class="table-responsive">
                <table id="dataTableExample" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Class Level</th>
                            <th>Breakdown</th>
                            <th>Total Weight</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                   
                    @forelse($assessments as $subjectName => $group)
                        <tbody>
                            <tr>
                                <td>
                                    <span class="fw-semibold d-block">{{ $group->first()->subject->name }}</span>
                                    <small class="text-muted">{{ $group->first()->subject->subject_type ?? 'Core' }}</small>
                                </td>
                                <td>
                                    {{ $group->pluck('class.name')->unique()->implode(', ') }}
                                </td>
                                <td>
                                    @php
                                        $grouped = [];
                                        $total = 0;
                                    @endphp
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($group as $assessment)
                                            @php
                                                $grouped[$assessment->name] = $assessment->percentage;
                                                $total;
                                            @endphp
                                            
                                        @endforeach
                                        @foreach($grouped as $name => $percentage)
                                            <span class="badge bg-info-subtle text-dark border border-info px-2">
                                                {{ $name }}: {{ (int)$percentage }}%
                                            </span>
                                            @php
                                                $total += $percentage;
                                            @endphp
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="min-width: 120px;">
                                        <span class="me-2 fw-bold small">{{ (int)$total }}%</span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar {{ $total == 100 ? 'bg-success' : 'bg-warning' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $total }} %"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($total == 100)
                                        <span class="badge bg-success-subtle text-success border border-success px-3">Active</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning px-3">Incomplete</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.results-management.assessments.edit', $group->first()->subject_id) }}">
                                                    <i data-lucide="pencil" class="me-2 text-muted" style="width: 14px;"></i> Edit Structure
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.results-management.assessments.destroy', $group->first()->subject_id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center" onclick="return confirm('Delete this grading structure?')">
                                                        <i data-lucide="trash-2" class="me-2" style="width: 14px;"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i data-lucide="clipboard-list" class="mb-2" style="width: 40px; height: 40px; opacity: 0.2;"></i>
                                    <p class="mb-0">No assessment structures configured.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


</div>
@endsection