<?php namespace Tdt\Dapps\Controllers;

use Tdt\Dapps\Repositories\DatasetRepository;

class HomeController extends \Controller
{
    public function index()
    {
        $datasetRepo = new DatasetRepository();

        return json_encode($datasetRepo->getAll());
    }
}
