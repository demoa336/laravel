<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Laravel\Passport\Client;
use App\User;
use Route;
use Hash;
use Auth;
use DB;

class AuthController extends Controller
{

	private $client;
	private $noContent = 204;
	private $unauthorised = 401;

	public function __construct(){
		$this->client = Client::where('password_client', '1')->whereNull('user_id')->first();
	}

	public function login(Request $request){
    	$this->validate($request, [
    		'email' => 'required',
    		'password' => 'required'
    	]);
        return $this->issueToken($request, 'password');
    }

    public function refresh(Request $request){
    	$this->validate($request, [
    		'refresh_token' => 'required'
    	]);

    	return $this->issueToken($request, 'refresh_token');
    }

    public function register(Request $request){

		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|min:6'
		]);

		$user = User::create([
			'name' => request('name'),
			'email' => request('email'),
			'password' => Hash::make(request('password'))
		]);

		return $this->issueToken($request, 'password');
	}

	public function logout(Request $request){

		$accessToken = Auth::user()->token();

    	if($request->user()->token()) {
    		DB::table('oauth_refresh_tokens')
    		->where('access_token_id', $accessToken->id)
    		->update(['revoked' => true]);
    		$accessToken->revoke();

    		return response()->json(['message'=>'Successfully logged out!'], $this->noContent);
    	}

    	return response()->json(['error'=>'Unauthorised'], $this->unauthorised);
    }

    public function issueToken(Request $request, $grantType, $scope = "") {

		$http = new \GuzzleHttp\Client;
		$params = [
    		'grant_type' => $grantType,
    		'client_id' => $this->client->id,
    		'client_secret' => $this->client->secret,    		
    		'scope' => $scope,
    		'username' => $request->email, 
    		'password' => $request->password, 
    	];
    	$request->request->add($params);

		$tokenRequest = $request->create('/oauth/token', 'POST', $request->all());

		$response = Route::dispatch($tokenRequest);

        return $response;
	}
}
