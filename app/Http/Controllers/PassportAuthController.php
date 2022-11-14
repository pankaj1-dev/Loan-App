<?php
namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends Controller
{

	 use ApiHelpers; 

    /**
     * Registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), $this->registerValidationRules());
	    if ($validator->passes()) {
 
	        $user = User::create([
	            'name' => $request->name,
	            'email' => $request->email,
	            'password' => bcrypt($request->password)
	        ]);
	       
	        $token = $user->createToken('LaravelAuthApp')->accessToken;

	        return $this->onSuccess(['token' => $token], 'Registered Successfully');
	    }
	    return $this->onError(400, $validator->errors());
    }
 
    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return $this->onSuccess(['token' => $token], 'Loggedin Successfully');
        }
        return $this->onError(401, 'Unauthorized Access');
    } 
}