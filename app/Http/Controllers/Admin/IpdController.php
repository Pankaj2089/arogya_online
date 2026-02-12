<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OpdRegistration;
use App\Models\IpdRegistration;
use App\Models\User;
use App\Models\BedDistribution;
use App\Models\Department;
use App\Models\Disease;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Validator;

class IpdController extends Controller
{
    public function newIpdRegistration(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $opdRecord = null;
        $opdLookupError = null;

        if ($request->isMethod('post') && $request->input()) {
            $opdNo = trim($request->input('opd_no', ''));

            if ($opdNo !== '' && !$request->has('opd_registration_id')) {
                $opdLookupQuery = OpdRegistration::where('opd_number', $opdNo);
                if (admin_dept_id()) {
                    $opdLookupQuery->where('dept_id', admin_dept_id());
                }
                $opdRecord = $opdLookupQuery->first();
                if (!$opdRecord) {
                    $opdLookupError = 'OPD number not found.';
                }else{
                    $patientAlreadyAdmit = IpdRegistration::where('opd_registration_id', $opdRecord->id)
                    ->whereNull('discharge_date')
                    ->where(function ($q) {
                        $q->whereNull('bed_distribution_id')
                        ->orWhereHas('bedDistribution', function ($b) {
                            $b->where('bed_status', 'occupied');
                        });
                    })
                    ->exists();

                    if ($patientAlreadyAdmit) {
                        $opdLookupError = 'Patient already admitted and not discharged yet.';
                        $opdRecord = null;
                    }

                }

            } elseif ($request->has('opd_registration_id') && $request->input('opd_registration_id')) {
                $validator = Validator::make($request->all(), [
                    'opd_registration_id' => 'required|exists:opd_registration,id',
                    'date' => 'required|date',
                    'fath_husb_name' => 'nullable|string|max:255',
                    'address' => 'nullable|string',
                    'diagnosis' => 'nullable|string',
                    'bed_distribution_id' => 'nullable|exists:bed_distributions,id',
                    'admit_by_user_id' => 'nullable|exists:users,id',
                    'amount' => 'nullable|numeric|min:0',
                ], [
                    'opd_registration_id.required' => 'OPD registration is required.',
                    'opd_registration_id.exists' => 'Invalid OPD registration.',
                    'date.required' => 'Please select date.',
                    'date.date' => 'Please enter a valid date.',
                ]);
                if ($validator->fails()) {
                    $firstError = $validator->errors()->first();
                    return response()->json(['heading' => 'Error', 'msg' => $firstError]);
                }

                $opdFindQuery = OpdRegistration::where('id', $request->input('opd_registration_id'));
                if (admin_dept_id()) {
                    $opdFindQuery->where('dept_id', admin_dept_id());
                }
                $opdRecord = $opdFindQuery->first();
                if (!$opdRecord) {
                    return response()->json(['heading' => 'Error', 'msg' => 'OPD record not found.']);
                }

                $dateStr = $request->input('date');
                $dateObj = \DateTime::createFromFormat('m/d/Y', $dateStr);
                $dateDb = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');

                $bedDistributionId = $request->input('bed_distribution_id') ? (int) $request->input('bed_distribution_id') : null;
                $category = trim($request->input('category', 'GENERAL')) ?: 'GENERAL';

                $ipdRecord = IpdRegistration::create([
                    'opd_registration_id' => $opdRecord->id,
                    'patient_name' => $opdRecord->patient_name,
                    'patient_age' => $opdRecord->patient_age !== null ? (int) $opdRecord->patient_age : null,
                    'patient_age_unit' => $opdRecord->patient_age_unit ?? null,
                    'gender' => $opdRecord->gender ?? null,
                    'opd_number' => $opdRecord->opd_number ?? null,
                    'hid_number' => $opdRecord->hid_number ?? null,
                    'dept_id' => $opdRecord->dept_id ? (int) $opdRecord->dept_id : null,
                    'category' => $category,
                    'date' => $dateDb,
                    'fath_husb_name' => trim($request->input('fath_husb_name')) ?: null,
                    'address' => trim($request->input('address')) ?: null,
                    'diagnosis' => trim($request->input('diagnosis')) ?: null,
                    'bed_distribution_id' => $bedDistributionId,
                    'admit_by_user_id' => $request->input('admit_by_user_id') ? (int) $request->input('admit_by_user_id') : null,
                    'amount' => $request->input('amount') !== '' && $request->input('amount') !== null ? (float) $request->input('amount') : null,
                ]);

                $fy = FinancialYear::find($opdRecord->financial_year_id);
                $yearName = $fy ? $fy->name : date('Y');
                $ipdNumber = $yearName . str_pad((string) $ipdRecord->id, 4, '0', STR_PAD_LEFT);
                $ipdRecord->update(['ipd_number' => $ipdNumber]);

                if ($bedDistributionId) {
                    BedDistribution::where('id', $bedDistributionId)->update(['bed_status' => 'occupied']);
                }

                return response()->json(['heading' => 'Success', 'msg' => 'IPD registration saved successfully.']);
            }
        }

        $deptId = $opdRecord && $opdRecord->dept_id ? (int) $opdRecord->dept_id : admin_dept_id();
        $doctorsQuery = User::where('type', 'Doctor')->where('status', 1);
        if ($deptId) {
            $doctorsQuery->where('dept_id', $deptId);
        }
        $doctors = $doctorsQuery->orderBy('name')->get(['id', 'name']);
        $bedsQuery = BedDistribution::where('status', 1)->where('bed_status', 'available');
        if (admin_dept_id()) {
            $bedsQuery->where('department_id', admin_dept_id());
        }
        $beds = $bedsQuery->orderBy('bed_no')->get();
        $lastIpdQuery = IpdRegistration::with('opdRegistration')->orderBy('id', 'desc');
        if (admin_dept_id()) {
            $lastIpdQuery->where('dept_id', admin_dept_id());
        }
        if ($deptId) {
            $lastIpdQuery->where('dept_id', $deptId);
        }
        $lastIpd = $lastIpdQuery->first();

        $disease = null;

        if ($opdRecord && $opdRecord->disease_id > 0) {
            $disease = Disease::find($opdRecord->disease_id);
        }
    
        return view('/admin/ipd/new-ipd-registration', compact('opdRecord', 'opdLookupError', 'doctors', 'beds', 'lastIpd', 'disease'  ));
    }

