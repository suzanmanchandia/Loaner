{{ Form::model( $loan, array('route' => array('loans.update', $loan->lid), 'method' => 'put', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('loans.index') ) ) }}
<fieldset>

    <div class="form-group">
        <strong class="control-label col-sm-4">Current Fine</strong>
        <div class="col-sm-8">
        {{ Form::text('current_fine', $loan->fine, array('class' => 'form-control', 'id' => 'disposalType', 'disabled' ) ) }}
        </div>
    </div>

    <div class="form-group">
        <label for="fine" class="control-label col-sm-4">New Fine</label>
        <div class="col-sm-8">
        {{ Form::input('number', 'fine', null, array('class' => 'form-control', 'id' => 'fine', 'required', 'min' => 0 ) ) }}
        </div>
    </div>

    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
    </div>

</div>

</fieldset>
{{ Form::close() }}