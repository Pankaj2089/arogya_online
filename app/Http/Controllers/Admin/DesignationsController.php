<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Validator;

class DesignationsController extends Controller
{
    private static $Designation;

    public function __construct()
    {
        self::$Designation = new Designation();
    }

    public function getList(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        return view('/admin/designations/index');
    }

    public function listPaginate(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $query = self::$Designation->query();

        if ($request->input('name') && $request->input('name') != "") {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }

        $records = $query->orderBy('id', 'DESC')->paginate(20);
        return view('/admin/designations/paginate', compact('records'));
    }

    public function addPage(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:designations,name',
            ], [
                'name.required' => 'Please enter designation name.',
                'name.unique' => 'This designation name already exists.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
            } else {
                if (!self::$Designation->ExistingRecord($request->input('name'))) {
                    $setData['name'] = trim($request->input('name'));
                    $setData['status'] = $request->has('status') ? 1 : 0;
                    self::$Designation->CreateRecord($setData);
                }
                echo json_encode(array('heading' => 'Success', 'msg' => 'Designation added successfully'));
                die;
            }
        }
        return view('/admin/designations/add-page');
    }

    public function editPage(Request $request, $row_id)
    {
        $RowID = base64_decode($row_id);
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:designations,name,' . $RowID,
            ], [
                'name.required' => 'Please enter designation name.',
                'name.unique' => 'This designation name already exists.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
            } else {
                if (self::$Designation->ExistingRecordUpdate(trim($request->input('name')), $RowID)) {
                    echo json_encode(array('heading' => 'Error', 'msg' => 'This designation name already exists.'));
                    die;
                }
                $setData['id'] = $RowID;
                $setData['name'] = trim($request->input('name'));
                $setData['status'] = $request->has('status') ? 1 : 0;
                self::$Designation->UpdateRecord($setData);
                echo json_encode(array('heading' => 'Success', 'msg' => 'Designation updated successfully'));
                die;
            }
        }
        $rowData = self::$Designation->where(array('id' => $RowID))->first();
        if (isset($rowData->id)) {
            return view('/admin/designations/edit-page', compact('rowData', 'row_id'));
        } else {
            return redirect('/admin/designations');
        }
    }
}
