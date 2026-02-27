<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpdRegistration;
use App\Models\IpdRegistration;
use App\Models\Department;
use App\Models\BedDistribution;
use App\Models\DietPlans;
use Illuminate\Http\Request;


class ReportsController extends Controller
{
    public function opdReports(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $adminDeptId = admin_dept_id();
        $departmentsQuery = Department::where('status', 1);
        if ($adminDeptId) {
            $departmentsQuery->where('id', $adminDeptId);
        }
        $departments = $departmentsQuery->orderBy('name')->get(['id', 'name']);

        $datewiseData = [];
        $fromDate = null;
        $toDate = null;
        $filterDeptId = null;
        $filterError = null;

        if ($request->isMethod('post') && $request->input()) {
            $fromStr = trim($request->input('from_date', ''));
            $toStr = trim($request->input('to_date', ''));
            $filterDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
            if ($adminDeptId) {
                $filterDeptId = $adminDeptId;
            }

            $fromObj = $fromStr ? \DateTime::createFromFormat('m/d/Y', $fromStr) : null;
            $toObj = $toStr ? \DateTime::createFromFormat('m/d/Y', $toStr) : null;
            $today = date('Y-m-d');

            if (!$fromObj || !$toObj) {
                $filterError = 'Please select both From Date and To Date.';
            } elseif ($fromObj->format('Y-m-d') > $toObj->format('Y-m-d')) {
                $filterError = 'From Date cannot be greater than To Date.';
            } elseif ($fromObj->format('Y-m-d') > $today || $toObj->format('Y-m-d') > $today) {
                $filterError = 'Dates cannot be greater than today.';
            } else {
                $fromDb = $fromObj->format('Y-m-d');
                $toDb = $toObj->format('Y-m-d');

                $query = OpdRegistration::with(['department', 'disease'])
                    ->whereBetween('date', [$fromDb, $toDb]);
                if ($filterDeptId) {
                    $query->where('dept_id', $filterDeptId);
                }
                $opdRecords = $query->orderBy('date')->orderBy('opd_number')->get();

                foreach ($opdRecords->groupBy(function ($r) {
                    return $r->date->format('Y-m-d');
                }) as $dateKey => $records) {
                    $records = $records->values();
                    $deptSummary = [];
                    foreach ($records->groupBy('dept_id') as $deptId => $deptRecords) {
                        $first = $deptRecords->first();
                        $deptName = ($deptId && $first->department) ? $first->department->name : 'General';
                        $newMale = $deptRecords->where('register_type', 'New')->where('gender', 'Male')->count();
                        $newFemale = $deptRecords->where('register_type', 'New')->where('gender', 'Female')->count();
                        $oldMale = $deptRecords->where('register_type', 'Old')->where('gender', 'Male')->count();
                        $oldFemale = $deptRecords->where('register_type', 'Old')->where('gender', 'Female')->count();
                        $deptSummary[] = (object) [
                            'department' => $deptName,
                            'new_male' => $newMale,
                            'new_female' => $newFemale,
                            'old_male' => $oldMale,
                            'old_female' => $oldFemale,
                        ];
                    }
                    $datewiseData[$dateKey] = (object) [
                        'opd_records' => $records,
                        'dept_summary' => collect($deptSummary),
                    ];
                }
                ksort($datewiseData);
                $fromDate = $fromStr;
                $toDate = $toStr;
            }
        }

        return view('admin.reports.opd-reports', compact(
            'departments', 'datewiseData', 'fromDate', 'toDate', 'filterDeptId', 'filterError'
        ));
    }

