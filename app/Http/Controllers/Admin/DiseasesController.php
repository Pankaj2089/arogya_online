<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Disease;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Validator;

class DiseasesController extends Controller
{
    private static $Disease;
    private static $Department;

    public function __construct()
    {
        self::$Disease = new Disease();
        self::$Department = new Department();
    }

    public function getList(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $departments = self::$Department->where('status', 1)->orderBy('name')->get();
        return view('/admin/diseases/index', compact('departments'));
    }

    public function listPaginate(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $query = self::$Disease->query()->with('department');

        if ($request->input('name') && $request->input('name') != "") {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($request->input('dept_id') && $request->input('dept_id') != "") {
            $query->where('dept_id', $request->input('dept_id'));
        }

        $records = $query->orderBy('id', 'DESC')->paginate(20);
        return view('/admin/diseases/paginate', compact('records'));
    }

    public function addPage(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'dept_id' => 'required|exists:departments,id',
            ], [
                'name.required' => 'Please enter disease name.',
                'dept_id.required' => 'Please select department.',
                'dept_id.exists' => 'Invalid department selected.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
                if ($errors->first('dept_id')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('dept_id')));
                    die;
                }
            } else {
                if (!self::$Disease->ExistingRecord(trim($request->input('name')), $request->input('dept_id'))) {
                    $setData['name'] = trim($request->input('name'));
                    $setData['dept_id'] = $request->input('dept_id');
                    $setData['status'] = $request->has('status') ? 1 : 0;
                    self::$Disease->CreateRecord($setData);
                }
                echo json_encode(array('heading' => 'Success', 'msg' => 'Disease added successfully'));
                die;
            }
        }
        $departments = self::$Department->where('status', 1)->orderBy('name')->get();
        return view('/admin/diseases/add-page', compact('departments'));
    }

    public function editPage(Request $request, $row_id)
    {
        $RowID = base64_decode($row_id);
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'dept_id' => 'required|exists:departments,id',
            ], [
                'name.required' => 'Please enter disease name.',
                'dept_id.required' => 'Please select department.',
                'dept_id.exists' => 'Invalid department selected.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
                if ($errors->first('dept_id')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('dept_id')));
                    die;
                }
            } else {
                if (self::$Disease->ExistingRecordUpdate(trim($request->input('name')), $request->input('dept_id'), $RowID)) {
                    echo json_encode(array('heading' => 'Error', 'msg' => 'This disease name already exists for the selected department.'));
                    die;
                }
                $setData['id'] = $RowID;
                $setData['name'] = trim($request->input('name'));
                $setData['dept_id'] = $request->input('dept_id');
                $setData['status'] = $request->has('status') ? 1 : 0;
                self::$Disease->UpdateRecord($setData);
                echo json_encode(array('heading' => 'Success', 'msg' => 'Disease updated successfully'));
                die;
            }
        }
        $rowData = self::$Disease->where(array('id' => $RowID))->first();
        if (isset($rowData->id)) {
            $departments = self::$Department->where('status', 1)->orderBy('name')->get();
            return view('/admin/diseases/edit-page', compact('rowData', 'row_id', 'departments'));
        } else {
            return redirect('/admin/diseases');
        }
    }
}
