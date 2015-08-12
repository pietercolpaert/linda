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
     <form class="form-horizontal add" role="form">

        <div class="col-sm-6 panel panel-default">

            <div class="form-group">
                <label class="col-sm-2 control-label">
                </label>
                <div class="col-sm-10">
                <h4>Dataset required meta-data</h4>
                </div>
            </div>
            <div class="form-group">
            @foreach ($fields as $field)
                @if ($field['required'] && $field['domain'] == 'dcat:Dataset')

                <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($field['type'] == 'string')
                    <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($field['default_value'])) value='{{ $field['default_value'] }}' @endif>
                    @elseif($field['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($field['default_value'])){{ $field['default_value'] }}@endif</textarea>
                    @endif
                    <div class='help-block'>
                        {{ $field['description'] }}
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
            @foreach ($fields as $field)
                @if (!$field['required'] && $field['domain'] == 'dcat:Dataset')

                <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($field['type'] == 'string')
                    <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($field['default_value'])) value='{{ $field['default_value'] }}' @endif>
                    @elseif($field['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($field['default_value'])){{ $field['default_value'] }}@endif</textarea>
                    @elseif($field['type'] == 'list')
                    <select id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}"  @if (!$field['single_value']) class="form-control multiSelect" @else class="form-control" @endif>
                        <option></option>
                        @foreach($field['data'] as $option)
                        <?php $option = (array)$option; ?>
                        <option value="{{ $option[$field['value_name']] }}">{{ $option[$field['key_name']] }}</option>
                        @endforeach
                    </select>
                    @endif
                    <div class='help-block'>
                        {{ $field['description'] }}
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
                <!--<h4>Additional meta-data</h4>-->
                </div>
            </div>
            <div class="form-group">
            @foreach ($fields as $field)
                @if (!$field['required'] && $field['domain'] == 'dcat:CatalogRecord')

                <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                </label>
                <div class="col-sm-9">
                    @if($field['type'] == 'string')
                    <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($field['default_value'])) value='{{ $field['default_value'] }}' @endif>
                    @elseif($field['type'] == 'text')
                    <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($field['default_value'])){{ $field['default_value'] }}@endif</textarea>
                    @elseif($field['type'] == 'list')
                    <select id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" @if (!$field['single_value']) class="form-control multiSelect" @else class="form-control" @endif>
                        @foreach($field['data'] as $option)
                        <?php $option = (array)$option; ?>
                        <option value="{{ $option[$field['value_name']] }}" class="omitted">{{ $option[$field['key_name']] }}</option>
                        @endforeach
                    </select>
                    @endif
                    <div class='help-block'>
                        {{ $field['description'] }}
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <!-- distribution -->
        <div class="form-group distribution-form">
            <label class="col-sm-2 control-label">
            </label>
            <div class="col-sm-8">
                <h4>Distributions</h4>
            </div>
            <div class="col-sm-2">
                <button type='button' class='btn btn-cta btn-add-distribution'><i class='fa fa-plus'></i> Add</button>
            </div>
        </div>
        <div id="distributions">

        </div>
        </div>
    </form>
</div>

<div class="row">
    <div class="col-sm-3">
        <button type='submit' class='btn btn-cta btn-add margin-left'><i class='fa fa-plus'></i> Add</button>
    </div>
</div>
</div>
@stop