    public function updateIpdRegistration(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $ipdRecord = null;
        $ipdLookupError = null;

        if ($request->isMethod('post') && $request->input()) {
            $ipdNo = trim($request->input('ipd_no', ''));

            if ($ipdNo !== '' && !$request->has('ipd_registration_id')) {
                $ipdLookupQuery = IpdRegistration::with('opdRegistration', 'bedDistribution')->where('ipd_number', $ipdNo);
                if (admin_dept_id()) {
                    $ipdLookupQuery->where('dept_id', admin_dept_id());
                }
                $ipdRecord = $ipdLookupQuery->first();
                if (!$ipdRecord) {
                    $ipdLookupError = 'IPD number not found.';
                }
            } elseif ($request->has('ipd_registration_id') && $request->input('ipd_registration_id')) {
                $validator = Validator::make($request->all(), [
                    'ipd_registration_id' => 'required|exists:ipd_registration,id',
                    'date' => 'required|date',
                    'fath_husb_name' => 'nullable|string|max:255',
                    'address' => 'nullable|string',
                    'diagnosis' => 'nullable|string',
                    'bed_distribution_id' => 'nullable|exists:bed_distributions,id',
                    'admit_by_user_id' => 'nullable|exists:users,id',
                    'amount' => 'nullable|numeric|min:0',
                ], [
                    'ipd_registration_id.required' => 'IPD registration is required.',
                    'date.required' => 'Please select date.',
                ]);
                if ($validator->fails()) {
                    $firstError = $validator->errors()->first();
                    return response()->json(['heading' => 'Error', 'msg' => $firstError]);
                }

                $ipdFindQuery = IpdRegistration::where('id', $request->input('ipd_registration_id'));
                if (admin_dept_id()) {
                    $ipdFindQuery->where('dept_id', admin_dept_id());
                }
                $ipdRecord = $ipdFindQuery->first();
                if (!$ipdRecord) {
                    return response()->json(['heading' => 'Error', 'msg' => 'IPD record not found.']);
                }

                $dateStr = $request->input('date');
                $dateObj = \DateTime::createFromFormat('m/d/Y', $dateStr);
                $dateDb = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');

                $oldBedId = $ipdRecord->bed_distribution_id;
                $newBedId = $request->input('bed_distribution_id') ? (int) $request->input('bed_distribution_id') : null;

                $ipdRecord->date = $dateDb;
                $ipdRecord->fath_husb_name = trim($request->input('fath_husb_name')) ?: null;
                $ipdRecord->address = trim($request->input('address')) ?: null;
                $ipdRecord->diagnosis = trim($request->input('diagnosis')) ?: null;
                $ipdRecord->bed_distribution_id = $newBedId;
                $ipdRecord->admit_by_user_id = $request->input('admit_by_user_id') ? (int) $request->input('admit_by_user_id') : null;
                $ipdRecord->amount = $request->input('amount') !== '' && $request->input('amount') !== null ? (float) $request->input('amount') : null;
                $ipdRecord->save();

                if ($oldBedId && $oldBedId != $newBedId) {
                    BedDistribution::where('id', $oldBedId)->update(['bed_status' => 'available']);
                }
                if ($newBedId) {
                    BedDistribution::where('id', $newBedId)->update(['bed_status' => 'occupied']);
                }

                return response()->json(['heading' => 'Success', 'msg' => 'IPD registration updated successfully.']);
            }
        }

        $deptId = null;
        if ($ipdRecord) {
            $deptId = $ipdRecord->dept_id ? (int) $ipdRecord->dept_id : null;
            if (!$deptId && $ipdRecord->opdRegistration) {
                $deptId = $ipdRecord->opdRegistration->dept_id ? (int) $ipdRecord->opdRegistration->dept_id : null;
            }
        }
        $doctorsQuery = User::where('type', 'Doctor')->where('status', 1);
        if ($deptId) {
            $doctorsQuery->where('dept_id', $deptId);
        }
        $doctors = $doctorsQuery->orderBy('name')->get(['id', 'name']);
        $bedsBaseQuery = function () {
            $q = BedDistribution::where('status', 1)->where('bed_status', 'available')->orderBy('bed_no');
            if (admin_dept_id()) {
                $q->where('department_id', admin_dept_id());
            }
            return $q;
        };
        $beds = collect();
        if ($ipdRecord) {
            $availableBeds = $bedsBaseQuery()->get();
            $currentBedId = $ipdRecord->bed_distribution_id;
            if ($currentBedId && !$availableBeds->contains('id', $currentBedId)) {
                $currentBed = BedDistribution::find($currentBedId);
                $beds = $currentBed ? $availableBeds->push($currentBed)->sortBy('bed_no')->values() : $availableBeds;
            } else {
                $beds = $availableBeds;
            }
        } else {
            $beds = $bedsBaseQuery()->get();
        }
        $lastIpdQuery = IpdRegistration::with('opdRegistration')->orderBy('id', 'desc');
        if (admin_dept_id()) {
            $lastIpdQuery->where('dept_id', admin_dept_id());
        }
        $lastIpd = $lastIpdQuery->first();

        return view('/admin/ipd/update-ipd-registration', compact('ipdRecord', 'ipdLookupError', 'doctors', 'beds', 'lastIpd'));
    }

