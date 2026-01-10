@forelse($feeStructures as $structure)
    <tr>
        <td class="align-middle"><span class="fw-bold">{{ $structure->feeCategory->name ?? 'N/A' }}</span></td>
        <td class="align-middle"><span class="badge bg-light text-secondary border">{{ $structure->academicYear->name ?? 'N/A' }}</span></td>
        <td class="align-middle">{{ $structure->academicPeriod->name ?? 'N/A' }}</td>
        <td class="align-middle">{{ $structure->class->name ?? 'N/A' }}</td>
        <td class="align-middle fw-bold">{{ number_format($structure->amount, 2) }}</td>
        <td class="text-end align-middle">
             </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-5 text-muted">No fee structures found.</td>
    </tr>
@endforelse