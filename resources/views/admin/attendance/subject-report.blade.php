@extends('layouts.app')

@section('title', 'Subject Attendance Report')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Subject Attendance Report</h6>
        <div>
            <a href="{{ route('admin.attendance.create') }}" class="btn btn-success btn-sm me-2">
                <i data-lucide="plus" style="width: 14px;" class="me-1"></i>Take Attendance
            </a>
            <button id="printTableBtn" class="btn btn-primary btn-sm me-2">
                <i data-lucide="printer" style="width: 14px;" class="me-1"></i>Print
            </button>
            <button id="downloadPdfBtn" class="btn btn-danger btn-sm">
                <i data-lucide="download" style="width: 14px;" class="me-1"></i>Download PDF
            </button>
        </div>
    </div>
</div>

@include('includes.message')

<!-- Subject Statistics -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Subject-wise Attendance Summary</h6>
    </div>
    <div class="card-body">
        @if($subjectStats->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Subject</th>
                        <th class="text-end">Total Records</th>
                        <th class="text-end">Present</th>
                        <th class="text-end">Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjectStats as $stat)
                    <tr>
                        <td>
                            @php
                            $subject = \App\Models\Subject::find($stat->subject_id);
                            @endphp
                            {{ $subject->name ?? 'N/A' }}
                        </td>
                        <td class="text-end">{{ $stat->total }}</td>
                        <td class="text-end fw-bold text-success">{{ $stat->present_count }}</td>
                        <td class="text-end">
                            <strong>{{ $stat->total > 0 ? round(($stat->present_count / $stat->total) * 100, 1) : 0 }}%</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center mb-0">No subject attendance records found.</p>
        @endif
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <h6 class="card-title fw-bold mb-4">Filters</h6>

        <form method="GET" action="{{ route('admin.attendance.subject-report') }}">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Subject</label>
                    <select name="subject_id" class="form-select">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
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
                    <label class="form-label fw-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i data-lucide="search" style="width: 14px;" class="me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Detailed Records Table -->
