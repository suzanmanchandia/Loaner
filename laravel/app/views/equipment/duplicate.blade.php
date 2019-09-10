@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => link_to_route('equipment.index', 'Equipment'), 'subtitle' => 'Duplicate Equipment' )) }}
@stop

@section('content')
{{ Form::model( $equipment, array('route' => array('equipment.store', $equipment->equipmentid), 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('equipment.index') ) ) }}

<div class="row">

    <div class="col-md-6">

        <div class="form-group">
            <label for="equipment-id" class="control-label col-sm-4">Equipment ID</label>
            <div class="col-sm-8">
                {{ Form::text('equipmentid', $equipment->suggestId($deptID), array('class' => 'form-control', 'id' => 'equipment-id', 'maxlength' => 15 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-kitid" class="control-label col-sm-4">Kit ID</label>
            <div class="col-sm-8">
                {{ Form::text('kitid', $equipment->kitid, array('class' => 'form-control', 'id' => 'equipment-kitid', 'maxlength' => 15, 'data-suggest' => 'yes', 'data-source' => route('kits.suggest') ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-ll" class="control-label col-sm-4">Loan Length</label>
            <div class="col-sm-8">
                {{ Form::input('number', 'loan_lengthEQ', $equipment->loan_lengthEQ, array('type' => 'number', 'class' => 'form-control', 'id' => 'equipment-ll', 'required', 'min' => 0 ) ) }}
                <span class="help-block with-errors">Default 3.</span>
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-manufacturer" class="control-label col-sm-4">Manufacturer</label>
            <div class="col-sm-8">
                {{ Form::text('manufacturer', $equipment->manufacturer, array('class' => 'form-control', 'id' => 'equipment-manufacturer' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-manufSerialNum" class="control-label col-sm-4">Manufacturer Serial #</label>
            <div class="col-sm-8">
                {{ Form::text('manufSerialNum', $equipment->manufSerialNum, array('class' => 'form-control', 'id' => 'equipment-manufSerialNum' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-manufactureDate" class="control-label col-sm-4">Manufacture Date</label>
            <div class="col-sm-8">
                {{ Form::text('manufactureDate', $equipment->manufactureDate ? $equipment->manufactureDate->format('n/j/Y') : '' , array('class' => 'form-control', 'data-type' => 'date', 'id' => 'equipment-manufactureDate' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-expectedLifetime" class="control-label col-sm-4">Expected Lifetime</label>
            <div class="col-sm-8">
                {{ Form::text('expectedLifetime', $equipment->expectedLifetime, array('class' => 'form-control', 'id' => 'equipment-expectedLifetime' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-model" class="control-label col-sm-4">Model</label>
            <div class="col-sm-8">
                {{ Form::text('model', $equipment->model, array('class' => 'form-control', 'id' => 'equipment-model' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-equipCatID" class="control-label col-sm-4">Category</label>
            <div class="col-sm-8">
                {{ Form::select('equipCatID', array('' => 'Select Category') + EquipmentCategory::query()->lists('equipCatName', 'equipCatID'), $equipment->equipCatID, array('class' => 'form-control', 'id' => 'equipment-equipCatID', 'required' ) ) }}
            </div>
        </div>

        <div class="form-group equipSubCatID">
            <label for="equipment-equipSubCatID" class="control-label col-sm-4">Sub Category</label>
            <div class="col-sm-8">
                {{ Form::select('equipSubCatID', array(), '', array('class' => 'form-control', 'id' => 'equipment-equipSubCatID', 'required', 'data-val' => $equipment->equipSubCatID ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-locationID" class="control-label col-sm-4">Location</label>
            <div class="col-sm-8">
                {{ Form::select('locationID', array('' => '---Select---') + Location::query()->lists('locationName', 'locationID'), $equipment->locationID, array('class' => 'form-control', 'id' => 'equipment-locationID', 'required' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-condID" class="control-label col-sm-4">Condition</label>
            <div class="col-sm-8">
                {{ Form::select('condID', array('' => '---Select---') + Condition::query()->lists('condName', 'condID'), $equipment->condID, array('class' => 'form-control', 'id' => 'equipment-condID', 'required' ) ) }}
            </div>
        </div>

    </div>
    <div class="col-md-6">

        <div class="form-group">
            <strong class="control-label col-sm-4">Department</strong>
            <div class="col-sm-8">
                    {{ Form::select('deptID', $departments, null, array('class' => 'form-control', 'id' => 'equipment-deptID' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <strong class="control-label col-sm-4">Access Areas</strong>
            <div class="col-sm-8">
                @foreach ($access as $area)
                <div class="checkbox col-sm-6" data-access-dept="{{{ $area->deptID }}}">
                    <label>
                        {{ Form::checkbox('access[]', $area->id, in_array($area->id, $equipmentaccess), array('data-bv-choice' => 'true', 'data-bv-choice-min' => 1, 'data-bv-choice-message' => 'Please specify at least one access area', 'data-bv-container' => '.access-container' ) ) }}
                        {{ $area->accessarea }}
                    </label>
                </div>
                @endforeach
				<p class="clearfix access-container"></p>
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-desc" class="control-label col-sm-4">Description</label>
            <div class="col-sm-8">
                {{ Form::textarea('equipment_desc', $equipment->equipment_desc, array('class' => 'form-control', 'id' => 'equipment-desc', 'rows' => 3  ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="equipment-notes" class="control-label col-sm-4">Notes</label>
            <div class="col-sm-8">
                {{ Form::textarea('notes', $equipment->notes, array('class' => 'form-control', 'id' => 'equipment-notes', 'rows' => 3,  ) ) }}
            </div>
        </div>


    </div>

</div>

<fieldset>
    <legend>Purchase Information</legend>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="equipment-purchaseDate" class="control-label col-sm-4">Purchase Date</label>
                <div class="col-sm-8">
                    {{ Form::text('purchaseDate',  $equipment->purchaseDate ? $equipment->purchaseDate->format('n/j/Y') : '', array('class' => 'form-control', 'data-type' => 'date', 'id' => 'equipment-purchaseDate' ) ) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="equipment-purchasePrice" class="control-label col-sm-4">Purchase Price</label>
                <div class="col-sm-8">
                    {{ Form::input('number', 'purchasePrice', $equipment->purchasePrice, array('class' => 'form-control', 'id' => 'equipment-purchasePrice', 'step' => '.01', 'data-bv-numeric' => 'true' ) ) }}
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Network Information</legend>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="equipment-ipAddress" class="control-label col-sm-4">IP Address</label>
                <div class="col-sm-8">
                    {{ Form::text('ipAddress', $equipment->ipAddress, array('class' => 'form-control', 'id' => 'equipment-ipAddress' ) ) }}
                </div>
            </div>
            <div class="form-group">
                <label for="equipment-macAddress" class="control-label col-sm-4">MAC Address</label>
                <div class="col-sm-8">
                    {{ Form::text('macAddress', $equipment->macAddress, array('class' => 'form-control', 'id' => 'equipment-macAddress' ) ) }}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="equipment-hostName" class="control-label col-sm-4">Host Name</label>
                <div class="col-sm-8">
                    {{ Form::text('hostName', $equipment->hostName, array('class' => 'form-control', 'id' => 'equipment-hostName' ) ) }}
                </div>
            </div>
            <div class="form-group">
                <label for="equipment-connectType" class="control-label col-sm-4">Connect Type</label>
                <div class="col-sm-8">
                    {{ Form::select('connectType', Config::get('dropdowns.connect_types'), $equipment->connectType, array('class' => 'form-control', 'id' => 'equipment-connectType' ) ) }}
                </div>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>Warranty Information</legend>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="equipment-warrantyInfo" class="control-label col-sm-4">Warranty</label>
                <div class="col-sm-8">
                    {{ Form::textarea('warrantyInfo', $equipment->warrantyInfo, array('class' => 'form-control', 'id' => 'equipment-warrantyInfo' ) ) }}
                </div>
            </div>
        </div>
    </div>
</fieldset>

<div class="row">
    <div class="col-sm-offset-2 col-sm-10"><button type="submit" class="btn btn-primary btn-lg">Submit</button></div>
</div>

{{ Form::close() }}
@stop

@section('footer')
@include('equipment.scripts')
@stop