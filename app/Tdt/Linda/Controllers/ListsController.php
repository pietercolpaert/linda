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
            if ($list == 'frequency') {
                return \Response::json(json_decode(file_get_contents(app_path() . '/database/seeds/data/frequencies.json')));
            } else {
                return \Redirect::to('/');
            }
        }

    }

}
