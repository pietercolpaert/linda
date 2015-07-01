<?php namespace Tdt\Dapps\Controllers;

use Tdt\Dapps\Repositories\DatasetRepository;

class HomeController extends \Controller
{
    public function index()
    {
        return \View::make('layout.home')->with('title', 'LINDA');
    }
}
