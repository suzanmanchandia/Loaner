<fieldset>
    <legend class="clearfix">
        <div class="row">
            <div class ="col-xs-9">
                Kit Details : {{ $kit->kitid }}
            </div>
            <div class="col-xs-3 text-right">
                <a data-toggle="tooltip" title="Edit Kit" class="btn btn-xs btn-info" href="{{ route( 'kits.edit', array($kit->kitid) ) }}"><i class="fa fa-pencil"></i></a>
                <a data-toggle="tooltip" title="Deactivate" class="btn btn-xs btn-danger" href="{{ route( 'kits.destroy', array($kit->kitid) ) }}" data-method="delete" data-confirm="Are you sure?"><i class="fa fa-minus-circle"></i></a>
                <a data-toggle="tooltip" title="Duplicate" class="btn btn-xs btn-success" href="{{ route( 'kits.duplicate', array($kit->kitid) ) }}"><i class="fa fa-copy"></i></a>
                <a data-toggle="tooltip" title="Issue Loan" class="btn btn-xs btn-warning" href="{{ route( 'loans.kits', array($kit->kitid) ) }}"><i class="fa fa-plus-square"></i></a>
            </div>
        </div>
    </legend>

    <div class="row-fluid">
        <div class="col-md-6">
            <dl>
                <dt>
                    Kit ID:
                </dt>
                <dd>
                    {{ $kit->kitid }}
                </dd>
                <dt>Status:
                </dt>
                <dd>
                    <span class="label label-{{ $kit->status == Kit::STATUS_ACTIVE ? 'success' : 'danger' }}">{{ $kit->friendlyStatus() }}</span>
                </dd>
                <dt>
                    Department:
                </dt>
                <dd>
                    {{ $kit->department->deptName }}
                </dd>
                <dt>
                    Loan Period:
                </dt>
                <dd>
                    {{ $kit->loan_length }}
                </dd>
                <dt>
                    Kit Added:
                </dt>
                <dd>
                    {{ $kit->created_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                @if ($kit->updated_on)
                <dt>
                    Kit Updated:
                </dt>
                <dd>
                    {{ $kit->updated_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                <dt>
                    Access Areas:
                </dt>
                <dd>
                    <ol>
                        @foreach ($kit->accessAreas as $area)
                        <li>{{ $area->accessarea }}</li>
                        @endforeach
                    </ol>
                </dd>

                <dt>
                    Description:
                </dt>
                <dd>
                    {{ $kit->kit_desc }}
                </dd>
                <dt>
                    Notes:
                </dt>
                <dd>
                    {{ $kit->notes }}
                </dd>
            </dl>
        </div>
    </div>
</fieldset>

@if ($kit->equipment->count())
<fieldset>
    <legend>Kit Equipment</legend>
    <dl>
        <dt>
            Listed Equipment
        </dt>
        <dd>

            @foreach ($kit->equipment as $item)
            <div class="col-md-6">
                <h4> <a href="javascript:;" data-dialog="yes" data-title="{{{ $item->model }}}" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}">{{ $item->model }} {{ $item->equipmentid }}</a></h4>
                <div class="well">
                <dl>
                    <dt>Equipment ID</dt>
                    <dd>{{ $item->equipmentid }}</dd>
                    <dt>Model</dt>
                    <dd>{{ $item->model }}</dd>
                    <dt>Manufacturer</dt>
                    <dd>{{ $item->manufacturer }}</dd>
                    <dt>Notes</dt>
                    <dd>{{ $item->notes }}</dd>
                </dl>
                </div>
            </div>
            @endforeach
        </dd>
    </dl>
</fieldset>
@endif