<h4 class="text-center">{{ $user->fname }} {{ $user->lname }} - {{ $user->userid }}</h4>
<h5 class="text-center">{{ date(Config::get('formatting.dates.friendlyTime')) }}</h5>

<span class="pull-right">
    <a href="{{ route('reports.byuser.export', $user->userNum) }}" class="btn btn-default">Export to CSV</a>
    <span class="btn btn-default btn-print">Print</span>
</span>

<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#active" role="tab" data-toggle="tab">Active Loans</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade in active" id="active">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Loan Date</th>
                    <th>Barcode</th>
                    <th>Equipment</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Year Manufactured</th>
                </tr>
                </thead>
                <tbody>
                @foreach($active as $loan)
                @if ($loan->equipmentid && $loan->equipment)
                <tr>
                    <td>{{ $loan->issue_date ? $loan->issue_date->format(Config::get('formatting.dates.friendly')) : '' }}</td>
                    <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->equipment->model }}} {{ $loan->equipment->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($loan->equipment->equipmentid) ) }}" href="javascript:;">{{ $loan->equipment->equipmentid }}</a></td>
                    <td>{{ $loan->equipment->model }}</td>
                    <td>{{ $loan->equipment->location ? $loan->equipment->location->locationName : '' }}</td>
                    <td>{{ $loan->equipment->subCategory ? $loan->equipment->subCategory->equipSubName : '' }}</td>
                    <td>{{ $loan->equipment->getOriginal('manufactureDate') ? $loan->equipment->manufactureDate->format('Y') : '' }}</td>
                </tr>
                @elseif($loan->kitid && $loan->kit)
                @foreach($loan->kit->equipment as $item)
                <tr>
                    <td>{{ $loan->issue_date ? $loan->issue_date->format(Config::get('formatting.dates.friendly')) : '' }}</td>
                    <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->model }}} {{ $item->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipmentid }}</a></td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->location ? $item->location->locationName : '' }}</td>
                    <td>{{ $item->subCategory ? $item->subCategory->equipSubName : '' }}</td>
                    <td>{{ $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '' }}</td>
                </tr>
                @endforeach
                @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>