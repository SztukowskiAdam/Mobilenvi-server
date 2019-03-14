<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Station;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function __construct()
    {
        Config::set('jwt.user', User::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ]]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Email i hasło do siebie nie pasują!'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Problem z serwerem. Spróbuj później!'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['Użytkownik nie został znaleziony'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            return response()->json(['Token się przedawnił!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['Token jest niewłaśniwy!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['Token nie istnieje!'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

    public function userLocationData(Request $request) {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['Użytkownik nie został znaleziony'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['Token się przedawnił!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['Token jest niewłaśniwy!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['Token nie istnieje!'], $e->getStatusCode());

        }

        $station = Station::orderByRaw('ABS(position_x - '.$request->x.') + ABS(position_y - '.$request->y.')')->first();

        if(empty($station)) return false;

        $data = Data::where('station_id', $station->id)->orderBy('created_at', 'DESC')->get();

        return response()->json(compact('data'),201);
    }
}
