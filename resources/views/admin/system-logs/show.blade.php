@extends('layouts.app')

@section('title', 'View System Log')

@section('content')
<div class="row mb-4">
    <div class="col-12 dashboard-header bg-primary text-white p-4 rounded d-flex justify-content-between align-items-center">
        <div>
            <h2>Log Details</h2>
            <p class="mb-0">Detailed view of the system activity log.</p>
        </div>
        <div>
            <a href="{{ route('admin.system-logs.index') }}" class="btn btn-light"><i class="fas fa-arrow-left me-1"></i> Back to Logs</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Log Record Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th class="bg-light" style="width: 25%;">Timestamp</th>
                            <td>{{ $log->created_at->format('l, F j, Y \\a\\t H:i:s') }} <span class="text-muted">({{ $log->created_at->diffForHumans() }})</span></td>
                        </tr>
                        <tr>
                            <th class="bg-light">User</th>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <div class="ms-0">
                                            <h6 class="mb-0">{{ $log->user->name }}</h6>
                                            <small class="text-muted">{{ $log->user->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">System / Guest</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Action Type</th>
                            <td>
                                <span class="badge bg-secondary px-2 py-2">{{ $log->action }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Affected Module</th>
                            <td>
                                @if($log->module)
                                    <span class="badge rounded-pill bg-light text-dark border">{{ $log->module }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">IP Address</th>
                            <td>
                                @if($log->ip_address)
                                    <code>{{ $log->ip_address }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light align-middle">Details / Description</th>
                            <td>
                                <div class="bg-light p-3 rounded text-break" style="font-family: inherit; font-size: 0.95rem; white-space: pre-wrap;">{{ $log->description }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
