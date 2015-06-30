<?php namespace Tdt\Dapps\Controllers;

use Tdt\Dapps\Repositories\DatasetRepository;

class HomeController extends \Controller
{
    public function index()
    {
        $datasetRepo = new DatasetRepository();

        return \View::make('layout.home')->with('title', 'Linda');
    }
}
