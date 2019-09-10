
<!DOCTYPE html>
<html>
	<head>
		<meta charset="iso-8859-1">
		<title>USC Roski Loaner | Loaner :: Roski School of Fine Arts</title>

        <!-- Import All Universal StyleSheets -->
        <link rel="stylesheet" type="text/css" href="{{ asset('packages/css/style.css') }}">
    </head>

    <body>

            <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse">

                        {{ Menu::handler('main') }}
                        <ul class="nav navbar-nav navbar-right navbar-special">
                            <li class="dropdown">
                                <a id="curUser" class="dropdown-toggle"
                                   data-toggle="dropdown"
                                   href="#">
                                    <span class="current-user">{{ Auth::getUser()->fname }}</span> <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('profile.password') }}">Change Password</a>
                                    </li>
                                    <li><a id="help" href="{{ route('help') }}">Help</a></li>
                                    <li><a id = "logout"  href="{{ route('logout') }}">Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        @yield('jumbotron', '')
        <div class="container" id="main">
            {{ Notification::showAll() }}
            @yield('content', '')
        </div>
        


        <div id="footer">
            <p>
            		Copyright &copy; Roski School of Fine Arts <small>v2.21</small>
            </p>
            <p id="debug"></p>
        </div>
        <script>var appURL = '{{ parse_url(url(), PHP_URL_PATH) }}/';</script>
        <script src="{{ asset('packages/js/app.min.js') }}"></script>
        @if(array_key_exists('debug', Input::all()) || App::environment('local'))
        <script>
            window.onbeforeunload = function(e) {

                if ($Loaner.stopReload)
                {
                    $Loaner.stopReload = false;

                    var message = "Do you want to continue this redirect?", e = e || window.event;
                    // For IE and Firefox
                    if (e) {
                        e.returnValue = message;
                    }
                    // For Safari
                    return message;
                }
            };
        </script>
        @endif
        @yield('footer', '')
  </body>
</html>
      
        	

