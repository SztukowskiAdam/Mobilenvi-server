<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Station;
use App\Services\DataService;
use App\Services\StationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    private $dataService, $stationService;

    /**
     * UserController constructor.
     * @param DataService $dataService
     * @param StationService $stationService
     */
    public function __construct(DataService $dataService, StationService $stationService)
    {
        Config::set('jwt.user', User::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ]]);

        $this->dataService = $dataService;
        $this->stationService = $stationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     */
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

        $station = $this->stationService->getNearestStation($request->x, $request->y);
        if(empty($station)) return false;

        $data = $this->dataService->getDataFromStation($station->id);

        return response()->json(compact('data'),201);
    }
}
