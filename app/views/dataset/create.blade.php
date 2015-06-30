@extends('layout.master')

@section('content')

<div class="container">
<div class="row">
    <div class="col-sm-11">
        <h1>Add a dataset</h1>
    </div>
</div>
<div class="row">
    <div class='row'>
        <div class="col-sm-12">
            <div class="alert alert-danger error hide">
                <i class='fa fa-2x fa-exclamation-circle'></i> <span class='text'></span>
            </div>
        </div>
    </div>
     <form class="form-horizontal add-dataset" role="form">

        <div class="col-sm-6 panel panel-default">

            <div class="form-group">
                <label class="col-sm-2 control-label">
                </label>
                <div class="col-sm-10">
                <h4>Dataset required meta-data</h4>
                </div>
            </div>
            <div class="form-group">
            @foreach ($fields as $config)
                @if ($config['required'] && $config['domain'] == 'dcat:Dataset')

                <label for="input_{{ $config['var_name'] }}" class="col-sm-3 control-label">
                        {{ $config['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($config['type'] == 'string')
                    <input type="text" class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" placeholder="" @if(isset($config['default_value'])) value='{{ $config['default_value'] }}' @endif>
                    @elseif($config['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}"> @if (isset($config['default_value'])) {{ $config['default_value'] }}@endif</textarea>
                    @elseif($config['type'] == 'integer')
                    <input type="number" class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" placeholder="" @if(isset($config['default_value'])) value='{{ $config['default_value'] }}' @endif>
                    @elseif($config['type'] == 'boolean')
                    <input type='checkbox' class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" checked='checked'/>
                    @endif
                    <div class='help-block'>
                        {{ $config['description'] }}
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="form-group">
                <label class="col-sm-2 control-label">
                </label>
                <div class="col-sm-10">
                <h4>Dataset optional meta-data</h4>
                </div>
            </div>
            <div class="form-group">
            @foreach ($fields as $config)
                @if (!$config['required'] && $config['domain'] == 'dcat:Dataset')

                <label for="input_{{ $config['var_name'] }}" class="col-sm-3 control-label">
                        {{ $config['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($config['type'] == 'string')
                    <input type="text" class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" placeholder="" @if(isset($config['default_value'])) value='{{ $config['default_value'] }}' @endif>
                    @elseif($config['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}"> @if (isset($config['default_value'])) {{ $config['default_value'] }}@endif</textarea>
                    @endif
                    <div class='help-block'>
                        {{ $config['description'] }}
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        </div>

        <div class="col-sm-6 panel panel-default">

        <div class="form-group">
                <label class="col-sm-2 control-label">
                </label>
                <div class="col-sm-10">
                <h4>Additional meta-data</h4>
                </div>
            </div>
            <div class="form-group">
            @foreach ($fields as $config)
                @if (!$config['required'] && $config['domain'] != 'dcat:Dataset')

                <label for="input_{{ $config['var_name'] }}" class="col-sm-3 control-label">
                        {{ $config['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($config['type'] == 'string')
                    <input type="text" class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" placeholder="" @if(isset($config['default_value'])) value='{{ $config['default_value'] }}' @endif>
                    @elseif($config['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}"> @if (isset($config['default_value'])) {{ $config['default_value'] }}@endif</textarea>
                    @elseif($config['type'] == 'integer')
                    <input type="number" class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" placeholder="" @if(isset($config['default_value'])) value='{{ $config['default_value'] }}' @endif>
                    @elseif($config['type'] == 'boolean')
                    <input type='checkbox' class="form-control" id="input_{{ $config['var_name'] }}" name="{{ $config['var_name'] }}" checked='checked'/>
                    @endif
                    <div class='help-block'>
                        {{ $config['description'] }}
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        </div>

    </form>
</div>

<div class="row">
    <div class="col-sm-3">
        <button type='submit' class='btn btn-cta btn-add-dataset margin-left'><i class='fa fa-plus'></i> Add</button>
    </div>
</div>
</div>