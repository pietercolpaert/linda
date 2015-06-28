<?php namespace Tdt\Dapps\Controllers;

use Tdt\Dapps\Repositories\DatasetRepository;

class DatasetController extends \Controller
{
    public function __construct()
    {
        $this->datasetRepo = new DatasetRepository();
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return \View::make('dataset.create')
                    ->with('title', 'Add a dataset')
                    ->with(['fields' => $this->datasetRepo->getFields()]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $input = \Input::all();

        // TODO add it to the back-end
        // Build check if the title is unique or not
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
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
