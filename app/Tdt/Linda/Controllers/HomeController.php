<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Repositories\DatasetRepository;

class HomeController extends \Controller
{
    public function index()
    {
        return \View::make('layout.home')->with('title', 'LINDA');
    }
}
