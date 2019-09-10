@section('content')
<div class="container well" style="margin:auto; margin-top:100px; width:300px; padding-left:35px; padding-right:35px;">
    <form name="change" action="{{ route('profile.password.process') }}" method="post" class="clearfix">
    	<legend><span style="font-size:20px;">Change Password</span></legend>
        <div>
            @if ($errors->any())
				<p class="alert alert-danger">
					@foreach ($errors->all() as $message)
						{{ $message }}<br>
					@endforeach
				</p>
			@endif
			
			@if ($status = Session::get('message'))
				<p class="alert alert-success">
					{{ $status }}
				</p>
			@endif			
			
			<dl class="ui-content">
				<dt>Old Password</dt>
				<dd><input id="opassword" name="opassword" type="password" required></dd>
				<dt>New Password</dt>
				<dd><input id="password" name="password" type="password" required></dd>
				<dt>Confirm New Password</dt>
				<dd><input id="password_confirmation" name="password_confirmation" type="password" required></dd>
				<dt></dt>
				<dd>
					<button id="submit" type="submit" name="submit" class="btn btn-primary">Submit</button>
				</dd>
			</dl>
		</div>
	</form>
</div>
	
		



@stop