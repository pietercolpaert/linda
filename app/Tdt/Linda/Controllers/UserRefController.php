<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Repositories\DatasetRepository;
use Cartalyst\Sentry\Users\Eloquent\User;

class UserRefController extends \Controller
{
    public function index($id)
    {
        $user = User::where('first_name', $id)->get()->toArray();

        if (empty($user)) {
            \Redirect::to('/');
        }

        $uri = \URL::to('/users/' . $id);

        $graph = new \EasyRdf_Graph();
        $resource = $graph->resource($uri);

        $resource->addLiteral('foaf:firstName', $id);

        $serializer = new \EasyRdf_Serialiser_Turtle();

        $turtle = $serializer->serialise($graph, 'turtle');

        return \View::make('user.detail')
                ->with('title', 'Detail | Linda')
                ->with('turtle', $turtle);
    }
}
