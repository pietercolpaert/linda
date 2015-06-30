@extends('layout.master')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-sm-11">
            <h1>Edit a dataset</h1>
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
        <form class="form-horizontal edit-dataset" role="form">

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
                    <?php
                    $datasetGraph = $dataset->resource($uri);
                    ?>
                    @if ($field['required'] && $field['domain'] == 'dcat:Dataset')

                    <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                    </label>
                    <div class="col-sm-9">
                        @if($field['type'] == 'string')
                        <?php $val = $datasetGraph->getLiteral($field['short_sem_term'])->getValue(); ?>
                        <input type="text" class="form-control" id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>

                        @elseif($field['type'] == 'text')
                        <?php $val = $datasetGraph->getLiteral($field['short_sem_term'])->getValue(); ?>
                        <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($val)){{ $val }}@endif</textarea>
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
                        <?php
                        $literal = $datasetGraph->getLiteral($field['short_sem_term']);
                        $val = $literal;

                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <input type="text" class="form-control" id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>
                        @elseif($field['type'] == 'text')
                        <?php

                        $literal = $datasetGraph->getLiteral($field['short_sem_term']);
                        $val = $literal;


                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($val)){{ $val }}@endif</textarea>
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
                        <h4>Additional meta-data</h4>
                    </div>
                </div>
                <?php
                    $datasetGraph = $dataset->resource($uri . '#record');
                ?>
                <div class="form-group">
                    @foreach ($fields as $field)
                    @if (!$field['required'] && $field['domain'] == 'dcat:CatalogRecord')


                    <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                    </label>
                    <div class="col-sm-9">
                        @if($field['type'] == 'string')
                        <?php
                        $literal = $datasetGraph->getLiteral($field['short_sem_term']);
                        $val = $literal;

                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <input type="text" class="form-control" id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>


                        @elseif($field['type'] == 'text')
                        <?php
                        $literal = $datasetGraph->getLiteral($field['short_sem_term']);
                        $val = $literal;

                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($val)){{ $val }}@endif</textarea>
                        @endif
                        <div class='help-block'>
                            {{ $field['description'] }}
                        </div>
                    </div>


                    @endif
                    @endforeach
                </div>

                <?php
                    $datasetGraph = $dataset->resource($uri . '#distribution');
                ?>
                <div class="form-group">
                    @foreach ($fields as $field)
                    @if (!$field['required'] && $field['domain'] == 'dcat:Distribution')

                    <?php
                        $literal = $datasetGraph->getLiteral($field['short_sem_term']);
                        $val = $literal;

                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                    ?>

                    <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                    </label>
                    <div class="col-sm-9">
                        @if($field['type'] == 'string')
                        <input type="text" class="form-control" id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>

                        @elseif($field['type'] == 'text')
                        <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($val)){{ $val }}@endif</textarea>
                        @elseif($field['type'] == 'list')
                        <select id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" class="form-control">
                        @foreach($field['data'] as $option)

                            <?php $option = (array)$option; ?>
                            @if ($option[$field['value_name']] == $val)
                                <option value="{{ $option[$field['value_name']] }}" selected>{{ $option[$field['key_name']] }}</option>
                            @else
                                <option value="{{ $option[$field['value_name']] }}">{{ $option[$field['key_name']] }}</option>
                            @endif
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
        </div>
    </form>
    <div class="row">
    <div class="col-sm-3">
        <button type='submit' class='btn btn-cta btn-edit-dataset margin-left'><i class='fa fa-plus'></i> Edit</button>
    </div>
</div>
</div>
</div>
@stop