    public function opdReportsExport(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $fromStr = $request->input('from_date', '');
        $toStr = $request->input('to_date', '');
        $filterDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
        $adminDeptId = admin_dept_id();
        if ($adminDeptId) {
            $filterDeptId = $adminDeptId;
        }

        $fromObj = $fromStr ? \DateTime::createFromFormat('m/d/Y', $fromStr) : null;
        $toObj = $toStr ? \DateTime::createFromFormat('m/d/Y', $toStr) : null;
        if (!$fromObj || !$toObj) {
            return redirect()->route('admin.opd-reports')->with('error', 'Please apply filters (From Date and To Date) before exporting.');
        }

        $fromDb = $fromObj->format('Y-m-d');
        $toDb = $toObj->format('Y-m-d');

        $query = OpdRegistration::with(['department', 'disease'])
            ->whereBetween('date', [$fromDb, $toDb]);
        if ($filterDeptId) {
            $query->where('dept_id', $filterDeptId);
        }
        $opdRecords = $query->orderBy('date')->orderBy('opd_number')->get();

        $datewiseData = [];
        foreach ($opdRecords->groupBy(function ($r) {
            return $r->date->format('Y-m-d');
        }) as $dateKey => $records) {
            $records = $records->values();
            $deptSummary = [];
            foreach ($records->groupBy('dept_id') as $deptId => $deptRecords) {
                $first = $deptRecords->first();
                $deptName = ($deptId && $first->department) ? $first->department->name : 'General';
                $newMale = $deptRecords->where('register_type', 'New')->where('gender', 'Male')->count();
                $newFemale = $deptRecords->where('register_type', 'New')->where('gender', 'Female')->count();
                $deptSummary[] = ['department' => $deptName, 'new_male' => $newMale, 'new_female' => $newFemale];
            }
            $datewiseData[$dateKey] = ['opd_records' => $records, 'dept_summary' => $deptSummary];
        }
        ksort($datewiseData);

        if (empty($datewiseData)) {
            return redirect()->route('admin.opd-reports')->with('error', 'No data to export for the selected filters.');
        }

        $filename = 'opd-reports-' . $fromDb . '-to-' . $toDb . '.csv';

        $output = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        foreach ($datewiseData as $dateKey => $dayData) {
            $dateFormatted = \Carbon\Carbon::parse($dateKey)->format('d-m-Y');
            fputcsv($output, ['Date: ' . $dateFormatted]);
            fputcsv($output, []);

            fputcsv($output, ['S.No.', 'OPD No.', 'Patient Name', 'Age / Gender', 'Location', 'Department', 'Diagnosis', 'Status']);
            foreach ($dayData['opd_records'] as $idx => $opd) {
                fputcsv($output, [
                    $idx + 1,
                    $opd->opd_number ?? '—',
                    $opd->patient_name ?? '—',
                    ($opd->patient_age ?? '—') . ' / ' . ($opd->gender ?? '—'),
                    $opd->address ?? '—',
                    $opd->department ? $opd->department->name : '—',
                    $opd->disease ? $opd->disease->name : '—',
                    $opd->register_type ?? '—',
                ]);
            }
            fputcsv($output, []);
            fputcsv($output, ['S.No.', 'Department', 'New Patient Male', 'New Patient Female']);
            foreach ($dayData['dept_summary'] as $dIdx => $r) {
                fputcsv($output, [$dIdx + 1, $r['department'], $r['new_male'], $r['new_female']]);
            }
            fputcsv($output, []);
        }
        fclose($output);
        exit;
    }

