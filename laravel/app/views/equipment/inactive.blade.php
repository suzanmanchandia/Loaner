

<div class="table-responsive">
	<table class="table table-striped table-hover action-table">
		<thead>
		<tr>
            {{ View::make( 'utils.table.header', array('name' => 'ID', 'id' => 'equipmentid', 'default' => 'equipmentid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Kit', 'id' => 'kitid' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Model', 'id' => 'model' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Category', 'id' => 'category' ) ) }}
            {{ View::make( 'utils.table.header', array('name' => 'Location', 'id' => 'location' ) ) }}
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
            <td>{{ $item->condition ? $item->condition->condName : '' }}</td>
			<td class="text-right">
                <a data-toggle="tooltip" title="Reactivate" data-method="put" data-confirm="Are you sure you want to reactivate this equipment?" class="btn btn-xs btn-success" href="{{ route( 'equipment.activate', array($item->equipmentid) ) }}"><i class="fa fa-share"></i></a>
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>