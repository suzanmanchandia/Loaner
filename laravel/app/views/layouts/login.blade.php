
<!DOCTYPE html>
<html>
	<head>
		<meta charset="iso-8859-1">
		<title>USC Roski Loaner | Loaner :: Roski School of Fine Arts</title>
		
		<!-- Import All Universal StyleSheets -->
		<link rel="stylesheet" type="text/css" href="{{ asset('packages/css/style.css') }}">
    </head>

    <body>
		<div id="page">
		<div id="login-header">Loaner&nbsp; | &nbsp;USC Roski School of Fine Arts </div>
        
        <div class="container well login-container">
            @yield('content', 'Hello world')
        </div>
        <div id="footer">
            <p>
            		Copyright &copy; Roski School of Fine Arts
            </p>
        </div>
        </div><!-- end footer -->
        <!-- Import Global Javascripts -->
        <script src="{{ asset('packages/js/app.min.js') }}"></script>
      </div>
  </body>
</html>
      
        	