    public function searchPatient(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $query = OpdRegistration::query();
        $adminDeptId = admin_dept_id();
        if ($adminDeptId) {
            $query->where('dept_id', $adminDeptId);
        }

        $hasFilters = $request->filled('patient_name') || $request->filled('from_date') || $request->filled('to_date');

        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . trim($request->patient_name) . '%');
        }
        if ($request->filled('from_date')) {
            $from = \DateTime::createFromFormat('m/d/Y', trim($request->from_date));
            if ($from) {
                $query->where('date', '>=', $from->format('Y-m-d'));
            }
        }
        if ($request->filled('to_date')) {
            $to = \DateTime::createFromFormat('m/d/Y', trim($request->to_date));
            if ($to) {
                $query->where('date', '<=', $to->format('Y-m-d'));
            }
        }

        $records = $hasFilters ? $query->orderBy('date', 'desc')->orderBy('opd_number')->paginate(20) : null;

        return view('admin.reports.search-patient', compact('records'));
    }

    public function searchPatientExport(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $query = OpdRegistration::query();
        $adminDeptId = admin_dept_id();
        if ($adminDeptId) {
            $query->where('dept_id', $adminDeptId);
        }

        $hasFilters = $request->filled('patient_name') || $request->filled('from_date') || $request->filled('to_date');
        if (!$hasFilters) {
            return redirect()->route('admin.search-patient')->with('error', 'Please apply filters before exporting.');
        }

        if ($request->filled('patient_name')) {
            $query->where('patient_name', 'like', '%' . trim($request->patient_name) . '%');
        }
        if ($request->filled('from_date')) {
            $from = \DateTime::createFromFormat('m/d/Y', trim($request->from_date));
            if ($from) {
                $query->where('date', '>=', $from->format('Y-m-d'));
            }
        }
        if ($request->filled('to_date')) {
            $to = \DateTime::createFromFormat('m/d/Y', trim($request->to_date));
            if ($to) {
                $query->where('date', '<=', $to->format('Y-m-d'));
            }
        }

        $records = $query->orderBy('date', 'desc')->orderBy('opd_number')->get();
        if ($records->isEmpty()) {
            return redirect()->route('admin.search-patient')->with('error', 'No data to export for the selected filters.');
        }

        $filename = 'search-patient-' . date('Y-m-d-His') . '.csv';
        $output = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        fputcsv($output, ['S.No.', 'OPD No.', 'Patient Name', 'Fath / Husb Name', 'Age / Gender', 'Category', 'Mobile No.', 'Address']);
        foreach ($records as $idx => $row) {
            fputcsv($output, [
                $idx + 1,
                $row->opd_number ?? '—',
                $row->patient_name ?? '—',
                $row->fath_husb_name ?? '—',
                ($row->patient_age ?? '—') . ' / ' . ($row->gender ?? '—'),
                $row->register_type ?? '—',
                '—',
                $row->address ?? '—',
            ]);
        }
        fclose($output);
        exit;
    }

    public function ipdReports(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        
        $adminDeptId = admin_dept_id();
        $departmentsQuery = Department::where('status', 1);
        
        if ($adminDeptId) {
            $departmentsQuery->where('id', $adminDeptId);
        }
        
        $departments = $departmentsQuery->orderBy('name')->get(['id', 'name']);
        
        $datewiseData = [];
        $fromDate = null;
        $toDate = null;
        $filterDeptId = null;
        $filterError = null;
        
        if ($request->isMethod('post') && $request->input()) {
        
            $fromStr = trim($request->input('from_date', ''));
            $toStr = trim($request->input('to_date', ''));
            $filterDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
        
            if ($adminDeptId) {
                $filterDeptId = $adminDeptId;
            }
        
            $fromObj = $fromStr ? \DateTime::createFromFormat('m/d/Y', $fromStr) : null;
            $toObj = $toStr ? \DateTime::createFromFormat('m/d/Y', $toStr) : null;
            $today = date('Y-m-d');
        
            if (!$fromObj || !$toObj) {
                $filterError = 'Please select both From Date and To Date.';
            } elseif ($fromObj->format('Y-m-d') > $toObj->format('Y-m-d')) {
                $filterError = 'From Date cannot be greater than To Date.';
            } elseif ($fromObj->format('Y-m-d') > $today || $toObj->format('Y-m-d') > $today) {
                $filterError = 'Dates cannot be greater than today.';
            } else {
        
                $fromDb = $fromObj->format('Y-m-d');
                $toDb = $toObj->format('Y-m-d');
        
                $query = IpdRegistration::with(['opd'])
                ->whereBetween('date', [$fromDb, $toDb]);
        
                if ($filterDeptId) {
                    $query->where('dept_id', $filterDeptId);
                }
        
                $ipdRecords = $query->orderBy('date')->get();
        
                foreach ($ipdRecords->groupBy(function ($r) {
                    return \Carbon\Carbon::parse($r->date)->format('Y-m-d');
                }) as $dateKey => $records) {
        
                    $records = $records->values();
                    $deptSummary = [];
        
                    foreach ($records->groupBy('dept_id') as $deptId => $deptRecords) {
        
                        $first = $deptRecords->first();
                        $deptName = ($deptId && $first->department)
                            ? $first->department->name
                            : 'General';
        
                        $maleCount = $deptRecords->where('gender', 'Male')->count();
                        $femaleCount = $deptRecords->where('gender', 'Female')->count();

                        
                        
                        $oldMale = $deptRecords->filter(function ($r) {
                            return $r->opd && $r->opd->register_type == 'Old' && $r->gender == 'Male';
                        })->count();
                        
                        $oldFemale = $deptRecords->filter(function ($r) {
                            return $r->opd && $r->opd->register_type == 'Old' && $r->gender == 'Female';
                        })->count();
                        
                        $newMale = $deptRecords->filter(function ($r) {
                            return $r->opd && $r->opd->register_type == 'New' && $r->gender == 'Male';
                        })->count();
                        
                        $newFemale = $deptRecords->filter(function ($r) {
                            return $r->opd && $r->opd->register_type == 'New' && $r->gender == 'Female';
                        })->count();
                        
                        $totalCount = $deptRecords->count();
                                
                        $deptSummary[] = (object) [
                            'department' => $deptName,
                            'male' => $maleCount,
                            'female' => $femaleCount,
                            'total' => $totalCount,
                            'new_male' => $newMale,
                            'new_female' => $newFemale,
                            'old_male' => $oldMale,
                            'old_female' => $oldFemale,
                        ];
                    }
        
                    $datewiseData[$dateKey] = (object) [
                        'ipd_records' => $records,
                        'dept_summary' => collect($deptSummary),
                    ];
                }
        
                ksort($datewiseData);
        
                $fromDate = $fromStr;
                $toDate = $toStr;
            }
        }
        
        return view('admin.reports.ipd-reports', compact(
            'departments',
            'datewiseData',
            'fromDate',
            'toDate',
            'filterDeptId',
            'filterError'
        ));
    }
    
    public function ipdReportsExport(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
    
        $fromStr = $request->input('from_date', '');
        $toStr = $request->input('to_date', '');
        $filterDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
    
        $adminDeptId = admin_dept_id();
        if ($adminDeptId) {
            $filterDeptId = $adminDeptId;
        }
    
        $fromObj = $fromStr ? \DateTime::createFromFormat('m/d/Y', $fromStr) : null;
        $toObj = $toStr ? \DateTime::createFromFormat('m/d/Y', $toStr) : null;
    
        if (!$fromObj || !$toObj) {
            return redirect()->route('admin.ipd-reports')
                ->with('error', 'Please apply filters (From Date and To Date) before exporting.');
        }
    
        $fromDb = $fromObj->format('Y-m-d');
        $toDb = $toObj->format('Y-m-d');
    
        $query = IpdRegistration::with(['opd'])
            ->whereBetween('date', [$fromDb, $toDb]);
    
        if ($filterDeptId) {
            $query->where('dept_id', $filterDeptId);
        }
    
        $ipdRecords = $query->orderBy('date')->get();
    
        $datewiseData = [];
    
        foreach ($ipdRecords->groupBy(function ($r) {
            return \Carbon\Carbon::parse($r->date)->format('Y-m-d');
        }) as $dateKey => $records) {
    
            $records = $records->values();
            $deptSummary = [];
    
            foreach ($records->groupBy('dept_id') as $deptId => $deptRecords) {
    
                $first = $deptRecords->first();
                $deptName = ($deptId && $first->opd->department)
                    ? $first->opd->department->name
                    : 'General';
    
                $maleCount = $deptRecords->where('gender', 'Male')->count();
                $femaleCount = $deptRecords->where('gender', 'Female')->count();
                $totalCount = $deptRecords->count();
    
                $oldMale = $deptRecords->filter(function ($r) {
                    return $r->opd && strtoupper($r->opd->register_type) == 'Old' && $r->gender == 'Male';
                })->count();
    
                $oldFemale = $deptRecords->filter(function ($r) {
                    return $r->opd && strtoupper($r->opd->register_type) == 'Old' && $r->gender == 'Female';
                })->count();
    
                $newMale = $deptRecords->filter(function ($r) {
                    return $r->opd && strtoupper($r->opd->register_type) == 'New' && $r->gender == 'Male';
                })->count();
    
                $newFemale = $deptRecords->filter(function ($r) {
                    return $r->opd && strtoupper($r->opd->register_type) == 'New' && $r->gender == 'Female';
                })->count();
    
                $deptSummary[] = [
                    'department' => $deptName,
                    'male' => $maleCount,
                    'female' => $femaleCount,
                    'total' => $totalCount,
                    'new_male' => $newMale,
                    'new_female' => $newFemale,
                    'old_male' => $oldMale,
                    'old_female' => $oldFemale,
                ];
            }
    
            $datewiseData[$dateKey] = [
                'ipd_records' => $records,
                'dept_summary' => $deptSummary,
            ];
        }
    
        ksort($datewiseData);
    
        if (empty($datewiseData)) {
            return redirect()->route('admin.ipd-reports')
                ->with('error', 'No data to export for the selected filters.');
        }
    
        $filename = 'ipd-reports-' . $fromDb . '-to-' . $toDb . '.csv';
    
        $output = fopen('php://output', 'w');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        foreach ($datewiseData as $dateKey => $dayData) {
    
            $dateFormatted = \Carbon\Carbon::parse($dateKey)->format('d-m-Y');
    
            fputcsv($output, ['Date: ' . $dateFormatted]);
            fputcsv($output, []);
    
            // IPD Records Table
            fputcsv($output, [
                'S.No.',
                'IPD No.',
                'Patient Name',
                'Age / Gender',
                'Location',
                'Department',
                'Diagnosis'
            ]);
    
            foreach ($dayData['ipd_records'] as $idx => $ipd) {
    
                fputcsv($output, [
                    $idx + 1,
                    $ipd->ipd_number ?? '—',
                    $ipd->patient_name ?? '—',
                    ($ipd->patient_age ?? '—') . ' / ' . ($ipd->gender ?? '—'),
                    $ipd->address ?? '—',
                    $ipd->opd->department ? $ipd->opd->department->name : '—',
                    $ipd->diagnosis ? $ipd->diagnosis : '—',
                ]);
            }
    
            fputcsv($output, []);
    
            // Department Summary Table
            fputcsv($output, [
                'S.No.',
                'Department',
                'Male',
                'Female',
                'Total',
                'New Male',
                'New Female',
                'Old Male',
                'Old Female'
            ]);
    
            foreach ($dayData['dept_summary'] as $dIdx => $r) {
    
                fputcsv($output, [
                    $dIdx + 1,
                    $r['department'],
                    $r['male'],
                    $r['female'],
                    $r['total'],
                    $r['new_male'],
                    $r['new_female'],
                    $r['old_male'],
                    $r['old_female'],
                ]);
            }
    
            fputcsv($output, []);
        }
    
        fclose($output);
        exit;
    }
    public function dischargeReports(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        
        $query = IpdRegistration::with(['opd.department']);
        
        $adminDeptId = admin_dept_id();
        
        if ($adminDeptId) {
            $query->whereHas('opd', function ($q) use ($adminDeptId) {
                $q->where('dept_id', $adminDeptId);
            });
        }
        
        $hasFilters = $request->filled('from_date');
        
        if ($request->filled('from_date')) {
            $from = \DateTime::createFromFormat('m/d/Y', trim($request->from_date));
            if ($from) {
                $query->where('discharge_date', $from->format('Y-m-d'));
            }
        }
        
        $records = $hasFilters
            ? $query->orderBy('discharge_date', 'desc')
                    ->orderBy('opd_number')
                    ->paginate(20)
            : null;
        
        return view('admin.reports.discharge', compact('records'));
    }

    public function dischargeReportsExport(Request $request)
{
    if (!$request->session()->has('admin_email')) {
        return redirect('/admin/');
    }

    $query = IpdRegistration::with(['opd.department']);

    $adminDeptId = admin_dept_id();

    // Department filter (from OPD table)
    if ($adminDeptId) {
        $query->whereHas('opd', function ($q) use ($adminDeptId) {
            $q->where('dept_id', $adminDeptId);
        });
    }

    $hasFilters = $request->filled('from_date');

    if (!$hasFilters) {
        return redirect()->route('admin.discharge-reports')
            ->with('error', 'Please apply filters before exporting.');
    }

    // Filter by discharge date
    if ($request->filled('from_date')) {
        $from = \DateTime::createFromFormat('m/d/Y', trim($request->from_date));
        if ($from) {
            $query->where('discharge_date', $from->format('Y-m-d'));
        }
    }

    $records = $query->orderBy('discharge_date', 'desc')
                     ->orderBy('opd_number')
                     ->get();

    if ($records->isEmpty()) {
        return redirect()->route('admin.discharge-reports')
            ->with('error', 'No data to export for the selected filters.');
    }

    $filename = 'discharge-reports-' . date('Y-m-d-His') . '.csv';

    $output = fopen('php://output', 'w');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // Header Row (same as your table)
    fputcsv($output, [
        'S.No.',
        'OPD No.',
        'Patient Name',
        'Fath / Husb Name',
        'Age / Gender',
        'Address',
        'Admit Date',
        'Discharge Date',
        'Discharge Department',
        'Discharge Type'
    ]);

    foreach ($records as $idx => $row) {

        fputcsv($output, [
            $idx + 1,
            $row->opd_number ?? '—',
            $row->patient_name ?? '—',
            $row->fath_husb_name ?? '—',
            ($row->patient_age ?? '—') . ' / ' . ($row->gender ?? '—'),
            $row->address ?? '—',
            $row->date ? date('d-m-Y', strtotime($row->date)) : '—',
            $row->discharge_date ? date('d-m-Y', strtotime($row->discharge_date)) : '—',
            $row->opd && $row->opd->department ? $row->opd->department->name : '—',
            $row->discharge_type ?? '—',
        ]);
    }

    fclose($output);
    exit;
}

