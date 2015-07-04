<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Auth\Auth;
use Tdt\Linda\Repositories\DatasetRepository;

class CatalogController extends \Controller
{
   public function index()
   {
        Auth::requirePermissions('catalog.view');

        $uri = \URL::to('/catalog#dcat');

        $graph = new \EasyRdf_Graph();
        $catalogR = $graph->resource($uri);

        $catalogR->addLiteral('dc:title', \Config::get('catalog.title'));
        $catalogR->addLiteral('dc:description', \Config::get('catalog.description'));
        $catalogR->addType('dcat:Catalog');

        $datasetRepo = new DatasetRepository();

        foreach ($datasetRepo->getAll() as $datasetGraph) {
            foreach ($datasetGraph->allOfType('dcat:Dataset') as $datasetR) {
                $graph->addResource($catalogR, 'dcat:dataset', $datasetR);
            }
        }

        $serializer = new \EasyRdf_Serialiser_Turtle();

        $turtle = $serializer->serialise($graph, 'turtle');

        return \View::make('catalog.detail')
                ->with('title', 'Catalog | Linda')
                ->with('turtle', $turtle);
   }
}
