<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Auth\Auth;
use Tdt\Linda\Repositories\DatasetRepository;
use Tdt\Linda\Repositories\UserRepository;
use Tdt\Linda\Repositories\AppRepository;

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
        \EasyRdf_Namespace::set('odapps', 'http://semweb.mmlab.be/ns/odapps#');
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

            $name = '';

            if ($list == 'frequency') {
                $name = 'frequencies.json';
            } else if ($list == 'usecases') {
                return $this->createUsecaseList();
            } else if ($list == 'agents') {
                $name = 'agentTypes.json';
            } else if ($list == 'apps') {
                $name = 'appTypes.json';
            } else if ($list == 'geo') {
                $name = 'geonames.json';
            } else if ($list == 'datasets') {
                return $this->createDatasetList();
            } else if ($list == 'users') {
                return $this->createUserList();
            } else {
                return \Redirect::to('/');
            }

            return \Response::json(json_decode(file_get_contents($seed_data_path . $name)));
        }

    }

    private function createDatasetList()
    {
        $datasetRepo = new DatasetRepository();

        $datasetGraphs = $datasetRepo->getAll();

        $datasets = [];

        foreach ($datasetGraphs as $datasetGraph) {

            $datasetResource = $datasetGraph->allOfType('dcat:Dataset');

            // There's always only one in the graph
            $datasetResource = array_shift($datasetResource);

            $uri = $datasetResource->getUri();
            $title = $datasetResource->getLiteral('dc:title')->getValue();

            $dataset = [ 'name' => $title . ' - ' . $uri, 'url' => $uri];

            $datasets[] = $dataset;
        }

        return $datasets;
    }

    private function createUserList()
    {
        $userRepo = new UserRepository();

        $userResources = $userRepo->getAll();

        $users = [];

        foreach ($userResources as $userResource) {

            $userResource = $userResource->allOfType('foaf:Agent');

            // There's always only one in the graph
            $userResource = array_shift($userResource);

            $uri = $userResource->getUri();
            $name = $userResource->getLiteral('foaf:name')->getValue();
            $email = $userResource->getLiteral('foaf:mbox');

            if (!empty($email)) {
                $dataset = [ 'name' => $name . ' - ' . $email->getValue(), 'url' => $uri];
            } else {
                $dataset = [ 'name' => $name . ' - ' . $uri, 'url' => $uri];
            }

            $users[] = $dataset;
        }

        return $users;
    }

    private function createUsecaseList()
    {
        $seed_data_path = app_path() . '/database/seeds/data/';

        $name = 'usecases.json';

        $usecases = json_decode(file_get_contents($seed_data_path . $name));

        // Fetch all of the apps
        $appsRepo = new AppRepository();

        $appGraphs = $appsRepo->getAll();

        foreach ($appGraphs as $appGraph) {
            foreach ($appGraph->allOfType('odapps:Application') as $appResource) {
                $uri = $appResource->getUri();
                $title = $appGraph->getLiteral($uri, 'dc:title')->getValue();

                $usecases[] = ['name' => $title, 'url' => $uri];
            }
        }

        return \Response::json($usecases);
    }
}
