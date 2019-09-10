

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Barcode</th>
            <th>Description</th>
            <th>Type</th>
            <th>Location</th>
            <th>Issued</th>
            <th>Due</th>
            <th>Manufactured</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loans as $loan)
        @if ($loan->equipmentid && $loan->equipment)
        <tr>
            <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->equipment->model }}} {{ $loan->equipment->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($loan->equipment->equipmentid) ) }}" href="javascript:;">{{ $loan->equipment->equipmentid }}</a></td>
            <td>{{ $loan->equipment->model }}</td>
            <td>{{ $loan->equipment->subCategory ? $loan->equipment->subCategory->equipSubName : '' }}</td>
            <td>{{ $loan->equipment->location ? $loan->equipment->location->locationName : '' }}</td>
            <td class="single-line">
                @if ($loan->issue_date)
                    {{ $loan->issue_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            <td class="single-line">
                @if ($loan->due_date)
                    {{ $loan->due_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            <td>{{ $loan->equipment->getOriginal('manufactureDate') ? $loan->equipment->manufactureDate->format('Y') : '' }}</td>
        </tr>
        @elseif($loan->kitid && $loan->kit)
        @foreach($loan->kit->equipment as $item)
        <tr>
            <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->equipment->model }}} {{ $loan->equipment->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($loan->equipment->equipmentid) ) }}" href="javascript:;">{{ $loan->equipment->equipmentid }}</a></td>
            <td>{{ $loan->equipment->model }}</td>
            <td>{{ $loan->equipment->subCategory ? $loan->equipment->subCategory->equipSubName : '' }}</td>
            <td>{{ $loan->equipment->location ? $loan->equipment->location->locationName : '' }}</td>
            <td class="single-line">
                @if ($loan->issue_date)
                    {{ $loan->issue_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            <td class="single-line">
                @if ($loan->due_date)
                    {{ $loan->due_date->format(Config::get('formatting.dates.short')) }}
                @endif
            </td>
            <td>{{ $loan->equipment->getOriginal('manufactureDate') ? $loan->equipment->manufactureDate->format('Y') : '' }}</td>
        </tr>
        @endforeach
        @endif
        @endforeach
        </tbody>
    </table>
</div>