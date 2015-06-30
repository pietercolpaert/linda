<?php namespace Tdt\Dapps\Controllers;

use Tdt\Dapps\Repositories\DatasetRepository;

class DerefController extends \Controller
{
    public function index($id)
    {
        $uri = \URL::to('/' . $id);

        $datasetRepo = new DatasetRepository();

        $graph = $datasetRepo->get($id);

        if (empty($graph)) {
            Redirect::to('/');
        }

        $serializer = new \EasyRdf_Serialiser_Turtle();

        $turtle = $serializer->serialise($graph, 'turtle');

        return \View::make('dataset.detail')
                ->with('title', 'Detail | Linda')
                ->with('turtle', $turtle);
    }
}
