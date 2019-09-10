@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Equipment')) }}
@stop

@section('content')

<div class="row">
<div class="col-md-2">
    <div class="well">
        <p><a href="{{ route('equipment.create') }}" class="btn btn-primary btn-block">Create Equipment</a></p>
        <ul class="list-unstyled">
            <li><a href="{{ route('equipment.index') }}">List All</a></li>
            <li><a href="{{ route('equipment.index') }}?cond=5">Damaged Equipment</a></li>
            <li><a href="{{ route('equipment.index') }}?cond=6">Missing Equipment</a></li>
            <li><a data-href="{{ route('equipment.inactive') }}" href="javascript:;" data-dialog="lg" data-title="InActive Equipment">Inactive Equipment</a></li>
            <li><a href="{{ route('equipment.index') }}?d=-1">All Departments</a></li>
        </ul>
    </div>

</div>
<div class="col-md-10">

    {{ View::make( 'utils.search', array('route' => 'equipment.index', 'extra' => View::make( 'equipment.search') ) ) }}
    {{ $equipment->appends(Input::except('page'))->links() }}

<div class="table-responsive">
	<table class="table table-striped table-hover action-table sortable-table">
		<thead>
		<tr>
            {{ View::make( 'utils.table.header', array('name' => 'ID', 'id' => 'equipmentid', 'default' => 'equipmentid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Kit', 'id' => 'kitid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Model', 'id' => 'model' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Category', 'id' => 'category' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Location', 'id' => 'location' ) ) }}
            @if (Input::has('d'))
            {{ View::make( 'utils.table.header', array('name' => 'Department', 'id' => 'deptName' ) ) }}
            @endif
            {{ View::make( 'utils.table.header', array('name' => 'Loan Period', 'id' => 'loan_lengthEQ' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Status', 'id' => 'status' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Condition', 'id' => 'condition' ) ) }}
			<th></th>
		</tr>
		</thead>
		<tbody>
		@foreach($equipment as $item)
		<tr>
			<td data-toggle="tooltip" data-title="{{ $item->model }}">{{ $item->equipmentid }}</td>
            <td>@if ($item->kit)
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($item->kitid) ) }}" href="javascript:;">{{ $item->kit->kitid }}</a>
                @endif
            </td>
            <td>{{ $item->model }}</td>
            <td>{{ $item->category ? $item->category->equipCatName : '' }}</td>
            <td>{{ $item->location ? $item->location->locationName : '' }}</td>
            @if (Input::has('d'))
            <td>{{ $item->department ? $item->department->deptName : 'N/A' }}</td>
            @endif
            <td>{{ $item->loan_lengthEQ }}</td>
			<td><i class="fa fa-{{ $item->status == Equipment::STATUS_LOANED ? 'minus-circle danger' : 'check-circle success' }}"></i></td>
            <td>{{ $item->condition ? $item->condition->condName : '' }}</td>
			<td class="text-right">
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->model }}} {{ $item->equipmentid }}" title="View Info" class="btn btn-xs btn-default" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;"><i class="fa fa-book"></i></a>
                @include('equipment.actions', array('item' => $item))
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>
    {{ $equipment->appends(Input::except('page'))->links() }}
</div>
</div>
@stop
