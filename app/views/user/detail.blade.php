@extends('layout.master')

@section('content')

<div class="container">
<script id="turtle" type="text/turtle">
    {{ $turtle }}
</script>
<div data-rdftohtml-plugin='map'></div>

<div data-rdftohtml-plugin='ontology'></div>

<div data-rdftohtml-plugin='triples'></div>

<script type="text/javascript">
    var triples = document.getElementById("turtle").innerHTML;
    var config = {
        plugins: ['triples', 'map', 'ontology', 'paging']
    };
    rdf2html(triples, config);
</script>
</div>
@stop