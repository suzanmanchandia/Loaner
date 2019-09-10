<fieldset>
    <legend class="clearfix">
        <div class="row">
            <div class ="col-xs-9">
                Equipment Information : {{ $item->equipmentid }}
            </div>
            <div class="col-xs-3 text-right">
                @include('equipment.actions', array('item' => $item))
            </div>
        </div>
    </legend>

    <div class="row-fluid">
        <div class="col-md-6">
            <dl>
                <dt>
                    Equipment ID:
                </dt>
                <dd>
                    {{ $item->equipmentid }}
                </dd>
                <dt>Status:
                </dt>
                <dd>
                    <span class="label label-{{ $item->status != Equipment::STATUS_LOANED ? 'success' : 'danger' }}">{{ $item->friendlyStatus() }}</span>
                </dd>
                <dt>
                    Kit ID:
                </dt>
                <dd>
                    <a href="javascript:;" data-dialog="yes" data-title="{{{ $item->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($item->kitid) ) }}">{{ $item->kitid }}</a>
                </dd>
                <dt>
                    Department:
                </dt>
                <dd>
                    {{ $item->department->deptName }}
                </dd>
                <dt>
                    Loan Period:
                </dt>
                <dd>
                    {{ $item->loan_lengthEQ }}
                </dd>
                <dt>
                    Manufacturer:
                </dt>
                <dd>
                    {{ $item->manufacturer }}
                </dd>
                <dt>
                    Manufacturer Serial #:
                </dt>
                <dd>
                    {{ $item->manufSerialNum }}
                </dd>
                @if ($item->manufactureDate)
                <dt>
                    Manufacture Date:
                </dt>
                <dd>
                    {{ $item->manufactureDate->format(Config::get('formatting.dates.friendly')) }}
                </dd>
                @endif
                <dt>
                    Expected Lifetime:
                </dt>
                <dd>
                    {{ $item->expectedLifetime }}
                </dd>
                @if ($item->expirationDate)
                <dt>
                    Expiration Date:
                </dt>
                <dd>
                    {{ $item->expirationDate->format(Config::get('formatting.dates.friendly')) }}
                </dd>
                @endif
                <dt>
                    Model:
                </dt>
                <dd>
                    {{ $item->model }}
                </dd>
            </dl>
        </div>
        <div class="col-md-6">
            <dl>
                @if ($item->updated_on)
                <dt>
                    Kit Updated:
                </dt>
                <dd>
                    {{ $item->updated_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                <dt>
                    Category:
                </dt>
                <dd>
                    {{ $item->category->equipCatName }}
                </dd>
                <dt>
                    Sub Category:
                </dt>
                <dd>
                    {{ $item->subCategory->equipSubName }}
                </dd>
                <dt>
                    Condition:
                </dt>
                <dd>
                    {{ $item->condition->condName }}
                </dd>
                <dt>
                    Location:
                </dt>
                <dd>
                    {{ $item->location ? $item->location->locationName : '' }}
                </dd>
                <dt>
                    Access Areas:
                </dt>
                <dd>
                    <ol>
                        @foreach ($item->accessAreas as $area)
                        <li>{{ $area->accessarea }}</li>
                        @endforeach
                    </ol>
                </dd>
                @if ($item->created_on)
                <dt>
                    Equipment Added:
                </dt>
                <dd>
                    {{ $item->created_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                @if ($item->updated_on)
                <dt>
                    Equipment Modified:
                </dt>
                <dd>
                    {{ $item->updated_on->format(Config::get('formatting.dates.friendlyTime')) }}
                </dd>
                @endif
                <dt>
                    Description:
                </dt>
                <dd>
                    {{ $item->equipment_desc }}
                </dd>
                <dt>
                    Notes:
                </dt>
                <dd>
                    {{ $item->notes }}
                </dd>
            </dl>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Purchase Information</legend>
    <dl>
        @if ($item->purchaseDate)
        <dt>Purchase Date</dt>
        <dd>
            {{ $item->purchaseDate->format(Config::get('formatting.dates.friendly')) }}
        </dd>
        @endif
        <dt>Purchase Price</dt>
        <dd>
            {{ $item->purchasePrice }}
        </dd>
    </dl>
</fieldset>

<fieldset>
    <legend>Network Information</legend>
    <dl>
        <dt>IP Address</dt>
        <dd>
            {{ $item->ipAddress }}
        </dd>
        <dt>MAC Address</dt>
        <dd>
            {{ $item->macAddress }}
        </dd>
        <dt>Host Name</dt>
        <dd>
            {{ $item->hostName }}
        </dd>
        <dt>Connect Type</dt>
        <dd>
            {{ $item->connectType }}
        </dd>
    </dl>
</fieldset>

<fieldset>
	<legend>Warranty Information</legend>
	<dl>
		<dt>Warranty</dt>
		<dd>
			{{ nl2br($item->warrantyInfo) }}
		</dd>
	</dl>
</fieldset>