    public function rePrintIpd(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $ipdRecord = null;
        $ipdLookupError = null;

        if ($request->isMethod('post') && $request->input()) {
            $ipdNo = trim($request->input('ipd_no', ''));
            if ($ipdNo !== '') {
                $rePrintQuery = IpdRegistration::with('opdRegistration', 'bedDistribution', 'admitByUser')->where('ipd_number', $ipdNo);
                if (admin_dept_id()) {
                    $rePrintQuery->where('dept_id', admin_dept_id());
                }
                $ipdRecord = $rePrintQuery->first();
                if (!$ipdRecord) {
                    $ipdLookupError = 'IPD number not found.';
                }
            }
        }

        return view('/admin/ipd/re-print-ipd', compact('ipdRecord', 'ipdLookupError'));
    }

    public function patientDischarge(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        $ipdRecord = null;
        $ipdLookupError = null;
        $deptId = admin_dept_id();
        $departmentsQuery = Department::where('status', 1);
        if ($deptId) {
            $departmentsQuery->where('id', $deptId);
        }
        $departments = $departmentsQuery->orderBy('name')->get(['id', 'name']);

        if ($request->isMethod('post') && $request->input()) {
            $ipdNo = trim($request->input('ipd_no', ''));

            if ($ipdNo !== '' && !$request->has('ipd_registration_id')) {
                $dischargeLookupQuery = IpdRegistration::with('opdRegistration')->where('ipd_number', $ipdNo);
                if ($deptId) {
                    $dischargeLookupQuery->where('dept_id', $deptId);
                }
                $ipdRecord = $dischargeLookupQuery->first();
                if (!$ipdRecord) {
                    $ipdLookupError = 'IPD number not found.';
                } elseif ($ipdRecord->discharge_date !== null) {
                    $ipdLookupError = 'Patient Already pre-discharged.';
                    $ipdRecord = null;
                }
            } elseif ($request->has('ipd_registration_id') && $request->input('ipd_registration_id')) {
                $validator = Validator::make($request->all(), [
                    'ipd_registration_id' => 'required|exists:ipd_registration,id',
                    'discharge_date' => 'required|date',
                    'discharge_type' => 'required',
                    'discharge_dept_id' => 'nullable|exists:departments,id',
                ], [
                    'ipd_registration_id.required' => 'IPD registration is required.',
                    'discharge_type.required' => 'Please select discharge type.',
                    'discharge_date.required' => 'Please select discharge date.',
                ]);
                if ($validator->fails()) {
                    $firstError = $validator->errors()->first();
                    return response()->json(['heading' => 'Error', 'msg' => $firstError]);
                }

                $ipdFindQuery = IpdRegistration::with('opdRegistration')->where('id', $request->input('ipd_registration_id'));
                if ($deptId) {
                    $ipdFindQuery->where('dept_id', $deptId);
                }
                $ipdRecord = $ipdFindQuery->first();
                if (!$ipdRecord) {
                    return response()->json(['heading' => 'Error', 'msg' => 'IPD record not found.']);
                }
                if ($ipdRecord->discharge_date !== null) {
                    return response()->json(['heading' => 'Error', 'msg' => 'Patient Already pre-discharged.']);
                }

                $dateStr = $request->input('discharge_date');
                $dateObj = \DateTime::createFromFormat('m/d/Y', $dateStr);
                $dateDb = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');
                $today = date('Y-m-d');
                $admitDate = $ipdRecord->date ? $ipdRecord->date->format('Y-m-d') : null;

                if ($dateDb > $today) {
                    return response()->json(['heading' => 'Error', 'msg' => 'Discharge date cannot be greater than today.']);
                }
                if ($admitDate && $dateDb < $admitDate) {
                    return response()->json(['heading' => 'Error', 'msg' => 'Discharge date cannot be less than admit date.']);
                }

                $ipdRecord->discharge_date = $dateDb;
                $ipdRecord->discharge_dept_id = $request->input('discharge_dept_id') ? (int) $request->input('discharge_dept_id') : null;
                $ipdRecord->discharge_type = $request->input('discharge_type') ?  $request->input('discharge_type') : null;
                $ipdRecord->save();

                if ($ipdRecord->bed_distribution_id) {
                    BedDistribution::where('id', $ipdRecord->bed_distribution_id)->update(['bed_status' => 'available']);
                }

                return response()->json(['heading' => 'Success', 'msg' => 'Patient discharged successfully. Bed is now available.']);
            }
        }
        return view('/admin/ipd/patient-discharge', compact('ipdRecord', 'ipdLookupError', 'departments'));
    }
}
