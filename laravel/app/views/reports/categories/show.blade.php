<h4 class="text-center">{{ $category->equipCatName }}</h4>
<h5 class="text-center">{{ date(Config::get('formatting.dates.friendlyTime')) }}</h5>

<form class="form-search form-inline autoload" method="get" action="{{ route('reports.categories.show', $category->equipCatID) }}">
	<div class="form-group">
		<label>
			Department:
			{{ Form::select('dept', array('' => 'All Departments', ) + $departments, $report_dept, array('class' => 'form-control auto-submit') ) }}
		</label>
		<input type="submit" class="btn btn-default sr-only" value="Filter">
	</div>
</form>

<span class="pull-right">
    <a href="{{ route('reports.categories.export', $category->equipCatID) }}" class="btn btn-default">Export to CSV</a>
    <span class="btn btn-default btn-print">Print</span>
</span>

<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#active" role="tab" data-toggle="tab">Active Equipment</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade in active" id="active">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Equipment</th>
                    <th>Owner</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Year Manufactured</th>
                </tr>
                </thead>
                <tbody>
                @foreach($equipment as $item)
                <tr>
                    <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->model }}} {{ $item->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipmentid }}</a></td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->getOwner() }}</td>
                    <td>{{ $item->location ? $item->location->locationName : '' }}</td>
                    <td>{{ $item->subCategory ? $item->subCategory->equipSubName : '' }}</td>
                    <td>{{ $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>