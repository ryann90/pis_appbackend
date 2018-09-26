<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tenants;
use App\TenantsModel\EmployeeDetails;
use Validator;
use Intervention\Image\Facades\Image as Image;

class TenantEmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //get first the database and table
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));

        // set the database connection to the appropriate user
        clientConnect('127.0.0.1', $database, 'root');
        // set the table connection to the appropriate user
        $employees = new EmployeeDetails($table."_employee_details");
        /* $employees->refresh(); */
        return response()->json([
            'status' => 'success',
            'msg' => $employees->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
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
        $user_id = randomNumber();
        $validator = Validator::make($request->all(),[
            'fname' => 'required|min:2' ,
            'mname' => 'required' ,
            'lname' => 'required|min:2' ,
            'email' => 'required|email|min:2',
            'cell_num' => 'required' ,
            'address' => 'required' ,
            'work_location' => 'required', 
            'filename' => 'required',
        ]);
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));

        $request['user_id'] = $user_id;

        //search for the Tenants database for the db and tbl
        clientConnect('127.0.0.1', $database, 'root');

        $image = $request->file('filename');
        $img_name = $request->post('fname'). "-" . $request->post('lname'). "-" .date('Y-m-d').'-'.$user_id.'.'.$image->getClientOriginalExtension();
        
        if( !$validator->fails() ){
            //sets the table connection
            $emp = new EmployeeDetails($table."_employee_details");
            if($image->move('Uploads/image', $img_name)) {
                $request['image'] = $img_name;
                //return array_filter($request->except('filename'),create_function('$value', 'return $value != "undefined";'));
                $emp_model = $emp->insert(array_filter($request->except('filename'),create_function('$value', 'return $value != "undefined";')));
            } else {
                return response()->json([
                    'status' => 'failed',
                    'msg' => 'failed to upload'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'msg' => [
                 'created' => $emp_model,
                 'msg' => $request->all()
             ],
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
    public function edit(Request $request, $user_id)
    {
        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));

        //sets the db connection base on the session given by the front end
        clientConnect('127.0.0.1', $database, 'root');
        //sets the table connection
        $emp = new EmployeeDetails($table."_employee_details");
        $emp_model = $emp->where('user_id', $user_id)->first();
        
        if ($emp_model) {
            return response()->json([
                'status' => 'success',
                'msg' =>  $emp_model
            ]);
        } 
        else {
            return response()->json([
                'status' => 'failed',
                'msg' =>  'Page 404'
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
        $validator = Validator::make($request->all(),[
            'fname' => 'required|min:2' ,
            'mname' => 'required' ,
            'lname' => 'required|min:2' ,
            'email' => 'required|email|min:2',
            'cell_num' => 'required' ,
            'address' => 'required' ,
            'work_location' => 'required', 
        ]);

        $tenant = Tenants::where(['database' => $request->header('d'), 'tbl' => $request->header('t')])->first();

        $database = decrypt($request->header('d'));
        $table = decrypt($request->header('t'));
        clientConnect('127.0.0.1', $database, 'root');

        if( !$validator->fails() ){
            //sets the table connection
            $emp = new EmployeeDetails($table."_employee_details");
            $emp_model = $emp->where('user_id',$id)->update(array_filter($request->except(['filename','_method']), create_function('$value', 'return $value != "null";')));
            if ($emp_model) {
                return response()->json([
                    'status' => 'success',
                    'msg' => $request->all()
                ]);
            }
            else {
                return response()->json([
                    'status' => 'failed',
                    'msg' => "failed to update"
                ]);
            }
        } else {
            return response()->json([
                'status' => 'failed',
                'msg' => $validator->errors()->toArray(),
            ]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
    }

    public function uploadImage(Request $request)
    { 
        if($request->hasFile('filename')) {
            $image = $request->file('filename');
            $img_name = $request->post('fname'). "-" . $request->post('lname'). "-" .date('Y-m-d').'-'.'99270107462851533841'.'.'.$image->getClientOriginalExtension();
            $image->move('Uploads/image', $img_name);
            
        } else {
            dd('image set to default');
        }
    }
}
