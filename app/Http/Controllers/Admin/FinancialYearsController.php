<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Validator;

class FinancialYearsController extends Controller
{
    private static $FinancialYear;

    public function __construct()
    {
        self::$FinancialYear = new FinancialYear();
    }

    public function getList(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        return view('/admin/financial-years/index');
    }

    public function listPaginate(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        $query = self::$FinancialYear->query();

        if ($request->input('name') && $request->input('name') != "") {
            $name = $request->input('name');
            $query->where('name', 'like', '%' . $name . '%');
        }
        if ($request->input('opd_number') && $request->input('opd_number') != "") {
            $opd_number = $request->input('opd_number');
            $query->where('opd_number', 'like', '%' . $opd_number . '%');
        }

        $records = $query->orderBy('id', 'DESC')->paginate(20);
        return view('/admin/financial-years/paginate', compact('records'));
    }

    public function addPage(Request $request)
    {
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }
        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:financial_years,name',
                'opd_number' => 'required|integer|min:0',
            ], [
                'name.required' => 'Please select year.',
                'name.unique' => 'This financial year already exists.',
                'opd_number.required' => 'Please enter OPD number.',
                'opd_number.integer' => 'OPD number must be an integer.',
                'opd_number.min' => 'OPD number must be 0 or greater.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
                if ($errors->first('opd_number')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('opd_number')));
                    die;
                }
            } else {
                if (!self::$FinancialYear->ExistingRecord($request->input('name'))) {
                    $setData['name'] = $request->input('name');
                    $setData['opd_number'] = (int) $request->input('opd_number');
                    $setData['status'] = $request->has('status') ? 1 : 0;
                    if ($setData['status'] == 1) {
                        DB::table('financial_years')->update(['status' => 0]);
                    }
                    self::$FinancialYear->CreateRecord($setData);
                }
                echo json_encode(array('heading' => 'Success', 'msg' => 'Financial year added successfully'));
                die;
            }
        }
        return view('/admin/financial-years/add-page');
    }

    public function editPage(Request $request, $row_id)
    {
        $RowID = base64_decode($row_id);
        if (!$request->session()->has('admin_email')) {
            return redirect('/admin/');
        }

        if ($request->input()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:financial_years,name,' . $RowID,
                'opd_number' => 'required|integer|min:0',
            ], [
                'name.required' => 'Please select year.',
                'name.unique' => 'This financial year already exists.',
                'opd_number.required' => 'Please enter OPD number.',
                'opd_number.integer' => 'OPD number must be an integer.',
                'opd_number.min' => 'OPD number must be 0 or greater.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                if ($errors->first('name')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('name')));
                    die;
                }
                if ($errors->first('opd_number')) {
                    return json_encode(array('heading' => 'Error', 'msg' => $errors->first('opd_number')));
                    die;
                }
            } else {
                if (self::$FinancialYear->ExistingRecordUpdate($request->input('name'), $RowID)) {
                    echo json_encode(array('heading' => 'Error', 'msg' => 'This financial year already exists.'));
                    die;
                }
                $setData['id'] = $RowID;
                $setData['name'] = $request->input('name');
                $setData['opd_number'] = (int) $request->input('opd_number');
                $setData['status'] = $request->has('status') ? 1 : 0;
                if ($setData['status'] == 1) {
                    DB::table('financial_years')->update(['status' => 0]);
                }
                self::$FinancialYear->UpdateRecord($setData);
                echo json_encode(array('heading' => 'Success', 'msg' => 'Financial year updated successfully'));
                die;
            }
        }
        $rowData = self::$FinancialYear->where(array('id' => $RowID))->first();
        if (isset($rowData->id)) {
            return view('/admin/financial-years/edit-page', compact('rowData', 'row_id'));
        } else {
            return redirect('/admin/financial-years');
        }
    }
}
