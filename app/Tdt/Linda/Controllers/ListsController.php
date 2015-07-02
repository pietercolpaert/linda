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
    public function index($list)
    {
        if (!isset($list)) {
            $datasets = $this->lists;

            return \View::make('dataset.index')
                ->with('title', 'List | Dataset')
                ->with('datasets', $datasets);
        } else {
            return "todo";
        }
        
    }

}
