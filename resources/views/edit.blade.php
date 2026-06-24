<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f7fb;
        }

        .page-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 24px;
        }

        .form-panel {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d7dee8;
            border-radius: 12px;
            padding: 24px;
        }

        .form-title {
            margin-bottom: 6px;
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
        }

        .form-subtitle {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .form-control {
            min-height: 44px;
            border-radius: 10px;
            border-color: #d7dee8;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: rgba(37, 99, 235, 0.45);
            box-shadow: 0 0 0 0.15rem rgba(37, 99, 235, 0.08) !important;
        }

        .btn-primary {
            min-height: 44px;
            border-radius: 10px;
        }

        @media (max-width: 576px) {
            .page-shell {
                padding: 16px;
            }

            .form-panel {
                padding: 20px;
            }
        }
    </style>

</head>

<body>
<div class="page-shell">
    <div class="form-panel">
        <h1 class="form-title">Update Student Data</h1>
        <p class="form-subtitle">Edit the selected student record.</p>

        <form action="/edit/{{ $data->id }}" method="POST" class="d-grid gap-3">
        @csrf

            <input type="text"
                   name="name"
                   value="{{ $data->name }}"
                   class="form-control"
                   placeholder="Enter Name">

            <input type="email"
                   name="email"
                   value="{{ $data->email }}"
                   class="form-control"
                   placeholder="Enter Email">

            <input type="tel"
                   name="phone"
                   value="{{ $data->phone }}"
                   class="form-control"
                   placeholder="Enter Phone">

            <button type="submit" class="btn btn-primary">
                Update Student
            </button>
        </form>
    </div>
</div>
</body>
</html>
