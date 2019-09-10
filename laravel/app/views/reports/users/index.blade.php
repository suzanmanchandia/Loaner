@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Reports', 'subtitle' => 'Loan Activity (By User)')) }}
@stop

@section('content')

<div class="row">
    <div class="col-md-3">
        @include('reports.sidebar')

		<form class="form-search form-inline" method="get" action="">
			<div class="form-group">
				<label>
					Department:
					{{ Form::select('dept', array('' => 'All Departments', ) + $departments, $report_dept, array('class' => 'form-control auto-submit') ) }}
				</label>
				<input type="submit" class="btn btn-default sr-only" value="Filter">
			</div>
		</form>

        <div class="panel panel-default">
            <div class="panel-heading">Users</div>
            <div class="panel-body">
                <p><input type="search" class="form-control" data-filter=".list-group" data-items=".list-group-item"></p>

                <div class="list-group">
                    @foreach ($users as $user)
                    <div class="list-group-item" data-load="{{ route('reports.users.show', $user->userNum) }}" data-target="#fine-info">
                        <h5 class="list-group-item-heading">{{{ $user->fname }}} {{{ $user->lname }}} - {{{ $user->userid }}}</h5>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-9" id="fine-info">

    </div>
</div>
@stop

@section('footer')
@include('reports.scripts');
@stop

