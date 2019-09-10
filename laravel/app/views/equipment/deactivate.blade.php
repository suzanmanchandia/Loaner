{{ Form::model( $equipment, array('route' => array('equipment.deactivate', $equipment->equipmentid), 'method' => 'delete', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('equipment.index') ) ) }}
<fieldset>
    <legend class="clearfix">
        {{{ Auth::getUser()->fname }}}
        {{{ Auth::getUser()->lname }}}
    </legend>

    <div class="form-group">
        <label for="disposalType" class="control-label col-sm-4">Disposal Type</label>
        <div class="col-sm-8">
        {{ Form::select('disposalType', Config::get('dropdowns.disposal'), null, array('class' => 'form-control', 'id' => 'disposalType', 'required' ) ) }}
        </div>
    </div>

    <div class="form-group">
        <label for="deactivationNotes" class="control-label col-sm-4">Disposal Details</label>
        <div class="col-sm-8">
        {{ Form::textarea('deactivationNotes', null, array('class' => 'form-control', 'id' => 'deactivationNotes', 'required', 'rows' => 2 ) ) }}
        </div>
    </div>

    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
    </div>

</div>

</fieldset>
{{ Form::close() }}