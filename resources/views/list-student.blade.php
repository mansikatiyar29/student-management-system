<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --bg: #f8fafc;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
        }

        body {
            background-color: var(--bg);
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--text);
            padding-bottom: 50px;
        }

        .page-shell { padding: 32px 0; }
        
        /* Modern Card Styling */
        .filter-card { 
            background: #ffffff; 
            border: 1px solid var(--border); 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border-radius: 16px; 
            padding: 24px;
        }

        .page-title { font-weight: 800; letter-spacing: -0.02em; }
        
        .form-label { 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            font-weight: 700; 
            color: var(--muted); 
            margin-bottom: 8px;
        }

        /* Modern Inputs */
        .form-control, .form-select {
            border-radius: 10px !important;
            border: 1px solid var(--border) !important;
            padding: 10px 14px !important;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
        }

        .input-shell .search-icon { color: var(--muted); }
        .input-shell .form-control { padding-left: 40px !important; }

        /* Buttons */
        .btn-primary { 
            background: var(--primary) !important; 
            border-radius: 10px !important; 
            font-weight: 600; 
            padding: 10px 20px; 
        }
        
        .btn-outline-primary { 
            border-color: var(--border) !important; 
            color: var(--primary) !important; 
            border-radius: 10px !important; 
        }

        /* Table */
        .student-table thead th { 
            background: #f1f5f9; 
            font-size: 0.7rem; 
            text-transform: uppercase;
        }

        .hero-band {
            background: linear-gradient(135deg, #ffffff 0%, #eef2ff 100%);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        }

        .action-toolbar {
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-soft {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #dbe4ff;
        }

        .btn-soft:hover {
            background: #e0e7ff;
            color: #312e81;
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success shadow-sm border-0 rounded-4 mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger shadow-sm border-0 rounded-4 mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="hero-band p-4 p-lg-5 mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div>
                    <h1 class="page-title mb-2">Student List</h1>
                    <p class="text-muted mb-0">Manage your student records, import CSV data, export the current list, and search instantly.</p>
                </div>
                <div class="d-flex action-toolbar">
                    <a href="{{ route('students.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-2"></i>Add Student
                    </a>
                    <button type="button" class="btn btn-soft" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fa-solid fa-file-arrow-up me-2"></i>Import CSV
                    </button>
                    <a href="{{ route('students.export') }}" id="exportStudentsBtn" class="btn btn-outline-primary">
                        <i class="fa-solid fa-file-arrow-down me-2"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="filter-card mb-4">
            <div class="row g-3 align-items-start">
                <div class="col-lg-2 col-md-3">
                    <div class="filter-group">
                        <label for="perPageSelect" class="form-label">Rows</label>
                        <select name="per_page" id="perPageSelect" class="form-select">
                            <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-4 col-md-5">
                    <div class="filter-group">
                        <label for="search" class="form-label">Search</label>
                        <form action="{{ route('students.search') }}" method="GET" id="searchForm" class="m-0">
                            <div class="input-shell position-relative">
                                <i class="fa-solid fa-magnifying-glass search-icon position-absolute" style="left:14px; top:12px;"></i>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name prefix..." value="{{ request('search') }}">
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <form action="{{ route('students.search') }}" method="GET" id="filterForm" class="m-0">
                        <div class="filter-group">
                            <label class="form-label">Date range</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="date" name="from_date" id="fromDate" class="form-control" value="{{ request('from_date') }}">
                                <input type="date" name="to_date" id="toDate" class="form-control" value="{{ request('to_date') }}">
                                <button class="btn btn-outline-primary"><i class="fa-solid fa-filter"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="student-results">
            @include('student-table', ['students' => $students])
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold" id="importModalLabel">Import Students</h5>
                    <p class="text-muted mb-0 small">Upload a CSV file with name, email, and phone columns.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" class="d-grid gap-3">
                    @csrf
                    <input type="file" name="student_file" class="form-control" accept=".csv,text/csv" required>
                    @error('student_file')
                        <div class="alert alert-danger py-2 px-3 mb-0 small">{{ $message }}</div>
                    @enderror
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import CSV</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const searchForm = document.getElementById('searchForm');
        const filterForm = document.getElementById('filterForm');
        const perPageSelect = document.getElementById('perPageSelect');
        const fromDate = document.getElementById('fromDate');
        const toDate = document.getElementById('toDate');
        const resultsContainer = document.getElementById('student-results');
        const ajaxUrl = @json(route('students.search'));
        const exportBaseUrl = @json(route('students.export'));
        const exportButton = document.getElementById('exportStudentsBtn');
        let searchTimer = null;
        let activeRequest = null;

        const buildUrl = (page = 1) => {
            const url = new URL(ajaxUrl, window.location.origin);
            const searchValue = searchInput.value.trim();

            if (searchValue) {
                url.searchParams.set('search', searchValue);
            }

            if (perPageSelect.value) {
                url.searchParams.set('per_page', perPageSelect.value);
            }

            if (fromDate.value) {
                url.searchParams.set('from_date', fromDate.value);
            }

            if (toDate.value) {
                url.searchParams.set('to_date', toDate.value);
            }

            url.searchParams.set('page', page);
            return url.toString();
        };

        const syncExportLink = () => {
            const url = new URL(exportBaseUrl, window.location.origin);
            const searchValue = searchInput.value.trim();

            if (searchValue) {
                url.searchParams.set('search', searchValue);
            }

            if (perPageSelect.value) {
                url.searchParams.set('per_page', perPageSelect.value);
            }

            if (fromDate.value) {
                url.searchParams.set('from_date', fromDate.value);
            }

            if (toDate.value) {
                url.searchParams.set('to_date', toDate.value);
            }

            exportButton.href = url.toString();
        };

        const runSearch = async (page = 1) => {
            syncExportLink();

            if (activeRequest) {
                activeRequest.abort();
            }

            const requestController = new AbortController();
            activeRequest = requestController;

            try {
                resultsContainer.classList.add('opacity-50');

                const response = await fetch(buildUrl(page), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: requestController.signal,
                });

                const data = await response.json();
                resultsContainer.innerHTML = data.html;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Student search failed:', error);
                }
            } finally {
                if (activeRequest === requestController) {
                    resultsContainer.classList.remove('opacity-50');
                }
            }
        };

        searchForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            runSearch(1);
        });

        filterForm?.addEventListener('submit', (e) => {
            e.preventDefault();
            runSearch(1);
        });

        searchInput?.addEventListener('input', () => {
            syncExportLink();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => runSearch(1), 200);
        });

        perPageSelect?.addEventListener('change', () => {
            syncExportLink();
            runSearch(1);
        });

        fromDate?.addEventListener('change', () => {
            syncExportLink();
            runSearch(1);
        });

        toDate?.addEventListener('change', () => {
            syncExportLink();
            runSearch(1);
        });

        resultsContainer?.addEventListener('click', (event) => {
            const pageLink = event.target.closest('.page-link');

            if (!pageLink) {
                return;
            }

            const parentItem = pageLink.closest('.page-item');

            if (!parentItem || parentItem.classList.contains('disabled') || parentItem.classList.contains('active')) {
                return;
            }

            const href = pageLink.getAttribute('href');

            if (!href) {
                return;
            }

            event.preventDefault();

            const pageUrl = new URL(href, window.location.origin);
            runSearch(pageUrl.searchParams.get('page') || 1);
        });

        syncExportLink();
    });
</script>
@if($errors->has('student_file'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('importModal');
        if (modalElement) {
            const importModal = new bootstrap.Modal(modalElement);
            importModal.show();
        }
    });
</script>
@endif
</body>
</html>  
