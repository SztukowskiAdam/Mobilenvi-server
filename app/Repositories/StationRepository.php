<?php
namespace App\Repositories;

use App\Models\Station;

class StationRepository {
    private $model;

    /**
     * StationRepository constructor.
     * @param Station $station
     */
    public function __construct(Station $station) {
        $this->model = $station;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id) {
        return $this->model->find($id);
    }

    /**
     * @return mixed
     */
    public function getListOfStations() {
        return $this->model->select('id')->pluck('id')->toArray();
    }

    public function getNearestStation($latitude, $longitude) {
        return $this->model->orderByRaw('ABS(position_x - '.$latitude.') + ABS(position_y - '.$longitude.')')->first();
    }
}
