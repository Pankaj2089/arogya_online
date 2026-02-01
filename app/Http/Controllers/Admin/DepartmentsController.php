<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Validator;

class DepartmentsController extends Controller
{
    private static $Department;

    public function __construct()
    {
        self::$Department = new Department();
    }

    public function getList(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        return view('/admin/departments/index');
    }

    public function listPaginate(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $query = self::$Department->query();

        if ($request->input('name') && $request->input('name') != "") {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }

        $records = $query->orderBy('id', 'DESC')->paginate(20);
        return view('/admin/departments/paginate', compact('records'));
    }

    public function addPage(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:departments,name',
            ], [
                'name.required' => 'Please enter department name.',
                'name.unique' => 'This department name already exists.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
            } else {
                if (!self::$Department->ExistingRecord($request->input('name'))) {
                    $setData['name'] = trim($request->input('name'));
                    $setData['status'] = $request->has('status') ? 1 : 0;
                    self::$Department->CreateRecord($setData);
                }
                echo json_encode(array('heading' => 'Success', 'msg' => 'Department added successfully'));
                die;
            }
        }
        return view('/admin/departments/add-page');
    }

    public function editPage(Request $request, $row_id)
    {
        $RowID = base64_decode($row_id);
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:departments,name,' . $RowID,
            ], [
                'name.required' => 'Please enter department name.',
                'name.unique' => 'This department name already exists.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
            } else {
                if (self::$Department->ExistingRecordUpdate(trim($request->input('name')), $RowID)) {
                    echo json_encode(array('heading' => 'Error', 'msg' => 'This department name already exists.'));
                    die;
                }
                $setData['id'] = $RowID;
                $setData['name'] = trim($request->input('name'));
                $setData['status'] = $request->has('status') ? 1 : 0;
                self::$Department->UpdateRecord($setData);
                echo json_encode(array('heading' => 'Success', 'msg' => 'Department updated successfully'));
                die;
            }
        }
        $rowData = self::$Department->where(array('id' => $RowID))->first();
        if (isset($rowData->id)) {
            return view('/admin/departments/edit-page', compact('rowData', 'row_id'));
        } else {
            return redirect('/admin/departments');
        }
    }
}
