<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Role; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use DB;
use Log;

class UserApiController extends Controller
{

 
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        $currentUser = Auth::user();
        return response()->json(['state' => 'success', 'message' => "login_success",'access_token' => compact('token'), 'data' => $currentUser], 200);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['user_not_found'], 404);
            }
            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                    return response()->json(['token_expired'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                    return response()->json(['token_invalid'], $e->getStatusCode());
            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
                    return response()->json(['token_absent'], $e->getStatusCode());
            }
            return response()->json(compact('user'));
    }

 

    public function register(Request $request)
        { 

            try {
           $role = Role::find($request->role_id);
            if($role){
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:6',
                    'role_id'=> 'required|numeric',
                ]);
            }else{
                $role = Role::select('id','name')->get();
                return response()->json(['state' => 'fail','message' => 'invalid_role', 'data'=>$role], 400);
            }


            if($validator->fails()){
                    return response()->json(['state' => 'fail','message' => $validator->errors()], 400);
            }
            $new_name = '';
            if ($request->hasFile('photo'))
            {
                  $file = $request->file('photo'); 
                  $new_name = rand() . '.' . $file->getClientOriginalExtension();
                  $file->move(public_path('photo-user'), $new_name);
            }

            $user = [      
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'photo' => $new_name
            ];
    
          $user =  User::create($user);  
          $token = JWTAuth::fromUser($user);
            return response()->json(['state' => 'success', 'message' => "user_created",'access_token' => compact('token'), 'data' => $user], 200);
        }catch (\Exception $e) {
            DB::rollback();
            Log::error('UserApiController@register: ' . $e->getMessage());
            return response()->json(['state' => 'fail', 'message' =>  $e->getMessage()], 401);
        }
 
        }

        public function listUser(){
            $user =  User::all(); 
            return response()->json(['state' => 'success', 'message' => "list_user", 'data' => $user], 200);
        }

        public function deleteUser(Request $request) {
 
            try {  
                $user =  User::find($request->user_id);
                if($user){
                    $user->delete();
                    return response()->json(['state' => 'success', 'message' => "user_delete", 'data' => $user], 200);
                }else{
                    return response()->json(['state' => 'fail', 'message' => "not_deleted", 'data' => $user], 401);

                }
            }catch (\Exception $e) {
                
                DB::rollback();
                Log::error('SurveyController@createSurvey: ' . $e->getMessage());
                return response()->json(['state' => 'fail', 'msg' => 'not_deleted'.$e->getMessage()], 401);
            }
        }

        public function updated(Request $request)
        { 
        try {
           $role = Role::find($request->role_id);
           if(empty($request->user_id) && !isset($request->user_id)){
            return response()->json(['state' => 'fail', 'message' =>  'user_id param_is_required'], 401);
           }else{
            if (!is_numeric($request->user_id)) {
                return response()->json(['state' => 'fail', 'message' =>  'user_id the_parameter_must_be_numeric'], 401);
         
            }else{
               $user = User::find($request->user_id);
               if(!$user){
                return response()->json(['state' => 'fail', 'message' =>  'user_id= '.$request->user_id.' does not exist'], 401);
         

               }
            }
           }
            if($role){
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'password' => 'string|min:6',
                    'role_id'=> 'required|numeric',
                    
                ]);
            }else{
                $role = Role::select('id','name')->get();
                return response()->json(['error' => 'invalid_role','message' => 'invalid_role', 'data'=>$role], 400);
            }


            if($validator->fails()){
                return response()->json(['state' => 'fail','message' => $validator->errors()], 400);
            }
            $new_name = '';
            if ($request->hasFile('photo'))
            {
                  $file = $request->file('photo'); 
                  $new_name = rand() . '.' . $file->getClientOriginalExtension();
                  $file->move(public_path('photo-user'), $new_name);
            }

            $user = DB::table('users')
            ->where('id', $request->user_id) 
            ->limit(1) 
            ->update( [ 
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
                'photo' => $new_name ]
            ); 
            return response()->json(['state' => 'success', 'message' => "user_updated", 'data' => $user], 200);
        }catch (\Exception $e) {
            DB::rollback();
            Log::error('UserApiController@updated: ' . $e->getMessage());
            return response()->json(['state' => 'fail', 'message' =>  $e->getMessage()], 401);
        }
 
        }
}
