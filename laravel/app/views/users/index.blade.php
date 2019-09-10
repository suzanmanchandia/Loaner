@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'users', 'title' => 'Users')) }}
@stop

@section('content')
{{--delete--}}
{{--@foreach($users as $user)--}}
{{--@if ($user->userid == "sampleworkstudy")--}}
{{--{{--}}
    {{--Debugbar::info("found ");--}}
    {{--Debugbar::info($user->userid);--}}
    {{--Debugbar::info("inside index with status: ");--}}
    {{--Debugbar::info($user->status);--}}
{{--}}--}}
{{--@endif--}}
{{--@endforeach--}}
{{--end delete--}}
<div class="row">
<div class="col-md-2">
    <div class="well">
        <p><a href="{{ route('users.create') }}" class="btn btn-primary btn-block">Create User</a></p>
        <ul class="fa-ul">
            <li><i class="fa-li fa fa-list"></i> <a href="{{ route('users.index') }}">List All</a></li>
            <li><i class="fa-li fa fa-list"></i> <a href="{{ route('users.index') }}?d=-1">All Departments</a></li>
            <li><i class="fa-li fa fa-list"></i> <a href="{{ route('users.index') }}?s=3">Deactivated Users</a></li>
        </ul>
    </div>

</div>
<div class="col-md-10">

    {{ View::make( 'utils.search', array('route' => 'users.index', 'extra' => View::make( 'users.search')) ) }}
    {{ $users->appends(Input::except('page'))->links() }}
<div class="table-responsive">
	<table class="table table-striped table-hover action-table sortable-table">
		<thead>
		<tr>
            {{ View::make( 'utils.table.header', array('name' => 'ID', 'id' => 'userid', 'default' => 'userid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Name', 'id' => 'fname' ) ) }}
            @if (Input::has('d'))
            {{ View::make( 'utils.table.header', array('name' => 'Department', 'id' => 'deptName' ) ) }}
            @endif
            {{ View::make( 'utils.table.header', array('name' => 'Email', 'id' => 'email' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Type', 'id' => 'role' ) ) }}
			<th class="text-right">
                @if(is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR))
				<a data-toggle="tooltip" title="Suspend All Students" class="btn btn-xs btn-success" href="{{ route( 'users.suspend.all' ) }}" data-confirm="Are you sure you want to suspend all students?" data-method="put"><i class="fa fa-check"></i></a>
                <a data-toggle="tooltip" title="Unsuspend All Students" class="btn btn-xs btn-danger" href="{{ route( 'users.unsuspend.all' ) }}" data-confirm="Are you sure you want to unsuspend all students?" data-method="put"><i class="fa fa-ban"></i></a>
                @endif
			</th>
		</tr>
		</thead>
		<tbody>
		@foreach($users as $user)
		<tr>
			<td>{{ $user->userid }}</td>
			<td>{{ $user->fname }} {{ $user->lname }}</td>
            @if (Input::has('d'))
            <td>{{ $user->department ? $user->department->deptName : 'N/A' }}</td>
            @endif
            <td>{{ $user->email }}</td>
            <td>{{ $user->getRole() }}</td>
			<td class="text-right">
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $user->fname }}} {{{ $user->lname }}}" title="View Info" class="btn btn-sm btn-default" data-href="{{ route( 'users.show', array($user->userNum) ) }}" href="javascript:;"><i class="fa fa-book"></i></a>
                @include('users.actions', array('user' => $user))
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>
    {{ $users->appends(Input::except('page'))->links() }}
</div>
</div>
@stop
