@if($format == "custom")
<div class="form-group">
            {{ Form::open( array('id' => 'dateForm', 'route' => 'reports.setCustomDate', 'method' => 'post', 'class' => 'form-horizontal', 'data-toggle' => 'modal' ) ) }}
            <div class="col-sm-3">
            {{ Form::text('fromDate', $customFromDate, array('class' => 'form-control', 'data-type' => 'date', 'id' => 'fromDate', 'placeholder' => 'from Date' ) ) }}
            </div>
            <div class="col-sm-3">
            {{ Form::text('toDate', $customToDate, array('class' => 'form-control', 'data-type' => 'date', 'id' => 'toDate', 'placeholder' => 'to Date' ) ) }}
            </div>
            <input class="btn btn-default" id="submitButton" type="submit" value="Filter">
            {{ Form::close() }}
</div>
@endif
<h4 class="text-center">Deactivation Report</h4>
<h5 class="text-center">{{ $start->format(Config::get('formatting.dates.friendly')) }} -- {{ $end->format(Config::get('formatting.dates.friendly')) }}</h5>
<span class="pull-right">
    <a href="{{ route('reports.loans.pdf', $format) }}" class="btn btn-default">Export to PDF</a>
    <span class="btn btn-default btn-print">Print</span>
</span>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Type</th>
                <th>ID</th>
                <th>Name</th>
                <th>User ID</th>
                <th>Disposal Type</th>
                <th>Notes</th>
                <th>Department</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($notes as $item)
            <tr>
                <td>{{ $item->kitid ? 'Kit' : 'Equipment' }}</td>
                <td>{{ $item->kitid ? $item->kitid : $item->equipmentid }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->userid }}</td>
                <td>{{ $item->disposalType }}</td>
                <td>{{ $item->deactivationNotes }}</td>
                <td>{{ $item->department ? $item->department->deptName : '' }}</td>
                <td class="single-line">{{ $item->deactivate_date ? $item->deactivate_date->format(Config::get('formatting.dates.friendlyTime')) : '' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
$( "#submitButton" ).click(function() {
  alert( "Handler for .click() called." );
});
</script>