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
        <form class="form-horizontal edit" role="form">

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
                    $datasetResource = $dataset->resource($uri . '#dataset');
                    ?>
                    @if ($field['required'] && $field['domain'] == 'dcat:Dataset')

                    <label for="input_{{ $field['var_name'] }}" class="col-sm-3 control-label">
                        {{ $field['view_name'] }}
                    </label>
                    <div class="col-sm-9">
                        @if($field['type'] == 'string')
                        <?php
                            $val = $datasetResource->getLiteral($field['short_sem_term'])->getValue();
                        ?>
                        <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>

                        @elseif($field['type'] == 'text')
                        <?php $val = $datasetResource->getLiteral($field['short_sem_term'])->getValue(); ?>
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

                        if (!$field['single_value']) {
                            $values = $datasetResource->all($field['short_sem_term']);
                            $val = "";
                            foreach ($values as $value) {
                                $val .= $value->getValue() . ',';
                            }

                            $val = rtrim($val, ',');
                        } else {
                            $literal = $datasetResource->getLiteral($field['short_sem_term']);
                            $val = $literal;


                            if (!empty($literal)) {
                                $val = $literal->getValue();
                            }
                        }

                        ?>
                        <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>
                        @elseif($field['type'] == 'text')
                        <?php

                        $literal = $datasetResource->getLiteral($field['short_sem_term']);
                        $val = $literal;


                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <textarea class="form-control" rows=10 id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}">@if (isset($val)){{ $val }}@endif</textarea>
                        @elseif($field['type'] == 'list')
                        <?php

                        $literal = $datasetResource->getLiteral($field['short_sem_term']);
                        $val = $literal;


                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <select id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" class="form-control">
                        <option></option>
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

            <div class="col-sm-6 panel panel-default">

                <div class="form-group">
                    <label class="col-sm-2 control-label">
                    </label>
                    <div class="col-sm-10">
                        <h4>Additional meta-data</h4>
                    </div>
                </div>
                <?php
                    $datasetResource = $dataset->resource($uri);
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
                        $literal = $datasetResource->getLiteral($field['short_sem_term']);
                        $val = $literal;

                        if (!empty($literal)) {
                            $val = $literal->getValue();
                        }

                        ?>
                        <input type="text" @if (!$field['single_value']) class="form-control multiInput" @else class="form-control" @endif id="input_{{ $field['var_name'] }}" name="{{ $field['var_name'] }}" placeholder="" @if(isset($val)) value='{{ $val }}' @endif>


                        @elseif($field['type'] == 'text')
                        <?php
                        $literal = $datasetResource->getLiteral($field['short_sem_term']);
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

                <!-- distribution -->
                <div class="form-group">
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
                <?php
                $datasetResource = $dataset->resource($uri . '#dataset');
                $distributions = $datasetResource->all('dcat:distribution');

                $i = 0;
                ?>
                @foreach ($distributions as $distribution)
                <div class="form-group">
                    <label class="col-sm-2 control-label"></label>
                    <div class="col-sm-8">
                        <h4>Distribution</h4>
                    </div>
                    <div class="col-sm-2">
                        <button type='submit' class='btn btn-cta btn-remove-distribution'><i class='fa fa-times icon-only'></i></button>
                    </div>
                </div>
                <div class="form-group distribution">
                    <label for="input_title" class="col-sm-3 control-label">Title</label>
                    <div class="col-sm-9">
                        <?php
                        $distTitle = $distribution->getLiteral('dc:title');

                        if (!empty($distTitle)) {
                            $distTitleVal = $distTitle->getValue();
                        } else {
                            $distTitleVal = null;
                        }

                        ?>
                        <input name="distributionTitle" id="input_distributionTitle" class="form-control" value="{{ $distTitleVal }}">
                    </div>

                    <label for="input_license" class="col-sm-3 control-label">License</label>
                    <div class="col-sm-9">
                        <?php
                        $license = $distribution->getResource('dc:license');

                        if (!empty($license)) {
                            $licenseVal = $license->getUri();
                        } else {
                            $licenseVal = null;
                        }

                        ?>
                        <select name="license" id="input_license" class="form-control">
                        <option></option>
                        @foreach ($licenses as $license)
                            <?php $license = (array) $license; ?>
                            @if ($licenseVal == $license['url'])
                                <option value="{{ $license['url'] }}" selected>{{ $license['title'] }}</option>
                            @else
                                <option value="{{ $license['url'] }}">{{ $license['title'] }}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>

                    <label for="input_accessURL" class="col-sm-3 control-label">Access URL</label>
                    <div class="col-sm-9">
                        <?php
                        $accessURL = $distribution->getResource('dcat:accessURL');

                        $accessURLVal = null;

                        if (!empty($accessURL)) {
                            $accessURLVal = $accessURL->getUri();
                        }

                        ?>
                        <input name="accessURL" id="input_accessURL" class="form-control" value="{{ $accessURLVal }}">
                    </div>

                    <label for="input_downloadURL" class="col-sm-3 control-label">Download URL</label>
                    <div class="col-sm-9">
                        <?php
                        $downloadURL = $distribution->getResource('dcat:downloadURL');

                        $downloadURLVal = null;

                        if (!empty($downloadURL)) {
                            $downloadURLVal = $downloadURL->getUri();
                        }

                        ?>
                        <input name="downloadURL" id="input_downloadURL" class="form-control" value="{{ $downloadURLVal }}">
                    </div>
                </div>
                <?php $i++; ?>
                @endforeach
                </div>

            </div>
        </div>
    </form>
    <div class="row">
    <div class="col-sm-3">
        <button type='submit' class='btn btn-cta btn-edit margin-left'><i class='fa fa-plus'></i> Edit</button>
    </div>
</div>
</div>
</div>
@stop