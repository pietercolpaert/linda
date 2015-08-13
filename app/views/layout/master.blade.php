<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>{{ $title }}</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::script('js/main.js') }}
         <script src="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.js"></script>
        {{ HTML::script('js/rdf2html.js') }}
        {{ HTML::script('js/multipleInput.js') }}

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="DC.title" content="{{ $title }}"/>

        <link href='//fonts.googleapis.com/css?family=Varela+Round|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>
        {{ HTML::style('css/main.css') }}
        <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.3/leaflet.css" />
        {{ HTML::style('css/font-awesome.css') }}
        {{ HTML::script('js/distributions.js') }}
    </head>

    <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="/">LINDA</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li @if(empty(\Request::segment(1))) class="active" @endif><a href="/">Home</a></li>
            @if(Tdt\Linda\Auth\Auth::hasAccess('datasets.manage'))
            <li @if(\Request::segment(1) == 'datasets') class="active" @endif><a href="/datasets">Datasets</a></li>
            @endif
            @if(Tdt\Linda\Auth\Auth::hasAccess('apps.manage'))
            <li @if(\Request::segment(1) == 'apps') class="active" @endif><a href="/apps">Apps</a></li>
            @endif
            @if(Tdt\Linda\Auth\Auth::hasAccess('users.manage'))
            <li @if(\Request::segment(1) == 'users') class="active" @endif><a href="/organizations">Organizations</a></li>
            @endif
          </ul>
          <ul class="nav navbar-nav navbar-right">
            @if (Sentry::check())
              <li><a href="/logout">Logout</a></li>
            @else
              <li><a href="/login">Login</a></li>
            @endif
          </ul>
        </div>
      </div>
    </nav>

    <div class="container">
     @yield('content')
    </div>
    </body>
</html>