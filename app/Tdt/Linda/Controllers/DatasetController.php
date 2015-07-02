<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Repositories\DatasetRepository;
use Tdt\Linda\Auth\Auth;

class DatasetController extends \Controller
{
    public function __construct()
    {
        $this->datasetRepo = new DatasetRepository();

        \EasyRdf_Namespace::set('linda', 'http://linda.mmlab.iminds.be/');
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

        $datasets = $this->datasetRepo->getAll($limit, $offset);

        return \View::make('dataset.index')
                ->with('title', 'List | Dataset')
                ->with('datasets', $datasets);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        Auth::requirePermissions('dataset.manage');

        $fields = $this->datasetRepo->getFields();

        $adjusted_fields = [];

        // Expand values in the fields list such as external lists
        foreach ($fields as $field) {
            if ($field['type'] == 'list') {
                if (substr($field['values'],0, 4) == 'http') {
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

        return \View::make('dataset.create')
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
        Auth::requirePermissions('dataset.manage');

        $input = \Input::all();

        // Add current user
        $user = \Sentry::getUser()->toArray();
        $input['user'] = $user['email'];

        $rules = $this->datasetRepo->getRules();

        $validator = \Validator::make($input, $rules);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->datasetRepo->add($input);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        Auth::requirePermissions('dataset.manage');

        $id = \Request::segment(2);

        $dataset = $this->datasetRepo->get($id);

        if (empty($dataset)) {
            \Redirect::to('datasets');
        }

        $fields = $this->datasetRepo->getFields();
        $adjusted_fields = [];

        // Expand values in the fields list such as external lists
        foreach ($fields as $field) {
            if ($field['type'] == 'list') {
                if (substr($field['values'],0, 4) == 'http') {
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

        $uri = \URL::to('/' . $id);

        return \View::make('dataset.edit')
                ->with('title', 'Edit | Linda')
                ->with('dataset', $dataset)
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
        Auth::requirePermissions('dataset.manage');

        $input = \Input::all();

        // Add current user
        $user = \Sentry::getUser()->toArray();
        $input['user'] = $user['email'];

        $rules = $this->datasetRepo->getRules();

        $validator = \Validator::make($input, $rules);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->datasetRepo->update($id, $input);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Auth::requirePermissions('dataset.manage');

        $this->datasetRepo->delete($id);
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
