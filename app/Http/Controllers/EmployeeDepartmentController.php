<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TenantsModel\Department;
use App\TenantsModel\EmployeeDetails;
use App\Tenants;
use Validator;

class EmployeeDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));

        clientConnect('127.0.0.1', $database, 'root');

        $empdetails_tbl = $table;
        $department_tbl = $table;

        $department_table = new Department($department_tbl.'_department');

        $department_data = $department_table->get()->map(function ($r) use ($empdetails_tbl, $department_table){
            $r['department_head'] = $department_table->getEmployeeDetailsBy($empdetails_tbl, $r['department_head']);
            return $r;
        });

        return response()->json([
            'status' => 'success',
            'msg' => $department_data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));
        //set database connection
        clientConnect('127.0.0.1', $database, 'root');
        $request['department_id'] = randomNumber();

        $validator = Validator::make($request->all(),[
            'department_id' => 'required',
            'department_name' => 'required',
            'department_head' => 'required'
        ]);
        
        if(!$validator->fails()){
            $department = new Department($table.'_department');
            $department->insert(array_filter($request->all(), create_function('$value','return $value != "undefined";')));
            return response()->json([
                'status' => 'success',
                'msg' => $request->all()
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => $validator->errors()->toArray(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));

        clientConnect('127.0.0.1', $database, 'root');

        $department_table = new Department($table.'_department');

        if($_GET['filter'] == 'user'){
            $department_data = $department_table->where('department_head',$id)->get()->map(function ($r) use ($table, $department_table){
                $r['department_head'] = $department_table->getEmployeeDetailsBy($table, $r['department_head']);
                return $r;
            });
            return response()->json([
                'status' => 'success',
                'msg' => $department_data
            ]);
        } else if($_GET['filter'] == 'id') {
            //note: must return the value first to show the output
            $department_data = $department_table->orderBy('id','desc')->where('department_head',$id)->limit(1)->get()->map(function ($r) use ($table, $department_table){
                $r['department_head'] = $department_table->getEmployeeDetailsBy($table, $r['department_head']);
                return $r;
            });
            return response()->json([
                'status' => 'success',
                'msg' => $department_data
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => 'No specified filter'
            ]);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
