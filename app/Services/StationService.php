<?php
namespace App\Services;

use App\Repositories\StationRepository;

class  StationService {

    private $stationRepository;

    /**
     * StationService constructor.
     * @param StationRepository $stationRepository
     */
    public function __construct(StationRepository $stationRepository)
    {
        $this->stationRepository = $stationRepository;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find(int $id) {
        return $this->stationRepository->find($id);
    }

    /**
     * @return mixed
     */
    public function getListOfStations() {
        return $this->stationRepository->getListOfStations();
    }

    public function getNearestStation($latitude, $longitude) {
        return $this->stationRepository->getNearestStation($latitude, $longitude);
    }

}
