@section('content')

<fieldset>
<legend>Forgot Password <a class="pull-right" href="{{ route('login') }}">Go Back to Sign in</a></legend>

			@if ($errors->any())
				<p class="alert alert-danger">
					@foreach ($errors->all() as $message)
						{{ $message }}<br>
					@endforeach
				</p>
			@endif
			{{ Notification::showAll() }}
		Enter your username<br>
			<form action='{{ route('login.forgot.process') }}' method='post'>
					<input type="text" name="userid" id="userid"/>
					<input type='submit' name="submit" id="submit"  class="btn btn-primary" style="margin-bottom:9px;" value="Submit"/>
			</form>
</fieldset>
@stop