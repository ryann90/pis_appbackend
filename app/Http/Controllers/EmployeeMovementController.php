<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tenants;
use App\TenantsModel\Movement;
use Validator;

class EmployeeMovementController extends Controller
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

        clientConnect('127.0.0.1', $database,'root');

        $movement_table = new Movement($table."_movement");

        $movement_data = $movement_table->get()->map(function ($r) use ($movement_table, $table){

            $r['added_by'] = $movement_table->getEmployeeDetailsBy($table, $r['added_by']);
            $r['user'] = $movement_table->getEmployeeDetailsBy($table, $r['user_id']);
            $r['department'] = $movement_table->getEmployeeDetailsByDepartment($table, $r['department_id']);

            return $r;
        });


        return response()->json([
            'status' => 'success',
            'msg' => $movement_data
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
        //get first the database and table
        //set database connection
        clientConnect('127.0.0.1',$database,'root');
        $validator = Validator::make($request->all(),[
            'user_id' => 'required',
            'position' => 'required',
            'added_by' => 'required',
            'date_start' => 'required',
            'department_id' => 'required'
        ]);
        
        if (!$validator->fails()) {
            $request['company_id'] = $tenant['company_id'];
            $movement = new Movement($table."_movement");
            $movement_model = $movement->insert(array_filter($request->all(),create_function('$value','return $value != "undefined";')));
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

       /*  
        if($_GET['filter'] == "user")
            search for movement user_id ->get()

        else if ($_GET['filter] == "id")
            search for movement id ->first() 
        */

        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();
        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));
    
        clientConnect('127.0.0.1', $database, 'root');

        $movement_table = new Movement($table.'_movement');

        if($_GET['filter'] == 'user'){
            $movement_model = $movement_table->where('user_id', $id)->get()->map(function ($r) use ($movement_table, $table){
            
                $r['user_id'] = $movement_table->getEmployeeDetailsBy($table, $r['user_id']);
                $r['department_id'] = $movement_table->getEmployeeDetailsByDepartment($table, $r['department_id']);
                $r['added_by'] = $movement_table->getEmployeeDetailsBy($table, $r['added_by']);
    
                return $r;
            });
        } 

        else if($_GET['filter'] == 'id') 
        {
            $movement_model = $movement_table->orderBy('id','desc')->where('user_id', $id)->limit(1)->get()->map(function ($r) use ($movement_table, $dynamic_tbl){
            
                $r['user_id'] = $movement_table->getEmployeeDetailsBy($dynamic_tbl, $r['user_id']);
                $r['department_id'] = $movement_table->getEmployeeDetailsByDepartment($dynamic_tbl, $r['department_id']);
                $r['added_by'] = $movement_table->getEmployeeDetailsBy($dynamic_tbl, $r['added_by']);
    
                return $r;
            });
        } 
        
        else 
        {
            return response()->json([
                'statuts' => 'failed',
                'msg' => 'no specified filter'
            ]);
        }

        return response()->json([
            'statuts' => 'success',
            'msg' => $movement_model
        ]);
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
