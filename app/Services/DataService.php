<?php
namespace App\Services;

use App\Models\Data;
use Illuminate\Http\Request;

class  DataService {

    private $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function makeData(Request $request, $stationId) {
        $this->data->station_id = $stationId;
        $this->data->temperature = $request->temperature;
        $this->data->pressure = $request->pressure;

        return $this->data->save();
    }
}
