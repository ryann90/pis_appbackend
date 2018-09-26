<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

  function randomNumber($length = 19) {

      return substr_replace(str_shuffle(str_repeat($x = "1029384756", ceil($length / strlen($x)))), 1, $length);
  }

  function guzzle($url, $param = [], $method){

       try{
         $client = new \GuzzleHttp\Client();
         $res = $client->request($method, $url, $param);
         return [
             'data' => json_decode($res->getBody(), true),
             'http_code' => $res->getStatusCode(),
             'header' => $res->getHeaderLine('content-type'),
         ]; 
     }catch(GuzzleHttp\Exception\ConnectException $e){

         Session::flash('custom_error', [
             'data' => 'Connection Lost. Some data may not be appeared. <a href="#" onclick="window.location.reload()">[Refresh]</a> again'
         ]);

     }catch (GuzzleHttp\Exception\RequestException $e) {

       Session::flash('custom_error', [
             'data' => 'Connection Lost. Some data may not be appeared. <a href="#" onclick="window.location.reload()">[Refresh]</a> again'
         ]);
         
     }

     return ['data' => 'CONN_ERR'];

  }

    function apiToken(){

      return bin2hex(openssl_random_pseudo_bytes(30));

    }

    function customToken($length = 32) {

      $x = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
      return substr(str_shuffle(str_repeat($x, ceil($length / strlen($x)))), 1, $length);

    }

    // FOR DB CONNECTION
    function clientConnect($hostname = null, $database = null, $username = null, $password = null){

        // DB::purge('client');

        Config::set('database.connections.client.host', $hostname);
        Config::set('database.connections.client.database', $database);
        Config::set('database.connections.client.username', $username);
        Config::set('database.connections.client.password', $password);

        // Rearrange the connection data
        // Connection path can be seen on database.php any changes will be reflected there
        DB::connection('client');

        // Ping the database. This will throw an exception in case the database does not exists.
        // Schema::connection('client')->getConnection()->reconnect();
    }

    // FOR TABLE CREATION OF TENANT

    function migrateClientTables($tbl){

        Schema::connection('client')->create($tbl.'_company', function ($table) {
            $table->increments('id');
            $table->string('company_id')->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->string('business_type')->nullable();
            $table->integer('setup_status')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_users', function ($table) {
            $table->increments('id');
            $table->string('user_id')->unique();
            $table->string('company_id');
            $table->string('email');       
            $table->tinyInteger('type')->default(1);            
            $table->string('password', 255);  
            $table->tinyInteger('isActive')->default(1); 
            $table->string('last_seen')->nullable();                                              
            $table->string('api_token')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('created_by')->default('PIS'); 
            $table->string('emp_num')->nullable();             
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_roles', function ($table) {
            $table->string('roles_id');
            $table->string('name');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_employee_details', function ($table){
            $table->increments('id');
            $table->string('user_id');
            $table->string('employee_num')->nullable();
            $table->string('fname', 100)->nullable();
            $table->string('mname', 100)->nullable();
            $table->string('lname', 100)->nullable();
            $table->string('suffix', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('zipcode')->nullable();
            $table->string('title_code')->nullable();
            $table->string('address', 500)->nullable();
            $table->string('other_address', 500)->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('tel_num')->nullable();
            $table->string('cell_num')->nullable();
            $table->string('work_location', 500)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('citizenship', 50)->nullable();
            $table->string('marital_status', 30)->nullable();
            $table->string('birthdate')->nullable();
            $table->string('birthplace', 500)->nullable();
            $table->string('image')->nullable()->default('default-avatar.png');
            $table->tinyInteger('is_archive')->default(0);
            $table->string('date_archive')->nullable();
            $table->string('reason')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_movement', function ($table){
            $table->increments('id');
            $table->string('user_id');
            $table->string('company_id');
            $table->string('added_by')->nullable();
            $table->string('position');
            $table->string('department_id')->nullable();
            $table->string('date_start')->nullable();
            $table->string('date_end')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_movement_file', function ($table){
            $table->increments('id');
            $table->string('company_id');
            $table->string('movement_id');
            $table->string('file')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });

        Schema::connection('client')->create($tbl.'_department', function ($table){
            $table->increments('id');
            $table->string('department_id')->nullable();
            $table->string('department_name')->nullable();
            $table->string('department_head')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }
?>