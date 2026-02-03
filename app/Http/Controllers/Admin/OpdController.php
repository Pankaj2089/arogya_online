<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use App\Models\Department;
use App\Models\Disease;
use App\Models\OpdRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class OpdController extends Controller
{
    private static $FinancialYear;
    private static $Department;
    private static $OpdRegistration;

    public function __construct()
    {
        self::$FinancialYear = new FinancialYear();
        self::$Department = new Department();
        self::$OpdRegistration = new OpdRegistration();
    }

    public function newOpdRegistration(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        if ($request->isMethod('post') && $request->input()) {
            $validator = Validator::make($request->all(), [
                'financial_year_id' => 'required|exists:financial_years,id',
                'patient_name' => 'required',
                'date' => 'required',
                'fath_husb_name' => 'nullable|string|max:255',
            ], [
                'financial_year_id.required' => 'Financial year is required.',
                'financial_year_id.exists' => 'Invalid financial year.',
                'patient_name.required' => 'Please enter patient name.',
                'date.required' => 'Please select date.',
            ]);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json(['heading' => 'Error', 'msg' => $firstError]);
            }
            if (!$request->input('financial_year_id')) {
                return response()->json(['heading' => 'Error', 'msg' => 'No active financial year. Please set an active financial year first.']);
            }

            try {
                DB::beginTransaction();

                $financialYearId = (int) $request->input('financial_year_id');
                $fy = FinancialYear::where('id', $financialYearId)->where('status', 1)->lockForUpdate()->first();
                if (!$fy) {
                    DB::rollBack();
                    return response()->json(['heading' => 'Error', 'msg' => 'Active financial year not found.']);
                }

                // Next OPD: use max of (counter, max existing in table + 1) to avoid duplicate if counter was out of sync
                $counterSeq = (int) $fy->opd_number;
                $maxExisting = OpdRegistration::where('financial_year_id', $fy->id)
                    ->where('opd_number', 'like', $fy->name . '%')
                    ->selectRaw('MAX(CAST(SUBSTRING(opd_number, ?) AS UNSIGNED)) as mx', [strlen($fy->name) + 1])
                    ->value('mx');
                $maxSeq = $maxExisting !== null ? (int) $maxExisting : 0;
                $nextSeq = max($counterSeq, $maxSeq + 1);
                $opdNumber = $fy->name . str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);

                $dateStr = $request->input('date');
                $dateObj = \DateTime::createFromFormat('m/d/Y', $dateStr);
                $dateDb = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');

                $reqDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
                $deptIdForOpd = admin_dept_id() ?: $reqDeptId;
                $record = OpdRegistration::create([
                    'financial_year_id' => $fy->id,
                    'patient_name' => trim($request->input('patient_name')),
                    'fath_husb_name' => trim($request->input('fath_husb_name')) ?: null,
                    'address' => trim($request->input('address')) ?: null,
                    'date' => $dateDb,
                    'patient_age' => (int) $request->input('patient_age', 0),
                    'patient_age_unit' => in_array($request->input('patient_age_unit'), ['Years', 'Months', 'Days']) ? $request->input('patient_age_unit') : 'Years',
                    'gender' => $request->input('gender') ?: null,
                    'dept_id' => $deptIdForOpd,
                    'register_type' => $request->input('register_type', 'New'),
                    'opd_number' => $opdNumber,
                    'hid_number' => $opdNumber,
                ]);

                $firstLetter = mb_strtoupper(mb_substr(trim($request->input('patient_name')), 0, 1));
                if ($firstLetter === '') {
                    $firstLetter = 'U';
                }
                $hidNumber = $fy->name . '-' . $firstLetter . '-' . str_pad((string) $record->id, 4, '0', STR_PAD_LEFT);
                OpdRegistration::where('id', $record->id)->update(['hid_number' => $hidNumber]);

                FinancialYear::where('id', $fy->id)->update(['opd_number' => $nextSeq + 1]);

                DB::commit();
                return response()->json(['heading' => 'Success', 'msg' => 'OPD registration saved successfully.']);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['heading' => 'Error', 'msg' => 'Failed to save registration. Please try again.']);
            }
        }

        $activeFinancialYear = self::$FinancialYear->where('status', 1)->first();
        $deptId = admin_dept_id();
        $departmentsQuery = self::$Department->where('status', 1);
        if ($deptId) {
            $departmentsQuery->where('id', $deptId);
        }
        $departments = $departmentsQuery->orderBy('name')->get();
        $lastOpdQuery = OpdRegistration::orderBy('id', 'desc');
        if ($deptId) {
            $lastOpdQuery->where('dept_id', $deptId);
        }
        $lastOpd = $lastOpdQuery->first();
        return view('/admin/opd/new-opd-registration', compact('activeFinancialYear', 'departments', 'lastOpd'));
    }

    public function reScheduleOpd(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $activeFinancialYear = self::$FinancialYear->where('status', 1)->first();
        $deptId = admin_dept_id();
        $departmentsQuery = self::$Department->where('status', 1);
        if ($deptId) {
            $departmentsQuery->where('id', $deptId);
        }
        $departments = $departmentsQuery->orderBy('name')->get();
        $lastOpdQuery = OpdRegistration::orderBy('id', 'desc');
        if ($deptId) {
            $lastOpdQuery->where('dept_id', $deptId);
        }
        $lastOpd = $lastOpdQuery->first();
        $opdRecord = null;
        $opdLookupError = null;

        if ($request->isMethod('post') && $request->input()) {
            $opdNo = trim($request->input('opd_no', ''));

            if ($opdNo !== '' && !$request->has('hid_number_to_use')) {
                $opdLookupQuery = OpdRegistration::where('opd_number', $opdNo);
                if ($deptId) {
                    $opdLookupQuery->where('dept_id', $deptId);
                }
                $opdRecord = $opdLookupQuery->first();
                if (!$opdRecord) {
                    $opdLookupError = 'OPD number not found.';
                }
            } elseif ($request->has('hid_number_to_use') && $request->input('hid_number_to_use')) {
                $validator = Validator::make($request->all(), [
                    'financial_year_id' => 'required|exists:financial_years,id',
                    'hid_number_to_use' => 'required|string',
                    'patient_name' => 'required',
                    'dept_id' => 'nullable|exists:departments,id',
                    'patient_age' => 'nullable|integer|min:0',
                ]);
                if ($validator->fails()) {
                    $firstError = $validator->errors()->first();
                    return response()->json(['heading' => 'Error', 'msg' => $firstError]);
                }

                try {
                    DB::beginTransaction();
                    $financialYearId = (int) $request->input('financial_year_id');
                    $fy = FinancialYear::where('id', $financialYearId)->where('status', 1)->lockForUpdate()->first();
                    if (!$fy) {
                        DB::rollBack();
                        return response()->json(['heading' => 'Error', 'msg' => 'Active financial year not found.']);
                    }
                    // Next OPD: use max of (counter, max existing in table + 1) to avoid duplicate if counter was out of sync
                    $counterSeq = (int) $fy->opd_number;
                    $maxExisting = OpdRegistration::where('financial_year_id', $fy->id)
                        ->where('opd_number', 'like', $fy->name . '%')
                        ->selectRaw('MAX(CAST(SUBSTRING(opd_number, ?) AS UNSIGNED)) as mx', [strlen($fy->name) + 1])
                        ->value('mx');
                    $maxSeq = $maxExisting !== null ? (int) $maxExisting : 0;
                    $nextSeq = max($counterSeq, $maxSeq + 1);
                    $opdNumber = $fy->name . str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
                    $dateDb = date('Y-m-d');

                    $reqDeptId = $request->input('dept_id') ? (int) $request->input('dept_id') : null;
                    $deptIdForOpd = admin_dept_id() ?: $reqDeptId;
                    OpdRegistration::create([
                        'financial_year_id' => $fy->id,
                        'patient_name' => trim($request->input('patient_name')),
                        'fath_husb_name' => trim($request->input('fath_husb_name')) ?: null,
                        'address' => trim($request->input('address')) ?: null,
                        'date' => $dateDb,
                        'patient_age' => (int) $request->input('patient_age', 0),
                        'patient_age_unit' => in_array($request->input('patient_age_unit'), ['Years', 'Months', 'Days']) ? $request->input('patient_age_unit') : 'Years',
                        'gender' => $request->input('gender') ?: null,
                        'dept_id' => $deptIdForOpd,
                        'register_type' => 'OLD',
                        'opd_number' => $opdNumber,
                        'hid_number' => trim($request->input('hid_number_to_use')),
                    ]);

                    FinancialYear::where('id', $fy->id)->update(['opd_number' => $nextSeq + 1]);
                    DB::commit();
                    return response()->json(['heading' => 'Success', 'msg' => 'Re-schedule OPD saved successfully.']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    $msg = config('app.debug') ? $e->getMessage() : 'Failed to save. Please try again.';
                    return response()->json(['heading' => 'Error', 'msg' => $msg]);
                }
            }
        }

        return view('/admin/opd/re-schedule-opd', compact('activeFinancialYear', 'departments', 'lastOpd', 'opdRecord', 'opdLookupError'));
    }

    public function patientDiseases(Request $request)
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
        $opdRecords = collect();
        $diseases = collect();
        $filterDate = null;
        $filterDeptId = null;
        $filterError = null;

        if ($request->isMethod('post') && $request->input()) {
            if ($request->has('opd_diseases_submit')) {
                // Submit: update disease_id for each OPD (only OPDs in doctor's department)
                $rows = $request->input('opd_disease', []);
                if (is_array($rows)) {
                    foreach ($rows as $opdId => $diseaseId) {
                        $opdId = (int) $opdId;
                        if ($opdId <= 0) continue;
                        $updateQuery = OpdRegistration::where('id', $opdId);
                        if ($adminDeptId) {
                            $updateQuery->where('dept_id', $adminDeptId);
                        }
                        $diseaseId = $diseaseId !== '' && $diseaseId !== null ? (int) $diseaseId : null;
                        $updateQuery->update(['disease_id' => $diseaseId]);
                    }
                }
                return response()->json(['heading' => 'Success', 'msg' => 'Disease details updated successfully.']);
            }

            // Search: filter by date and department
            $dateStr = trim($request->input('filter_date', ''));
            $deptId = $request->input('filter_dept_id') ? (int) $request->input('filter_dept_id') : null;
            if ($adminDeptId) {
                $deptId = $adminDeptId;
            }

            $dateObj = $dateStr ? \DateTime::createFromFormat('m/d/Y', $dateStr) : null;
            $dateDb = $dateObj ? $dateObj->format('Y-m-d') : null;
            $today = date('Y-m-d');

            if (!$dateDb) {
                $filterError = 'Please select date.';
            } elseif ($dateDb > $today) {
                $filterError = 'Date cannot be greater than today.';
            } else {
                $query = OpdRegistration::with('disease')->where('date', $dateDb);
                if ($deptId) {
                    $query->where('dept_id', $deptId);
                }
                $opdRecords = $query->orderBy('opd_number')->get();
                $filterDate = $dateStr;
                $filterDeptId = $deptId;
                $diseasesQuery = Disease::where('status', 1)->orderBy('name');
                if ($deptId) {
                    $diseasesQuery->where('dept_id', $deptId);
                }
                $diseases = $diseasesQuery->get(['id', 'name']);
            }
        }

        return view('/admin/opd/patient-diseases', compact('departments', 'opdRecords', 'diseases', 'filterDate', 'filterDeptId', 'filterError'));
    }
}
