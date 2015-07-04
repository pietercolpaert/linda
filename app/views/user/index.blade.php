@extends('layout.master')

@section('content')

    <div class='row header'>
        <div class="col-sm-7">
            <h3>Manage your users</h3>
        </div>
        @if (Tdt\Linda\Auth\Auth::hasAccess('users.manage'))
        <div class="col-sm-5 text-right">
            <a href='{{ URL::to('users/create') }}' class='btn btn-primary margin-left'
                <i class='fa fa-plus'></i> Add
            </a>
        </div>
        @endif
    </div>

    <div class="col-sm-12">

        <br/>

        @foreach($users as $user)
        <?php
            $user = $user->allOfType('foaf:Agent');
            $user = array_shift($user);

            $uri = $user->getUri();
            $pieces = explode('/', $uri);
            $id = array_pop($pieces);

            $id = str_replace('#agent', '', $id);
        ?>
            <div class="panel button-row panel-default">
                <div class="panel-body">
                    <div>
                        <div class='row'>
                            <div class='col-sm-8'>
                                <h4 class='dataset-title'>
                                    <a href='{{ $uri }}'>{{ $user->getLiteral('foaf:name')->getValue() }}</a>
                                </h4>
                            </div>
                            <div class='col-sm-4 text-right'>
                                <div class='btn-group'>
                                    @if(Tdt\Linda\Auth\Auth::hasAccess('users.manage'))
                                        <a href='{{ URL::to('users/' . $id .'/edit') }}' class='btn' title='Edit the dataset'>
                                            <i class='fa fa-pencil-square-o'></i> Edit
                                        </a>
                                    @endif
                                    @if(Tdt\Linda\Auth\Auth::hasAccess('users.manage'))
                                        <a href='{{ URL::to('users/' . $id) }}' class='btn delete' title='Delete this dataset'>
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
    </div>
@stop