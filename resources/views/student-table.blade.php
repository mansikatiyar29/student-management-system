<div class="main-card">
    <div class="table-wrap">
        <table class="table student-table align-middle">
            <thead>
                <tr>
                    <th scope="col" class="ps-4" style="width: 72px;">
                        <input class="form-check-input" type="checkbox" id="masterCheckbox">
                    </th>
                    <th scope="col">S.No.</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Created</th>
                    <th scope="col">Updated</th>
                    <th scope="col" class="text-end pe-4">Operation</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="ps-4">
                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $student->id }}">
                        </td>
                        <td>
                            <span class="student-chip">
                                {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                            </span>
                        </td>
                        <td>
                            <div class="student-name">{{ $student->name }}</div>
                        </td>
                        <td>
                            <div class="student-email">{{ $student->email }}</div>
                        </td>
                        <td>
                            <span class="student-chip">
                                <i class="fa-solid fa-phone"></i>
                                {{ $student->phone }}
                            </span>
                        </td>
                        <td>
                            <div class="student-meta small fw-semibold">
                                <i class="fa-regular fa-calendar-days me-1"></i>
                                {{ $student->created_at ? $student->created_at->format('d M Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <div class="student-meta small fw-semibold">
                                <i class="fa-regular fa-calendar-check me-1"></i>
                                {{ $student->updated_at ? $student->updated_at->format('d M Y') : 'N/A' }}
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex align-items-center gap-2 action-group">
                                <a href="{{ route('students.edit', $student->id) }}"
                                   class="btn btn-primary-subtle text-primary border-0"
                                   title="Edit Student Record">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('students.delete', $student->id) }}"
                                      method="POST"
                                      class="m-0"
                                      onsubmit="return confirm('Are you sure you want to delete this student record?')">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-danger-subtle text-danger border-0"
                                            title="Delete Student">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state text-center">
                                <div class="empty-icon">
                                    <i class="fa-solid fa-folder-open fs-2"></i>
                                </div>
                                <h5 class="fw-bold mb-2">No students found</h5>
                                <p class="text-muted mb-0">Try a different search term or clear the filters to see more records.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 p-3" id="pagination-wrapper">
        <div class="text-muted small fw-semibold">
            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} results.
        </div>

        @if($students->hasPages())
            <nav aria-label="Student pagination">
                <ul class="pagination pagination-sm mb-0">
                    @if ($students->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">&lt;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev">&lt;</a>
                        </li>
                    @endif

                    @php
                        $startPage = max(1, $students->currentPage() - 1);
                        $endPage = min($students->lastPage(), $students->currentPage() + 1);
                    @endphp

                    @foreach ($students->getUrlRange($startPage, $endPage) as $page => $url)
                        <li class="page-item {{ $page == $students->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach

                    @if ($students->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next">&gt;</a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link">&gt;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div> 