<span class="pull-right">
    <a href="{{ route('reports.loans.pdf', $format) }}" class="btn btn-default">Export to PDF</a>
    <span class="btn btn-default btn-print">Print</span>
</span>

@if($format == "custom")
<div class="form-group">
            {{ Form::open( array('id' => 'dateForm', 'route' => 'reports.setCustomDateLoan', 'method' => 'post', 'class' => 'form-horizontal', 'data-toggle' => 'modal' ) ) }}
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
<h4 class="text-center">System Activity Report</h4>
<h5 class="text-center">{{ $start->format(Config::get('formatting.dates.friendly')) }} -- {{ $end->format(Config::get('formatting.dates.friendly')) }}</h5>
<h6 class="text-center">System Summary</h6>
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-offset-0">
        <table class="table">
            <thead>
                <tr>
                    <th colspan="2">Issued Loans</th>
                    <th colspan="2">Overdue Loans</th>
                    <th colspan="2">Fines Incurred</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Kits</td>
                    <td>{{ $kits }}</td>
                    <td>Kits</td>
                    <td>{{ $kitsOverdue }}</td>
                    <td>Kits</td>
                    <td>${{ $kitFines }}</td>
                </tr>
                <tr>
                    <td>Equipment</td>
                    <td>{{ $equipment }}</td>
                    <td>Equipment</td>
                    <td>{{ $equipmentOverdue }}</td>
                    <td>Equipment</td>
                    <td>${{ $equipmentFines }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@foreach (array('Active' => $loans, 'Overdue' => $overdue, 'Returned' => $returned) as $title => $group)
<h6 class="text-center">{{ $title }} Loans</h6>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($group as $item)
            <tr>
                <?php if ($item->hasKit()): ?>
                <td>
                <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($item->kitid) ) }}" href="javascript:;">{{ $item->kit->kitid }}</a>
                </td>
                <td>Kit</td>
                <td><a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($item->userNum) ) }}" href="javascript:;">{{ $item->userid }}</a></td>
                <td>{{ $item->user->fname }} {{ $item->user->lname }}</td>
                <td>{{ $item->kit->kit_desc }}</td>
                <?php elseif ($item->hasEquipment()): ?>
                <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->equipment->model }}}" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipmentid }}</a></td>
                <td>Equipment</td>
                <td><a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($item->userNum) ) }}" href="javascript:;">{{ $item->userid }}</a></td>
                <td>{{ $item->user->fname }} {{ $item->user->lname }}</td>
                <td>{{ $item->equipment->model }}</td>
                <?php endif; ?>
                <td class="single-line">{{ $item->due_date ? $item->due_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td class="single-line">{{ $item->return_date ? $item->return_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td>{{ $item->fine }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endforeach
