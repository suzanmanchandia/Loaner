@include('reports.style')

<h4 class="text-center">System Activity Report</h4>
<h5 class="text-center">{{ $start->format(Config::get('formatting.dates.friendly')) }} -- {{ $end->format(Config::get('formatting.dates.friendly')) }}</h5>
<h6 class="text-center">System Summary</h6>
<div class="row">
    <div class="col-md-8 col-md-offset-2 col-xs-offset-0">
        <table class="table">
                <tr>
                    <th colspan="2">Issued Loans</th>
                    <th colspan="2">Overdue Loans</th>
                    <th colspan="2">Fines Incurred</th>
                </tr>
                <tr>
                    <td>Kits</td>
                    <td>{{ $kits }}</td>
                    <td>Kits</td>
                    <td>{{ count($kitsOverdue) }}</td>
                    <td>Kits</td>
                    <td>${{ $kitFines }}</td>
                </tr>
                <tr>
                    <td>Equipment</td>
                    <td>{{ $equipment }}</td>
                    <td>Equipment</td>
                    <td>{{ count($equipmentOverdue) }}</td>
                    <td>Equipment</td>
                    <td>${{ $equipmentFines }}</td>
                </tr>
        </table>
    </div>
</div>

<h6 class="text-center">Overdue Kit Details</h6>
<div class="table-responsive">
    <table class="table table-striped">
            <tr>
                <th>Kit ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
        @foreach ($kitsOverdue as $item)
            <tr>
                <td>{{ $item->kit->kitid }}</td>
                <td>{{ $item->userid }}</td>
                <td>{{ $item->user->fname }} {{ $item->user->lname }}</td>
                <td>{{ $item->kit->kit_desc }}</td>
                <td class="single-line">{{ $item->due_date ? $item->due_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td class="single-line">{{ $item->return_date ? $item->return_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td>{{ $item->fine }}</td>
            </tr>
        @endforeach
    </table>
</div>

<h6 class="text-center">Overdue Equipment Details</h6>

<div class="table-responsive">
    <table class="table table-striped">
            <tr>
                <th>Equipment ID</th>
                <th>User ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Fine</th>
            </tr>
        @foreach ($equipmentOverdue as $item)
            @if ($item->equipment)
            <tr>
                <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->equipment->model }}}" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipmentid }}</a></td>
                <td><a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($item->userNum) ) }}" href="javascript:;">{{ $item->userid }}</a></td>
                <td>{{ $item->user->fname }} {{ $item->user->lname }}</td>
                <td>{{ $item->equipment->model }}</td>
                <td class="single-line">{{ $item->due_date ? $item->due_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td class="single-line">{{ $item->return_date ? $item->return_date->format(Config::get('formatting.dates.short')) : '' }}</td>
                <td>{{ $item->fine }}</td>
            </tr>
            @endif
        @endforeach
    </table>
</div>