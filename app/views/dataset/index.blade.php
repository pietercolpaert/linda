@extends('layout.master')

@section('content')

    <div class='row header'>
        <div class="col-sm-7">
            <h3>Manage your datasets</h3>
        </div>
        <div class="col-sm-5 text-right">
            <a href='{{ URL::to('datasets/create') }}' class='btn btn-primary margin-left'
                <i class='fa fa-plus'></i> Add
            </a>
        </div>
    </div>

    <div class="col-sm-12">

        <br/>

        @foreach($datasets as $datasetGraph)
        <?php
            $datasets = $datasetGraph->allOfType('dcat:Dataset');
            $dataset = array_shift($datasets);

            $uri = $dataset->getUri();
            $pieces = explode('/', $uri);
            $id = array_pop($pieces);
        ?>
            <div class="panel button-row panel-default">
                <div class="panel-body">
                    <div>
                        <div class='row'>
                            <div class='col-sm-8'>
                                <h4 class='dataset-title'>
                                    <a href='{{ $uri }}'>{{ $dataset->getLiteral('dc:title')->getValue() }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                <div class='btn-group'>
                                    @if(Tdt\Dapps\Auth\Auth::hasAccess('datasets.manage'))
                                        <a href='{{ URL::to('datasets/' . $id .'/edit') }}' class='btn' title='Edit the dataset'>
                                            <i class='fa fa-eye'></i> Edit
                                        </a>
                                    @endif
                                    @if(Tdt\Dapps\Auth\Auth::hasAccess('datasets.manage'))
                                        <a href='{{ URL::to('datasets/' . $id . '/delete') }}' class='btn delete' title='Delete this dataset'>
                                            <i class='fa fa-times icon-only'></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <br/>
        <a href='#' class='introjs pull-right'>
             <i class='fa fa-lg fa-question-circle'></i>
        </a>
    </div>
@stop