<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Services\DataService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\Station;

class StationController extends Controller
{
    private $dataService;

    public function __construct(DataService $dataService)
    {
        Config::set('jwt.user', Station::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Station::class,
        ]]);

        $this->dataService = $dataService;
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

    public function getListOfStations() {
        $stations = Station::select('id')->pluck('id')->toArray();

        return response()->json(compact('stations'));
    }

    public function sendData(Request $request) {

        try {
            if (! $station = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['Stacja nie została znaleziona!'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['Token się przedawnił!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['Token jest niewłaśniwy!'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['Token nie istnieje!'], $e->getStatusCode());

        }

        return response()->json([$this->dataService->makeData($request, $station->id)], 200);
    }

    public function getCurrentData() {


            $data = Data::join(DB::raw('(SELECT station_id, MAX(created_at) created_at FROM data GROUP BY station_id) b'), function ($join) {
                $join->on('b.station_id', '=', 'data.station_id')->on('data.created_at', '=', 'b.created_at');
            })->join('stations as c', 'c.id', '=', 'data.station_id')
                ->select('data.station_id', 'data.temperature as temperature', 'data.pressure as pressure', 'c.position_x as latitude', 'c.position_y as longitude', 'c.name')
                ->get();



        return response()->json(compact('data'), 200);
    }
}
