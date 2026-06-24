<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function create()
    {
        return view('add-student');
    }

    public function add(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required',
        ]);

        $student = new Student();
        $student->name = $request->name;
        $student->email = $request->email;
        $student->phone = $request->phone;
        $student->created_at = $request->filled('created_at') ? Carbon::parse($request->created_at) : Carbon::now();
        $student->updated_at = Carbon::now();
        $student->save();

        return redirect()->route('students.search')->with('success', 'New student registered successfully!');
    }

    public function list()
    {
        $studentsData = $this->buildStudentResults(request());

        return view('list-student', ['students' => $studentsData]);
    }

    public function delete($id)
    {
        Student::destroy($id);

        return redirect()->route('students.search')->with('success', 'Student record deleted successfully!');
    }

    public function edit($id)
    {
        $student = Student::find($id);

        return view('edit', ['data' => $student]);
    }

    public function editStudent(Request $request, $id)
    {
        $student = Student::find($id);
        $student->name = $request->name;
        $student->email = $request->email;
        $student->phone = $request->phone;
        $student->save();

        return redirect()->route('students.search')->with('success', 'Student profile updated successfully!');
    }

    public function search(Request $request)
    {
        if ($request->ajax() || $request->expectsJson()) {
            return $this->ajaxSearch($request);
        }

        $studentsData = $this->buildStudentResults($request);

        return view('list-student', ['students' => $studentsData]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'student_file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('student_file');
        $filePath = $file->getRealPath();

        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);

            if ($firstLine === false || trim($firstLine) === '') {
                fclose($handle);

                return redirect()->back()->with('error', 'The uploaded CSV file is empty.');
            }

            $delimiter = $this->detectCsvDelimiter($firstLine);
            rewind($handle);

            $header = fgetcsv($handle, 0, $delimiter);

            if (!$header) {
                fclose($handle);

                return redirect()->back()->with('error', 'The uploaded CSV file does not contain a readable header row.');
            }

            $header = array_map(function ($h) {
                $h = preg_replace('/^\xEF\xBB\xBF/', '', (string) $h);

                return strtolower(trim($h));
            }, $header);

            $requiredColumns = ['name', 'email', 'phone'];

            foreach ($requiredColumns as $requiredColumn) {
                if (!in_array($requiredColumn, $header, true)) {
                    fclose($handle);

                    return redirect()->back()->with('error', 'CSV header must include name, email, and phone columns.');
                }
            }

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            DB::transaction(function () use ($handle, $header, $delimiter, &$importedCount, &$updatedCount, &$skippedCount) {
                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if ($data === [null] || count(array_filter($data, static fn ($value) => $value !== null && trim((string) $value) !== '')) === 0) {
                        continue;
                    }

                    if (count($header) !== count($data)) {
                        $skippedCount++;
                        continue;
                    }

                    $row = array_combine($header, $data);

                    if (!$row) {
                        $skippedCount++;
                        continue;
                    }

                    $name = isset($row['name']) ? trim((string) $row['name']) : null;
                    $email = isset($row['email']) ? trim((string) $row['email']) : null;
                    $phone = isset($row['phone']) ? trim((string) $row['phone']) : null;

                    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $skippedCount++;
                        continue;
                    }

                    $csvDate = Carbon::now();
                    $rawDateString = null;

                    if (isset($row['created_at']) && trim((string) $row['created_at']) !== '') {
                        $rawDateString = trim((string) $row['created_at']);
                    } elseif (isset($row['date']) && trim((string) $row['date']) !== '') {
                        $rawDateString = trim((string) $row['date']);
                    }

                    if ($rawDateString) {
                        try {
                            $csvDate = Carbon::parse($rawDateString);
                        } catch (\Throwable $e) {
                            $csvDate = Carbon::now();
                        }
                    }

                    $student = Student::where('email', $email)->first();

                    if ($student) {
                        $updatedCount++;
                    } else {
                        $student = new Student();
                        $student->email = $email;
                        $importedCount++;
                    }

                    $student->name = $name;
                    $student->phone = $phone;
                    $student->created_at = $csvDate;
                    $student->updated_at = Carbon::now();
                    $student->save();
                }
            });

            fclose($handle);

            $message = "Import completed. {$importedCount} new records added, {$updatedCount} existing records updated.";

            if ($skippedCount > 0) {
                $message .= " {$skippedCount} rows were skipped because they were invalid or incomplete.";
            }

            return redirect()->route('students.search')->with('success', $message);
        }

        return redirect()->back()->with('error', 'Unable to read the uploaded file safely.');
    }

    public function export(Request $request)
    {
        $fileName = 'students_directory_' . Carbon::now()->format('Y_m_d_His') . '.csv';
        $students = $this->buildStudentQuery($request);

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Created At']);

            $students->chunk(500, function ($records) use ($file) {
                foreach ($records as $student) {
                    fputcsv($file, [
                        $student->id,
                        $student->name,
                        $student->email,
                        $student->phone,
                        $student->created_at ? $student->created_at->format('Y-m-d') : '',
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ajaxSearch(Request $request)
    {
        $studentsData = $this->buildStudentResults($request);

        return response()->json([
            'html' => view('student-table', [
                'students' => $studentsData,
            ])->render(),
        ]);
    }

    private function buildStudentResults(Request $request)
    {
        $students = $this->buildStudentQuery($request);
        $perPage = $request->input('per_page', 5);

        return $students->paginate($perPage)->appends($request->all());
    }

    private function buildStudentQuery(Request $request)
    {
        $students = Student::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $searchLower = mb_strtolower($search, 'UTF-8');

            $students->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%'])
                ->orderByRaw(
                    'CASE WHEN LOWER(name) LIKE ? THEN 0 ELSE LOCATE(?, LOWER(name)) END ASC',
                    [$searchLower . '%', $searchLower]
                )
                ->orderBy('name', 'asc');
        } else {
            $students->orderBy('created_at', 'desc');
        }

        if ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $students->where('created_at', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $students->where('created_at', '<=', $toDate);
        }

        $allowedSortColumns = ['id', 'name', 'phone', 'created_at'];
        if ($request->filled('sort_by')) {
            $sortBy = in_array($request->input('sort_by'), $allowedSortColumns) ? $request->input('sort_by') : 'created_at';
            $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';
            $students->orderBy($sortBy, $sortOrder);
        }

        return $students;
    }

    private function detectCsvDelimiter(string $line): string
    {
        $candidates = [',' => substr_count($line, ','), ';' => substr_count($line, ';'), "\t" => substr_count($line, "\t"), '|' => substr_count($line, '|')];
        arsort($candidates);

        $delimiter = array_key_first($candidates);

        return $delimiter ?: ',';
    }
}