public function bedDistributionReports(Request $request)
{
    if (!$request->session()->has('admin_email')) {
        return redirect('/admin/');
    }

    $records = BedDistribution::with('department')
        ->where('status', 1)
        ->orderBy('department_id')
        ->get()
        ->groupBy('department_id')
        ->map(function ($deptRecords) {

            return $deptRecords->groupBy('gender')->map(function ($genderRecords) {

                return (object)[
                    'department' => $genderRecords->first()->department->name ?? '—',
                    'gender'     => $genderRecords->first()->gender ?? '—',
                    'total_bed'  => $genderRecords->count(),
                    'bed_numbers'=> $genderRecords->pluck('bed_no')->implode(', ')
                ];
            });

        })
        ->flatten(1); // flatten to single collection

    return view('admin.reports.bed-distribution', compact('records'));
}

public function bedDistributionReportsExport(Request $request)
{
    if (!$request->session()->has('admin_email')) {
        return redirect('/admin/');
    }

    $records = \App\Models\BedDistribution::with('department')
        ->where('status', 1)
        ->orderBy('department_id')
        ->get()
        ->groupBy('department_id')
        ->map(function ($deptRecords) {

            return $deptRecords->groupBy('gender')->map(function ($genderRecords) {

                return (object)[
                    'department' => $genderRecords->first()->department->name ?? '—',
                    'gender'     => $genderRecords->first()->gender ?? '—',
                    'total_bed'  => $genderRecords->count(),
                    'bed_numbers'=> $genderRecords->pluck('bed_no')->implode(', ')
                ];
            });

        })
        ->flatten(1)
        ->values();

    if ($records->isEmpty()) {
        return redirect()->route('admin.bed-distribution-reports')
            ->with('error', 'No data available to export.');
    }

    $filename = 'bed-distribution-' . date('Y-m-d-His') . '.csv';

    $output = fopen('php://output', 'w');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    // CSV Header
    fputcsv($output, [
        'S.No.',
        'Department',
        'Gender',
        'Total Bed',
        'Bed No.'
    ]);

    foreach ($records as $index => $row) {

        fputcsv($output, [
            $index + 1,
            $row->department,
            $row->gender == "M" ? "Male" : "Female",
            $row->total_bed,
            $row->bed_numbers
        ]);
    }

    fclose($output);
    exit;
}  

