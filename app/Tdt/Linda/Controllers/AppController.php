<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Auth\Auth;
use Tdt\Linda\Repositories\AppRepository;

class AppController extends \Controller
{
    private $error_messages = array(
        'multipleuri' => "Not all provided URIs are valid.",
    );

    public function __construct()
    {
        $this->appRepo = new AppRepository();

        \EasyRdf_Namespace::set('odapps', 'http://semweb.mmlab.be/ns/odapps#');
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $limit = \Input::get('limit', 100);
        $offset = \Input::get('offset', 0);

        $apps = $this->appRepo->getAll($limit, $offset);

        return \View::make('app.index')
                ->with('title', 'List | Apps')
                ->with('apps', $apps);
    }

    public function show($id)
    {

        $graph = $this->appRepo->get($id . '#application');

        if (empty($graph)) {
            \Redirect::to('/');
        }

        $serializer = new \EasyRdf_Serialiser_Turtle();

        $turtle = $serializer->serialise($graph, 'turtle');

        return \View::make('dataset.detail')
                ->with('title', 'Detail | Linda')
                ->with('turtle', $turtle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        Auth::requirePermissions('apps.manage');

        $fields = $this->appRepo->getFields();

        $adjusted_fields = [];

        // Expand values in the fields list such as external lists
        foreach ($fields as $field) {
            if ($field['type'] == 'list') {
                if (substr($field['values'], 0, 4) == 'http') {
                    $data = json_decode($this->getDocument($field['values']));
                    $field['data'] = $data;
                } else {
                    $values = explode(',', $field['values']);

                    $data = [];

                    foreach ($values as $val) {
                        $data[] = ['name' => $val, 'value' => $val];
                    }

                    $field['data'] = $data;
                }
            }

            $adjusted_fields[] = $field;
        }

        return \View::make('app.create')
                    ->with('title', 'Add a dataset')
                    ->with(['fields' => $adjusted_fields]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        Auth::requirePermissions('apps.manage');

        $input = \Input::all();

        // Add current user
        $user = \Sentry::getUser()->toArray();
        $input['user'] = $user['email'];

        $rules = $this->appRepo->getRules();

        $validator = \Validator::make($input, $rules, $this->error_messages);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->appRepo->add($input);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        Auth::requirePermissions('apps.manage');

        $id = \Request::segment(2);

        $application = $this->appRepo->get($id . '#application');

        $application = $application->allOfType('odapps:Application');
        $application = array_shift($application);

        if (is_null($application)) {
            \Redirect::to('apps');
        }

        $fields = $this->appRepo->getFields();
        $adjusted_fields = [];

        // Expand values in the fields list such as external lists
        foreach ($fields as $field) {
            if ($field['type'] == 'list') {
                if (substr($field['values'], 0, 4) == 'http') {
                    $data = json_decode($this->getDocument($field['values']));
                    $field['data'] = $data;
                } else {
                    $values = explode(',', $field['values']);

                    $data = [];

                    foreach ($values as $val) {
                        $data[] = ['name' => $val, 'value' => $val];
                    }

                    $field['data'] = $data;
                }
            }

            $adjusted_fields[] = $field;
        }

        $uri = \URL::to('/users/' . $id);

        return \View::make('app.edit')
                ->with('title', 'Edit | Linda')
                ->with('application', $application)
                ->with('fields', $adjusted_fields)
                ->with('uri', $uri);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        Auth::requirePermissions('apps.manage');

        $input = \Input::all();

        $rules = $this->appRepo->getRules();

        $validator = \Validator::make($input, $rules, $this->error_messages);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->appRepo->update($id, $input);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Auth::requirePermissions('apps.manage');

        $this->appRepo->delete($id);
    }

    private function getDocument($uri)
    {
        // Create a CURL client
        $cURL = new \Buzz\Client\Curl();
        $cURL->setVerifyPeer(false);
        $cURL->setTimeout(30);

        // Get discovery document
        $browser = new \Buzz\Browser($cURL);
        $response = $browser->get(\URL::to($uri));

        // Document content
        return $response->getContent();
    }
}
