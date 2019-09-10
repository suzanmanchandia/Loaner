@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'loans', 'title' => 'Reservations')) }}
@stop

@section('content')

<div class="row">
<div class="col-md-2">
    <div class="well">
        <p><a href="{{ route('reservations.create') }}" class="btn btn-primary btn-block">Issue Reservation</a></p>
        <ul class="list-unstyled">
            <li><a href="{{ route('reservations.index') }}">Active Reservations</a></li>
            <li><a href="{{ route('reservations.canceled') }}">Canceled Reservations</a></li>
        </ul>
    </div>

</div>
<div class="col-md-10">

    {{ View::make( 'utils.search', array('route' => 'reservations.index',  ) ) }}
    {{ $loans->appends(Input::except('page'))->links() }}

<div class="table-responsive">
	<table class="table table-striped table-hover action-table sortable-table">
		<thead>
		<tr>
            {{ View::make( 'utils.table.header', array('name' => 'Kit ID', 'id' => 'kitid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'EQ ID', 'id' => 'equipmentid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'User ID', 'id' => 'userid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Name', 'id' => 'lname' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Description', 'id' => 'desc' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Issued', 'id' => 'issue_date', 'default' => 'issue_date' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Due', 'id' => 'due_date' ) ) }}
            @if (Input::has('d'))
            {{ View::make( 'utils.table.header', array('name' => 'Department', 'id' => 'deptName' ) ) }}
            @endif
			<th></th>
		</tr>
		</thead>
		<tbody>
		@foreach($loans as $item)
		<tr>
            <td>@if ($item->kitid)
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($item->kitid) ) }}" href="javascript:;">{{ $item->kit->kitid }}</a>
                @endif
            </td>
            <td>@if ($item->equipmentid)
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->equipment->model }}}" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipment->equipmentid }}</a>
                @endif
            </td>
            <td>@if ($item->user)
                <a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($item->userNum) ) }}" href="javascript:;">{{ $item->userid }}</a>
                @endif
            </td>
            <td>@if ($item->user)
                {{ $item->user->fname }}
                {{ $item->user->lname }}
                @endif
            </td>
            <td>
                @if ($item->kit && $item->kitid)
                {{ $item->kit->kit_desc }}
                @endif
                @if ($item->equipment && $item->equipmentid)
                {{ $item->equipment->model }}
                @endif
            </td>
            <td class="single-line">
                @if ($item->issue_date)
                {{ $item->issue_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            <td class="single-line">
                @if ($item->due_date)
                {{ $item->due_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            @if (Input::has('d'))
            <td>{{ $item->department ? $item->department->deptName : 'N/A' }}</td>
            @endif
			<td class="text-right">
                <a data-toggle="tooltip" data-dialog="yes" data-title="Reservation Details" title="View Info" class="btn btn-xs btn-default" data-href="{{ route( 'reservations.show', array($item->lid) ) }}" href="javascript:;"><i class="fa fa-book"></i></a>
                @include('reservations.actions', array('item' => $item))
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>
    {{ $loans->appends(Input::except('page'))->links() }}
</div>
</div>
@stop
