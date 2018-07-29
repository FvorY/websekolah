<!DOCTYPE html>
<html>
    <head>

        @include('partials._head')
        @yield('extra_styles')

    </head>

    <body class="fixed-sidebar">
        <div id="wrapper">
            @include('partials._sidebar')

          <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('partials._topnav')
            
            @yield('content')

            @include('partials._footer')
          </div>
        </div>

        @include('partials._scripts')
        @yield('extra_scripts')
        
    </body>
</html>
