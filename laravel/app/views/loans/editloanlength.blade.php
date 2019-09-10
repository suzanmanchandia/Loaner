{{ Form::model( $loan, array('route' => array('loans.updateloanlength', $loan->lid), 'method' => 'put', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('loans.index') ) ) }}
<fieldset>

    <div class="form-group">
        <strong class="control-label col-sm-4">Current Issue Date</strong>
        <div class="col-sm-8">
        {{ Form::text('current_fine', $loan->issue_date->format('m/d/Y'), array('class' => 'form-control', 'id' => 'disposalType', 'disabled' ) ) }}
        </div>
    </div>

    <div class="form-group">
        <label for="fine" class="control-label col-sm-4">Current Due Date</label>
        <div class="col-sm-8">
        {{ Form::text('current_fine', $loan->due_date->format('m/d/Y'), array('class' => 'form-control', 'id' => 'disposalType', 'disabled' ) ) }}
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-sm-4" for="loan-issue_date">New Due Date</label>
                           <div class="col-sm-8">
                               {{ Form::text('new_due_date', $loan->due_date->format('m/d/Y'), array('class' => 'form-control', 'id' => 'loan-new-due_date', 'required' )) }}
                           </div>
    </div>

    <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary btn-lg">Submit</button>
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
    </div>

</div>

</fieldset>
{{ Form::close() }}

<script>
$('#loan-new-due_date').datepicker({
                    autoclose: true,
                    startDate: '{{ date("m/d/Y",strtotime("tomorrow")) }}'
                }).on('hide', function() {
                    $(this).trigger('input');
                 });
</script>