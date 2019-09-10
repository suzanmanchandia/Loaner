<fieldset>
    <legend class="clearfix">
        <div class="row">
            <div class ="col-xs-7">
                Loan Information : {{ $loan->lid }}
            </div>
            <div class="col-xs-5 text-right">
                @include('loans.actions', array('item' => $loan))
            </div>
        </div>
    </legend>

    <div class="row-fluid">
        <div class="col-md-6">
            <dl>
                <dt>
                    Department:
                </dt>
                <dd>
                    {{ $loan->department->deptName }}
                </dd>
                @if ($loan->hasKit())
                <dt>
                    Kit ID:
                </dt>
                <dd><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($loan->kitid) ) }}" href="javascript:;">{{ $loan->kit->kitid }}</a></dd>
                @elseif ($loan->hasEquipment())
                <dt>
                    Equipment ID:
                </dt>
                <dd><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->equipment->model }}}" data-href="{{ route( 'equipment.show', array($loan->equipmentid) ) }}" href="javascript:;">{{ $loan->equipmentid }}</a></dd>
                @endif
                <dt>
                    Default Loan Period:
                </dt>
                <dd>
                    {{ $loan->getDefaultPeriod() }}
                </dd>
                <dt>
                    Loan Period:
                </dt>
                <dd>
                    {{ $loan->getPeriod() }}
                </dd>
                <dt>
                    Issued By:
                </dt>
                <dd>
                    {{ $loan->issuedBy }}
                </dd>
                @if ($loan->issue_date)
                <dt>
                    Issued:
                </dt>
                <dd>
                    {{ $loan->issue_date->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                @if ($loan->due_date)
                <dt>
                    Due:
                </dt>
                <dd>
                    {{ $loan->due_date->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                @if ($loan->return_date)
                <dt>
                    Returned:
                </dt>
                <dd>
                    {{ $loan->return_date->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                <dt>
                    Access Areas:
                </dt>
                <dd>
                    <ul>
                        @foreach ($areas as $access)
                        <li>{{{ $access->accessarea }}}</li>
                        @endforeach
                    </ul>
                </dd>
                <dt>
                    Description:
                </dt>
                <dd>
                    {{ $description }}
                </dd>
                <dt>
                    Notes:
                </dt>
                <dd>
                    {{ $loan->notes }}
                </dd>
            </dl>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Equipment</legend>
    <dl>
        <dt>Listed Equipment</dt>
        <dd>
            @foreach ($equipment as $item)
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