<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BedDistribution;
use App\Models\Department;
use Illuminate\Http\Request;
use Validator;

class BedDistributionsController extends Controller
{
    private static $BedDistribution;
    private static $Department;

    public function __construct()
    {
        self::$BedDistribution = new BedDistribution();
        self::$Department = new Department();
    }

    public function getList(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $deptId = admin_dept_id();
        $departmentsQuery = self::$Department->where('status', 1);
        if ($deptId) {
            $departmentsQuery->where('id', $deptId);
        }
        $departments = $departmentsQuery->orderBy('name')->get();
        return view('/admin/bed-distributions/index', compact('departments'));
    }

    public function listPaginate(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $query = self::$BedDistribution->query()->with('department');
        if (admin_dept_id()) {
            $query->where('department_id', admin_dept_id());
        }
        if ($request->input('department_id') && $request->input('department_id') != "") {
            $query->where('department_id', $request->input('department_id'));
        }
        if ($request->input('bed_no') && $request->input('bed_no') != "") {
            $query->where('bed_no', $request->input('bed_no'));
        }
        if ($request->input('bed_status') && $request->input('bed_status') != "") {
            $query->where('bed_status', $request->input('bed_status'));
        }

        $records = $query->orderBy('department_id')->orderBy('bed_no')->paginate(20);
        return view('/admin/bed-distributions/paginate', compact('records'));
    }

    public function addPage(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'department_id' => 'required|exists:departments,id',
                'bed_no' => 'required|string|max:50',
                'gender' => 'nullable|in:M,F',
                'bed_status' => 'required|in:available,occupied,reserved,maintenance',
            ], [
                'department_id.required' => 'Please select department.',
                'department_id.exists' => 'Invalid department.',
                'bed_no.required' => 'Please enter bed number.',
                'bed_no.max' => 'Bed number must not exceed 50 characters.',
                'bed_status.required' => 'Please select bed status.',
                'bed_status.in' => 'Invalid bed status.',
            ]);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json(['heading' => 'Error', 'msg' => $firstError]);
            }
            $reqDeptId = (int) $request->input('department_id');
            $deptId = admin_dept_id() ?: $reqDeptId;
            if (admin_dept_id() && $reqDeptId != admin_dept_id()) {
                return response()->json(['heading' => 'Error', 'msg' => 'Invalid department.']);
            }
            $bedNo = trim($request->input('bed_no'));
            if (self::$BedDistribution->where('department_id', $deptId)->where('bed_no', $bedNo)->exists()) {
                return response()->json(['heading' => 'Error', 'msg' => 'This bed number already exists for the selected department.']);
            }
            $setData = [
                'department_id' => $deptId,
                'bed_no' => $bedNo,
                'gender' => $request->input('gender') ?: null,
                'bed_status' => $request->input('bed_status'),
                'status' => $request->has('status') ? 1 : 0,
            ];
            self::$BedDistribution->create($setData);
            return response()->json(['heading' => 'Success', 'msg' => 'Bed distribution added successfully.']);
        }
        $departmentsQuery = self::$Department->where('status', 1);
        if (admin_dept_id()) {
            $departmentsQuery->where('id', admin_dept_id());
        }
        $departments = $departmentsQuery->orderBy('name')->get();
        return view('/admin/bed-distributions/add-page', compact('departments'));
    }

    public function editPage(Request $request, $row_id)
    {
        $RowID = (int) base64_decode($row_id);
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $editQuery = self::$BedDistribution->where('id', $RowID);
        if (admin_dept_id()) {
            $editQuery->where('department_id', admin_dept_id());
        }
        $rowData = $editQuery->first();
        if (!$rowData) {
            return redirect('/admin/bed-distributions');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'department_id' => 'required|exists:departments,id',
                'bed_no' => 'required|string|max:50',
                'gender' => 'nullable|in:M,F',
                'bed_status' => 'required|in:available,occupied,reserved,maintenance',
            ], [
                'department_id.required' => 'Please select department.',
                'department_id.exists' => 'Invalid department.',
                'bed_no.required' => 'Please enter bed number.',
                'bed_no.max' => 'Bed number must not exceed 50 characters.',
                'bed_status.required' => 'Please select bed status.',
                'bed_status.in' => 'Invalid bed status.',
            ]);
            if ($validator->fails()) {
                $firstError = $validator->errors()->first();
                return response()->json(['heading' => 'Error', 'msg' => $firstError]);
            }
            $reqDeptId = (int) $request->input('department_id');
            $deptId = admin_dept_id() ?: $reqDeptId;
            if (admin_dept_id() && $reqDeptId != admin_dept_id()) {
                return response()->json(['heading' => 'Error', 'msg' => 'Invalid department.']);
            }
            $bedNo = trim($request->input('bed_no'));
            $exists = self::$BedDistribution->where('department_id', $deptId)->where('bed_no', $bedNo)->where('id', '!=', $RowID)->exists();
            if ($exists) {
                return response()->json(['heading' => 'Error', 'msg' => 'This bed number already exists for the selected department.']);
            }
            $rowData->department_id = $deptId;
            $rowData->bed_no = $bedNo;
            $rowData->gender = $request->input('gender') ?: null;
            $rowData->bed_status = $request->input('bed_status');
            $rowData->status = $request->has('status') ? 1 : 0;
            $rowData->save();
            return response()->json(['heading' => 'Success', 'msg' => 'Bed distribution updated successfully.']);
        }
        $departmentsQuery = self::$Department->where('status', 1);
        if (admin_dept_id()) {
            $departmentsQuery->where('id', admin_dept_id());
        }
        $departments = $departmentsQuery->orderBy('name')->get();
        return view('/admin/bed-distributions/edit-page', compact('rowData', 'row_id', 'departments'));
    }
}