<div class="card">
    <div class="card-header bg-light">
        <h6 class="card-title fw-bold mb-0">Attendance Details</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="12%">Date</th>
                    <th width="20%">Student</th>
                    <th width="18%">Class</th>
                    <th width="18%">Subject</th>
                    <th width="15%">Status</th>
                    <th width="17%">Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>
                        <strong>{{ $record->attendance_date->format('M d, Y') }}</strong>
                    </td>
                    <td>
                        <strong>{{ $record->student->first_name }} {{ $record->student->last_name }}</strong>
                        <br><small class="text-muted">{{ $record->student->student_id }}</small>
                    </td>
                    <td>{{ $record->classModel->name ?? 'N/A' }}</td>
                    <td>{{ $record->subject->name ?? 'N/A' }}</td>
                    <td>
                        @php
                        $statusColors = [
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning',
                        'excused' => 'info'
                        ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$record->status] ?? 'secondary' }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                    <td>
                        <small>{{ $record->remarks ?? '-' }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No subject attendance records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $records->links() }}
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printBtn = document.getElementById('printTableBtn');
        const downloadBtn = document.getElementById('downloadPdfBtn');

        if (printBtn) {
            printBtn.addEventListener('click', function(e) {
                e.preventDefault();
                printAttendanceDetails();
            });
        }

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                downloadAttendancePdf();
            });
        }
    });

    function printAttendanceDetails() {
        try {
            // Find all tables
            const allTables = document.querySelectorAll('.table-responsive table');
            if (allTables.length === 0) {
                alert('No attendance records found to print');
                return;
            }

            const summaryTable = allTables.length > 1 ? allTables[0] : null;
            const detailsTable = allTables[allTables.length - 1];

            // Create a new window for printing
            const printWindow = window.open('', 'print-window', 'height=600,width=900');
            if (!printWindow) {
                alert('Please disable popup blockers to print');
                return;
            }

            printWindow.document.write('<html><head><title>Subject Attendance Report</title>');
            printWindow.document.write('<style>');
            printWindow.document.write('body { padding: 30px; font-family: Arial, sans-serif; }');
            printWindow.document.write('h3 { font-weight: bold; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 1px; }');
            printWindow.document.write('h4 { font-weight: bold; margin-top: 25px; margin-bottom: 15px; }');
            printWindow.document.write('table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }');
            printWindow.document.write('th { background-color: #e8e8e8; padding: 12px; text-align: left; font-weight: bold; border-bottom: 2px solid #999; color: #666; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }');
            printWindow.document.write('td { padding: 12px; border-bottom: 1px solid #ddd; }');
            printWindow.document.write('tr:last-child td { border-bottom: 2px solid #999; }');
            printWindow.document.write('tr:nth-child(even) { background-color: #f9f9f9; }');
            printWindow.document.write('.badge { padding: 4px 8px; border-radius: 3px; color: white; font-size: 12px; font-weight: bold; display: inline-block; }');
            printWindow.document.write('.bg-success { background-color: #28a745; }');
            printWindow.document.write('.bg-danger { background-color: #dc3545; }');
            printWindow.document.write('.bg-warning { background-color: #ffc107; color: black; }');
            printWindow.document.write('.bg-info { background-color: #17a2b8; }');
            printWindow.document.write('.student-name { font-weight: bold; margin-bottom: 3px; }');
            printWindow.document.write('.student-id { font-size: 11px; color: #666; }');
            printWindow.document.write('.text-end { text-align: right; }');
            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');

            // Summary table
            if (summaryTable) {
                printWindow.document.write('<h3>Subject-wise Attendance Summary</h3>');
                printWindow.document.write('<table>');
                printWindow.document.write('<thead><tr><th>Subject</th><th class="text-end">Total Records</th><th class="text-end">Present</th><th class="text-end">Attendance %</th></tr></thead>');
                printWindow.document.write('<tbody>');

                const summaryRows = summaryTable.querySelectorAll('tbody tr');
                summaryRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        const subject = cells[0].innerText.trim();
                        const total = cells[1].innerText.trim();
                        const present = cells[2].innerText.trim();
                        const percentage = cells[3].innerText.trim();

                        printWindow.document.write('<tr>');
                        printWindow.document.write('<td>' + subject + '</td>');
                        printWindow.document.write('<td class="text-end">' + total + '</td>');
                        printWindow.document.write('<td class="text-end">' + present + '</td>');
                        printWindow.document.write('<td class="text-end">' + percentage + '</td>');
                        printWindow.document.write('</tr>');
                    }
                });

                printWindow.document.write('</tbody></table>');
            }

            // Details table
            printWindow.document.write('<h3>Attendance Details</h3>');
            printWindow.document.write('<table>');
            printWindow.document.write('<thead><tr><th>Date</th><th>Student</th><th>Class</th><th>Subject</th><th>Status</th><th>Remarks</th></tr></thead>');
            printWindow.document.write('<tbody>');

            const rows = detailsTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) {
                    const date = cells[0].innerText.trim();
                    const studentText = cells[1].innerText.trim();
                    const [studentName, studentId] = studentText.split('\n');
                    const className = cells[2].innerText.trim();
                    const subject = cells[3].innerText.trim();
                    const statusText = cells[4].innerText.trim();
                    const remarks = cells[5].innerText.trim();

                    printWindow.document.write('<tr>');
                    printWindow.document.write('<td>' + date + '</td>');
                    printWindow.document.write('<td><div class="student-name">' + (studentName || 'N/A') + '</div><div class="student-id">' + (studentId || '') + '</div></td>');
                    printWindow.document.write('<td>' + className + '</td>');
                    printWindow.document.write('<td>' + subject + '</td>');
                    printWindow.document.write('<td><span class="badge">' + statusText + '</span></td>');
                    printWindow.document.write('<td>' + remarks + '</td>');
                    printWindow.document.write('</tr>');
                }
            });

            printWindow.document.write('</tbody></table>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            // Wait for content to load before printing
            setTimeout(function() {
                printWindow.focus();
                printWindow.print();
            }, 500);
        } catch (error) {
            console.error('Print error:', error);
            alert('Error printing. Please try again.');
        }
    }

    function downloadAttendancePdf() {
        try {
            const allTables = document.querySelectorAll('.table-responsive table');
            if (allTables.length === 0) {
                alert('No attendance records found to download');
                return;
            }

            const summaryTable = allTables.length > 1 ? allTables[0] : null;
            const detailsTable = allTables[allTables.length - 1];

            const element = document.createElement('div');
            element.style.padding = '20px';
            element.style.fontFamily = 'Arial, sans-serif';

            const title = document.createElement('h3');
            title.textContent = 'SUBJECT ATTENDANCE REPORT';
            title.style.fontWeight = 'bold';
            title.style.textTransform = 'uppercase';
            title.style.marginBottom = '20px';
            title.style.letterSpacing = '1px';
            element.appendChild(title);

            // Add summary table if it exists
            if (summaryTable) {
                const summaryTitle = document.createElement('h4');
                summaryTitle.textContent = 'Subject-wise Attendance Summary';
                summaryTitle.style.fontWeight = 'bold';
                summaryTitle.style.marginTop = '20px';
                summaryTitle.style.marginBottom = '15px';
                element.appendChild(summaryTitle);

                const summaryTableElement = document.createElement('table');
                summaryTableElement.style.borderCollapse = 'collapse';
                summaryTableElement.style.width = '100%';
                summaryTableElement.style.marginBottom = '20px';

                const summaryThead = document.createElement('thead');
                const summaryHeaderRow = document.createElement('tr');
                const summaryHeaders = ['SUBJECT', 'TOTAL RECORDS', 'PRESENT', 'ATTENDANCE %'];

                summaryHeaders.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    th.style.backgroundColor = '#e8e8e8';
                    th.style.padding = '12px';
                    th.style.textAlign = header === 'SUBJECT' ? 'left' : 'right';
                    th.style.fontWeight = 'bold';
                    th.style.borderBottom = '2px solid #999';
                    th.style.color = '#666';
                    th.style.textTransform = 'uppercase';
                    th.style.fontSize = '12px';
                    th.style.letterSpacing = '0.5px';
                    summaryHeaderRow.appendChild(th);
                });
                summaryThead.appendChild(summaryHeaderRow);
                summaryTableElement.appendChild(summaryThead);

                const summaryTbody = document.createElement('tbody');
                const summaryRows = summaryTable.querySelectorAll('tbody tr');
                let summaryRowCount = 0;

                summaryRows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 4) {
                        const newRow = document.createElement('tr');
                        if (summaryRowCount % 2 === 1) {
                            newRow.style.backgroundColor = '#f9f9f9';
                        }

                        const subject = cells[0].innerText.trim();
                        const total = cells[1].innerText.trim();
                        const present = cells[2].innerText.trim();
                        const percentage = cells[3].innerText.trim();

                        // Subject
                        const tdSubject = document.createElement('td');
                        tdSubject.textContent = subject;
                        tdSubject.style.padding = '12px';
                        tdSubject.style.borderBottom = '1px solid #ddd';
                        newRow.appendChild(tdSubject);

                        // Total
                        const tdTotal = document.createElement('td');
                        tdTotal.textContent = total;
                        tdTotal.style.padding = '12px';
                        tdTotal.style.borderBottom = '1px solid #ddd';
                        tdTotal.style.textAlign = 'right';
                        newRow.appendChild(tdTotal);

                        // Present
                        const tdPresent = document.createElement('td');
                        tdPresent.textContent = present;
                        tdPresent.style.padding = '12px';
                        tdPresent.style.borderBottom = '1px solid #ddd';
                        tdPresent.style.textAlign = 'right';
                        tdPresent.style.fontWeight = 'bold';
                        tdPresent.style.color = '#28a745';
                        newRow.appendChild(tdPresent);

                        // Percentage
                        const tdPercentage = document.createElement('td');
                        tdPercentage.textContent = percentage;
                        tdPercentage.style.padding = '12px';
                        tdPercentage.style.borderBottom = '1px solid #ddd';
                        tdPercentage.style.textAlign = 'right';
                        tdPercentage.style.fontWeight = 'bold';
                        newRow.appendChild(tdPercentage);

                        summaryTbody.appendChild(newRow);
                        summaryRowCount++;
                    }
                });

                summaryTableElement.appendChild(summaryTbody);
                element.appendChild(summaryTableElement);
            }

            // Add details table
            const detailsTitle = document.createElement('h4');
            detailsTitle.textContent = 'Attendance Details';
            detailsTitle.style.fontWeight = 'bold';
            detailsTitle.style.marginTop = '30px';
            detailsTitle.style.marginBottom = '15px';
            element.appendChild(detailsTitle);

            const table = document.createElement('table');
            table.style.borderCollapse = 'collapse';
            table.style.width = '100%';

            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const headers = ['DATE', 'STUDENT', 'CLASS', 'SUBJECT', 'STATUS', 'REMARKS'];

            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                th.style.backgroundColor = '#e8e8e8';
                th.style.padding = '12px';
                th.style.textAlign = 'left';
                th.style.fontWeight = 'bold';
                th.style.borderBottom = '2px solid #999';
                th.style.color = '#666';
                th.style.textTransform = 'uppercase';
                th.style.fontSize = '12px';
                th.style.letterSpacing = '0.5px';
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            const rows = detailsTable.querySelectorAll('tbody tr');
            let rowCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) {
                    const newRow = document.createElement('tr');
                    if (rowCount % 2 === 1) {
                        newRow.style.backgroundColor = '#f9f9f9';
                    }

                    const date = cells[0].innerText.trim();
                    const studentText = cells[1].innerText.trim();
                    const parts = studentText.split('\n');
                    const studentName = parts[0] || 'N/A';
                    const studentId = parts[1] || '';
                    const className = cells[2].innerText.trim();
                    const subject = cells[3].innerText.trim();
                    const statusText = cells[4].innerText.trim();
                    const remarks = cells[5].innerText.trim();

                    // Date
                    const tdDate = document.createElement('td');
                    tdDate.textContent = date;
                    tdDate.style.padding = '12px';
                    tdDate.style.borderBottom = '1px solid #ddd';
                    newRow.appendChild(tdDate);

                    // Student
                    const tdStudent = document.createElement('td');
                    tdStudent.style.padding = '12px';
                    tdStudent.style.borderBottom = '1px solid #ddd';
                    const studentNameDiv = document.createElement('div');
                    studentNameDiv.textContent = studentName;
                    studentNameDiv.style.fontWeight = 'bold';
                    studentNameDiv.style.marginBottom = '3px';
                    const studentIdDiv = document.createElement('div');
                    studentIdDiv.textContent = studentId;
                    studentIdDiv.style.fontSize = '11px';
                    studentIdDiv.style.color = '#666';
                    tdStudent.appendChild(studentNameDiv);
                    tdStudent.appendChild(studentIdDiv);
                    newRow.appendChild(tdStudent);

                    // Class
                    const tdClass = document.createElement('td');
                    tdClass.textContent = className;
                    tdClass.style.padding = '12px';
                    tdClass.style.borderBottom = '1px solid #ddd';
                    newRow.appendChild(tdClass);

                    // Subject
                    const tdSubject = document.createElement('td');
                    tdSubject.textContent = subject;
                    tdSubject.style.padding = '12px';
                    tdSubject.style.borderBottom = '1px solid #ddd';
                    newRow.appendChild(tdSubject);

                    // Status
                    const tdStatus = document.createElement('td');
                    tdStatus.style.padding = '12px';
                    tdStatus.style.borderBottom = '1px solid #ddd';
                    const badge = document.createElement('span');
                    badge.textContent = statusText;
                    badge.style.padding = '4px 8px';
                    badge.style.borderRadius = '3px';
                    badge.style.color = 'white';
                    badge.style.fontSize = '12px';
                    badge.style.fontWeight = 'bold';
                    badge.style.display = 'inline-block';

                    // Set badge color based on status
                    const upperStatus = statusText.toUpperCase();
                    if (upperStatus === 'PRESENT') {
                        badge.style.backgroundColor = '#28a745';
                    } else if (upperStatus === 'ABSENT') {
                        badge.style.backgroundColor = '#dc3545';
                    } else if (upperStatus === 'LATE') {
                        badge.style.backgroundColor = '#ffc107';
                        badge.style.color = 'black';
                    } else if (upperStatus === 'EXCUSED') {
                        badge.style.backgroundColor = '#17a2b8';
                    }

                    tdStatus.appendChild(badge);
                    newRow.appendChild(tdStatus);

                    // Remarks
                    const tdRemarks = document.createElement('td');
                    tdRemarks.textContent = remarks;
                    tdRemarks.style.padding = '12px';
                    tdRemarks.style.borderBottom = '1px solid #ddd';
                    newRow.appendChild(tdRemarks);

                    tbody.appendChild(newRow);
                    rowCount++;
                }
            });

            table.appendChild(tbody);
            element.appendChild(table);

            const opt = {
                margin: 10,
                filename: 'subject-attendance-report-' + new Date().toISOString().split('T')[0] + '.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4'
                }
            };

            html2pdf().set(opt).from(element).save();
        } catch (error) {
            console.error('PDF download error:', error);
            alert('Error downloading PDF. Please try again.');
        }
    }
</script>
@endpush

@endsection