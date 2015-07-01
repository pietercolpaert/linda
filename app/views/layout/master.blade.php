<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>{{ $title }}</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        {{ HTML::script('js/main.js') }}
         <script src="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script>
        {{ HTML::script('js/rdf2html.js') }}



        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>

        <link href='//fonts.googleapis.com/css?family=Varela+Round|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        {{ HTML::style('css/main.css') }}
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    </head>

    <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="/">DAPPS</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li @if(empty(\Request::segment(1))) class="active" @endif><a href="/">Home</a></li>
            <li @if(\Request::segment(1) == 'datasets') class="active" @endif><a href="/datasets">Datasets</a></li>
            <li @if(\Request::segment(1) == 'apps') class="active" @endif><a href="/apps">Apps</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

     @yield('content')

    </div>
    </body>
</html>