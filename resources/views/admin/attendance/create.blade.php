@extends('layouts.app')

@section('title', 'Take Attendance')

@section('content')

<div class="card mb-4 p-4">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0 text-uppercase fw-bold">Take Attendance</h6>
        <div>
            <a href="{{ route('admin.attendance.class-report') }}" class="btn btn-info btn-sm me-2">
                <i data-lucide="chart-bar" style="width: 14px;" class="me-1"></i>Class Report
            </a>
            <a href="{{ route('admin.attendance.class-report') }}" class="btn btn-primary btn-sm">
                <i data-lucide="list" style="width: 14px;" class="me-1"></i>View Records
            </a>
        </div>
    </div>
</div>

@include('includes.message')

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title fw-bold mb-4">Select Class & Date</h6>

                <form id="classSelectionForm" method="GET">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label fw-bold">Academic Year</label>
                        <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                            <option value="">Select Academic Year</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="class_id" class="form-label fw-bold">Class</label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label fw-bold">Subject (Optional)</label>
                        <select name="subject_id" id="subject_id" class="form-select">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="attendance_date" class="form-label fw-bold">Attendance Date</label>
                        <input type="date" name="attendance_date" id="attendance_date" class="form-control"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <button type="button" class="btn btn-primary w-100" id="loadStudentsBtn">
                        <i data-lucide="download" style="width: 14px;" class="me-2"></i>Load Students
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card" id="studentsCard" style="display:none;">
            <div class="card-header bg-light">
                <h6 class="card-title fw-bold mb-0">Student Attendance</h6>
            </div>
            <div class="card-body">
                <form id="attendanceForm" method="POST" action="{{ route('admin.attendance.store') }}">
                    @csrf

                    <input type="hidden" name="class_id" id="formClassId">
                    <input type="hidden" name="academic_year_id" id="formAcademicYearId">
                    <input type="hidden" name="subject_id" id="formSubjectId">
                    <input type="hidden" name="attendance_date" id="formAttendanceDate">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Student Name</th>
                                    <th width="35%">Status</th>
                                    <th width="30%">Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i data-lucide="check" style="width: 14px;" class="me-1"></i>Save Attendance
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetForm">
                            <i data-lucide="x" style="width: 14px;" class="me-1"></i>Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="alert alert-info" id="noDataMessage">
            <i data-lucide="info" style="width: 16px;" class="me-2"></i>
            <strong>Select an academic year, class, date, and optionally a subject to load students.</strong>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    document.getElementById('loadStudentsBtn').addEventListener('click', function() {
        const academicYearId = document.getElementById('academic_year_id').value;
        const classId = document.getElementById('class_id').value;
        const subjectId = document.getElementById('subject_id').value;
        const attendanceDate = document.getElementById('attendance_date').value;

        if (!academicYearId || !classId || !attendanceDate) {
            alert('Please select academic year, class, and date');
            return;
        }

        // Show loading state
        this.disabled = true;
        this.innerHTML = '<i data-lucide="loader" style="width: 14px;" class="me-2"></i>Loading...';

        const params = new URLSearchParams({
            class_id: classId,
            academic_year_id: academicYearId,
            attendance_date: attendanceDate
        });

        if (subjectId) {
            params.append('subject_id', subjectId);
        }

        fetch('{{ route("admin.attendance.get-students") }}?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (!data.students) {
                    throw new Error('Invalid response format');
                }
                populateStudentsTable(data.students, data.existingAttendance || {});
                document.getElementById('formClassId').value = classId;
                document.getElementById('formAcademicYearId').value = academicYearId;
                document.getElementById('formSubjectId').value = subjectId || '';
                document.getElementById('formAttendanceDate').value = attendanceDate;
                document.getElementById('studentsCard').style.display = 'block';
                document.getElementById('noDataMessage').style.display = 'none';

                // Reinitialize Lucide icons after DOM update
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading students: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i data-lucide="download" style="width: 14px;" class="me-2"></i>Load Students';

                // Reinitialize Lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
    });

    function populateStudentsTable(students, existingAttendance) {
        const tbody = document.getElementById('studentsTableBody');
        tbody.innerHTML = '';

        if (!students || students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">No students found for this class and year.</td></tr>';
            return;
        }

        students.forEach((levelData, index) => {
            const student = levelData.student;
            const existing = existingAttendance[student.id] || null;
            const status = existing ? existing.status : 'present';
            const remarks = existing ? (existing.remarks || '') : '';

            const row = document.createElement('tr');
            row.innerHTML = `
            <td>
                <strong>${student.first_name} ${student.last_name}</strong>
                <br><small class="text-muted">${student.student_id}</small>
            </td>
            <td>
                <select name="attendance[${index}][status]" class="form-select form-select-sm" required>
                    <option value="present" ${status === 'present' ? 'selected' : ''}>Present</option>
                    <option value="absent" ${status === 'absent' ? 'selected' : ''}>Absent</option>
                    <option value="late" ${status === 'late' ? 'selected' : ''}>Late</option>
                    <option value="excused" ${status === 'excused' ? 'selected' : ''}>Excused</option>
                </select>
            </td>
            <td>
                <input type="text" name="attendance[${index}][remarks]" class="form-control form-control-sm" 
                       placeholder="Optional notes" value="${remarks}">
            </td>
            <input type="hidden" name="attendance[${index}][student_id]" value="${student.id}">
            <input type="hidden" name="attendance[${index}][level_data_id]" value="${levelData.id}">
        `;
            tbody.appendChild(row);
        });
    }

    document.getElementById('resetForm').addEventListener('click', function() {
        document.getElementById('classSelectionForm').reset();
        document.getElementById('studentsCard').style.display = 'none';
        document.getElementById('noDataMessage').style.display = 'block';
    });
</script>
@endpush

@endsection