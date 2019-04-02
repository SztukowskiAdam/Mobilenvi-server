<?php

namespace App\Http\Controllers;

use App\Services\DataService;
use App\Services\StationService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Models\Station;

class StationController extends Controller
{
    private $dataService, $stationService;

    /**
     * StationController constructor.
     * @param DataService $dataService
     * @param StationService $stationService
     */
    public function __construct(DataService $dataService, StationService $stationService)
    {
        Config::set('jwt.user', Station::class);
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => Station::class,
        ]]);

        $this->dataService = $dataService;
        $this->stationService = $stationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request) {
        $station = $this->stationService->find($request->id);

        try {
            if (! $token = JWTAuth::fromUser($station)) {
                return response()->json(['error' => 'Stacja nie istnieje!'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Nie można stworzyć tokenu!'], 500);
        }

        return response()->json(compact('token'));
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListOfStations() {
        $stations = $this->stationService->getListOfStations();

        return response()->json(compact('stations'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendData(Request $request) {

        try {
            if (!$station = JWTAuth::parseToken()->authenticate()) {
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentData() {
        $data = $this->dataService->getCurrentData();

        return response()->json(compact('data'), 200);
    }
}
