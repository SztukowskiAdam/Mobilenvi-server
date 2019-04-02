<?php
namespace App\Services;

use App\Repositories\DataRepository;
use Illuminate\Http\Request;

class  DataService {

    private $dataRepository;

    /**
     * DataService constructor.
     * @param DataRepository $dataRepository
     */
    public function __construct(DataRepository $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * @param Request $request
     * @param $stationId
     * @return bool
     */
    public function makeData(Request $request, $stationId) {
        return $this->dataRepository->makeData($request, $stationId);
    }

    /**
     * @return mixed
     */
    public function getCurrentData() {
        return $this->dataRepository->getCurrentData();
    }

    public function getDataFromStation(int $stationId) {
        return $this->dataRepository->getDataFromStation($stationId);
    }
}
