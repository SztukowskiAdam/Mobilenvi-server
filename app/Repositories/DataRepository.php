<?php
namespace App\Repositories;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataRepository {
    private $model;

    public function __construct(Data $data) {
        $this->model = $data;
    }

    /**
     * @param Request $request
     * @param int $stationId
     * @return bool
     */
    public function makeData(Request $request, int $stationId) {
        $this->model->station_id = $stationId;
        $this->model->temperature = $request->temperature;
        $this->model->pressure = $request->pressure;

        return $this->model->save();
    }

    /**
     * @return mixed
     */
    public function getCurrentData() {

        return $this->model->join(
            DB::raw('(SELECT station_id, MAX(created_at) created_at FROM data GROUP BY station_id) b'), function ($join) {
                        $join->on('b.station_id', '=', 'data.station_id')->on('data.created_at', '=', 'b.created_at');
                    }
                )->join('stations as c', 'c.id', '=', 'data.station_id')
            ->select(
                'data.station_id',
                'data.temperature as temperature',
                'data.pressure as pressure',
                'c.position_x as latitude',
                'c.position_y as longitude',
                'c.name')
            ->get();
    }

    public function getDataFromStation(int $stationId) {
        return $this->model->where('station_id', $stationId)->orderBy('created_at', 'DESC')->get();
    }
}
