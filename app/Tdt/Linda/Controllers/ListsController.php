<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Auth\Auth;

class ListsController extends \Controller
{
    public function __construct()
    {
        // Enable things like: http://linda/lists/frequencies.json?q=trien
        // cfr. https://github.com/iRail/hyperRail/blob/master/app/controllers/StationController.php#L44

        $this->lists = [
            "frequencies" => [],
            "organizationtypes" => [],
            "uses" => []
        ];

        \EasyRdf_Namespace::set('linda', 'http://semweb.mmlab.be/ns/linda#');
    }

    /**
     * Display a listing of the lists available
     *
     * @return Response
     */
    public function index($list = null)
    {
        if (!isset($list)) {
            $datasets = $this->lists;
            return $this->lists;
        } else {

            $seed_data_path = app_path() . '/database/seeds/data/';
            if ($list == 'frequency') {
                return \Response::json(json_decode(file_get_contents($seed_data_path . 'frequencies.json')));
            } else if ($list == 'usecases') {
                return \Response::json(json_decode(file_get_contents($seed_data_path . 'usecases.json')));
            } else {
                return \Redirect::to('/');
            }
        }

    }

}
