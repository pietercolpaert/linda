<?php namespace Tdt\Linda\Controllers;

use Tdt\Linda\Repositories\UserRepository;
use Tdt\Linda\Auth\Auth;

class UserController extends \Illuminate\Routing\Controller
{
    private $error_messages = array(
        'multipleuri' => "Not all provided URIs are valid.",
    );

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        Auth::requirePermissions('users.manage');

        $limit = \Input::get('limit', 100);
        $offset = \Input::get('offset', 0);

        $users = $this->userRepo->getAll($limit, $offset);

        return \View::make('user.index')
                ->with('title', 'List | Users')
                ->with('users', $users);
    }

    public function show($id)
    {
        Auth::requirePermissions('users.manage');

        $graph = $this->userRepo->get($id . '#agent');

        if (is_array($graph)) {
            return \Redirect::to('/users');
        }

        $serializer = new \EasyRdf_Serialiser_Turtle();

        $turtle = $serializer->serialise($graph, 'turtle');

        return \View::make('user.detail')
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
        Auth::requirePermissions('users.manage');

        $fields = $this->userRepo->getFields();

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

        return \View::make('user.create')
                    ->with('title', 'Add a user')
                    ->with(['fields' => $adjusted_fields]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        Auth::requirePermissions('users.manage');

        $input = \Input::all();

        $rules = $this->userRepo->getRules();

        $validator = \Validator::make($input, $rules, $this->error_messages);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->userRepo->add($input);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        Auth::requirePermissions('users.manage');

        $id = \Request::segment(2);

        $user = $this->userRepo->get($id . '#agent');

        if (empty($user)) {
            \Redirect::to('users');
        }

        $fields = $this->userRepo->getFields();
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

        $uri = \URL::to('/users/' . $id);

        return \View::make('user.edit')
                ->with('title', 'Edit a user| Linda')
                ->with('user', $user)
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
        Auth::requirePermissions('users.manage');

        $input = \Input::all();

        $rules = $this->userRepo->getRules();

        $validator = \Validator::make($input, $rules, $this->error_messages);

        if ($validator->fails()) {
            $message = $validator->messages()->first();
            \App::abort(400, $message);
        }

        $this->userRepo->update($id, $input);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Auth::requirePermissions('users.manage');

        $this->userRepo->delete($id);
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