public function dietPlanReports(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        
        $query = DietPlans::with(['department']);
        
        $adminDeptId = admin_dept_id();
        
        if ($adminDeptId) {
            $query->where('dept_id', $adminDeptId);
        }
        
        $hasFilters = $request->filled('filter_date');
        
        if ($request->filled('filter_date')) {
            $from = \DateTime::createFromFormat('m/d/Y', trim($request->filter_date));
            if ($from) {
                $query->where('plan_date', $from->format('Y-m-d'));
            }
        }
        
        $records = $hasFilters
            ? $query->orderBy('plan_date', 'desc')
                    ->orderBy('ipd_no')
                    ->paginate(20)
            : null;
        
        return view('admin.reports.diet-plans', compact('records'));
    }
    public function dietPlanReportsExport(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
    
        $adminDeptId = admin_dept_id();
    
        if (!$request->filled('filter_date')) {
            return redirect()->route('admin.diet-chart-reports')
                ->with('error', 'Please apply date filter before exporting.');
        }
    
        $date = \DateTime::createFromFormat('m/d/Y', trim($request->filter_date));
    
        if (!$date) {
            return redirect()->route('admin.diet-chart-reports')
                ->with('error', 'Invalid date format.');
        }
    
        $query = DietPlans::with('department')
            ->where('plan_date', $date->format('Y-m-d'));
    
        // Department restriction (admin based)
        if ($adminDeptId) {
            $query->where('dept_id', $adminDeptId);
        }
    
        $records = $query->orderBy('plan_date', 'desc')
                         ->orderBy('ipd_no')
                         ->get();
    
        if ($records->isEmpty()) {
            return redirect()->route('admin.diet-chart-reports')
                ->with('error', 'No data to export for selected date.');
        }
    
        $filename = 'diet-plan-reports-' . date('Y-m-d-His') . '.csv';
    
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        $output = fopen('php://output', 'w');
    
        // Header Row
        fputcsv($output, [
            'S.No.',
            'Diet Date',
            'IPD No.',
            'OPD No.',
            'Patient Name',
            'Gender',
            'Department',
            'Morning',
            'Afternoon',
            'Evening'
        ]);
    
        foreach ($records as $index => $row) {
    
            fputcsv($output, [
                $index + 1,
                $row->plan_date ? date('d-m-Y', strtotime($row->plan_date)) : '—',
                $row->ipd_no ?? '—',
                $row->opd_no ?? '—',
                $row->patient_name ?? '—',
                $row->gendar ?? '—',
                $row->department->name ?? '—',
                $row->morning ?? 'No',
                $row->afternoon ?? 'No',
                $row->evening ?? 'No',
            ]);
        }
    
        fclose($output);
        exit;
    }

}
