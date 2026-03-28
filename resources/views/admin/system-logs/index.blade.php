@extends('layouts.app')

@section('title', 'System Logs')

@push('styles')
<style>
    .log-table th,
    .log-table td {
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12 dashboard-header bg-primary text-white p-4 rounded">
        <h2>System Logs</h2>
        <p class="mb-0">Monitor system activity, user actions, and events.</p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form action="{{ route('admin.system-logs.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="search" class="form-label">Search</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Details...">
            </div>
            <div class="col-md-2">
                <label for="user_id" class="form-label">User</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $usr)
                    <option value="{{ $usr->id }}" {{ request('user_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="module" class="form-label">Module</label>
                <select name="module" id="module" class="form-select">
                    <option value="">All Modules</option>
                    @foreach($modules as $mod)
                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>{{ $mod }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="action" class="form-label">Action Type</label>
                <select name="action" id="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 mt-3 text-end">
                <a href="{{ route('admin.system-logs.index') }}" class="btn btn-secondary me-2">Clear</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search me-1"></i> Search Logs</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTableExample" class="table log-table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>IP Address</th>
                        <th>Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof jQuery !== 'undefined' && $.fn.DataTable) {

            // Build the data table
            var table = $('#dataTableExample').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.system-logs.index') }}",
                    data: function(d) {
                        d.search = $('#search').val();
                        d.user_id = $('#user_id').val();
                        d.module = $('#module').val();
                        d.action_filter = $('#action').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "Quick search logs:",
                    "searchPlaceholder": "Type to filter..."
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [5, 6]
                }]
            });

            // Override form submission to reload table instead of full page turn
            $('form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Allow clear button to reset UI and table
            $('.btn-secondary').on('click', function(e) {
                e.preventDefault();
                $('form')[0].reset();
                table.search('').draw();
            });
        }
    });
</script>
@endpush