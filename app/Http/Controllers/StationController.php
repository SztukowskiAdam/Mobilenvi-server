<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\Station;

class StationController extends Controller
{
    public function __construct()
    {
        Config::set('jwt.user', Station::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Station::class,
        ]]);
    }

    public function authenticate(Request $request)
    {
        $station = Station::find($request->id);

        try {
            if (! $token = JWTAuth::fromUser($station)) {
                return response()->json(['error' => 'Stacja nie istnieje!'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Nie można stworzyć tokenu!'], 500);
        }

        return response()->json(compact('token'));
    }
    public function test() {
        $station = Station::find(2);

        $token = JWTAuth::fromUser($station);


        return $token;
    }

    public function sendData() {

        try {

            if (! $station = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['Użytkownik nie został znaleziony'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['Token się przedawnił!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['Token jest niewłaśniwy!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['Token nie istnieje!'], $e->getStatusCode());

        }

        return response()->json(compact('station'));
    }

}