@section('content')

    <form name="login" action="{{ route('login.process') }}" method="post" class="clearfix">
		<legend><span style="font-size:20px;">Sign in</span></legend>
		<div>
			@if ($errors->any())
				<p class="alert alert-danger">
					@foreach ($errors->all() as $message)
						{{ $message }}<br>
					@endforeach
				</p>
			@endif
            <div class="form-group">
                <label>Username</label>
                <input id="username" name="username" type="text" value="{{ Input::old('username') }}" required class="form-control">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input id="password" name="password" type="password" required class="form-control">
            </div>
			<p><button id="signin" type="submit" name="signin" class="btn btn-primary">Sign In</button></p>
		</div>
	</form>	
	<p><a class="clearfix" href="{{ route('login.forgot') }}">Forgot Password?</a></p>
	

@stop