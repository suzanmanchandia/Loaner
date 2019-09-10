@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Kits')) }}
@stop

@section('content')
<div class="row">
<div class="col-md-2">
    <div class="well">
        <p><a href="{{ route('kits.create') }}" class="btn btn-primary btn-block">Create Kit</a></p>
        <ul class="fa-ul">
            <li><i class="fa-li fa fa-list"></i> <a href="{{ route('kits.index') }}">List All</a></li>
            <li><i class="fa-li fa fa-list"></i> <a href="javascript:;" data-href="{{ route('kits.inactive') }}" data-dialog="lg" data-title="InActive Kits">List Inactive Kits</a></li>
            <li><i class="fa-li fa fa-list"></i> <a href="{{ route('kits.index') }}?d=-1">All Departments</a></li>
        </ul>
    </div>

</div>
<div class="col-md-10">

    {{ View::make( 'utils.search', array('route' => 'kits.index') ) }}
    {{ $kits->appends(Input::except('page'))->links() }}
<div class="table-responsive">
	<table class="table table-striped table-hover action-table sortable-table">
		<thead>
		<tr>
            {{ View::make( 'utils.table.header', array('name' => 'ID', 'id' => 'kitid', 'default' => 'kitid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Description', 'id' => 'kit_desc' ) ) }}
            @if (Input::has('d'))
            {{ View::make( 'utils.table.header', array('name' => 'Department', 'id' => 'deptName' ) ) }}
            @endif
            {{ View::make( 'utils.table.header', array('name' => 'Loan Period', 'id' => 'loan_length' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Status', 'id' => 'status' ) ) }}
			<th></th>
		</tr>
		</thead>
		<tbody>
		@foreach($kits as $kit)
		<tr>
			<td>{{ $kit->kitid }}</td>
			<td>{{ $kit->kit_desc }}</td>
            @if (Input::has('d'))
            <td>{{ $kit->department ? $kit->department->deptName : 'N/A' }}</td>
            @endif
            <td>{{ $kit->loan_length }}</td>
            <td><i class="fa fa-{{ $kit->status == Kit::STATUS_LOANED ? 'minus-circle danger' : 'check-circle success' }}"></i></td>
			<td class="text-right">
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $kit->kit_desc }}}" title="View Info" class="btn btn-xs btn-default" data-href="{{ route( 'kits.show', array($kit->kitid) ) }}" href="javascript:;"><i class="fa fa-book"></i></a>
                @include('kits.actions', array('kit' => $kit))
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>
    {{ $kits->appends(Input::except('page'))->links() }}
</div>
</div>
@stop
