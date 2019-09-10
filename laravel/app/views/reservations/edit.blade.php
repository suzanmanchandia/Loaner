{{ Form::model($loan, array('route' => array('reservations.update', $loan->lid), 'method' => 'put', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-refresh' => 'true' ) ) }}
<fieldset>
    <legend class="clearfix">
        Issue Reservation: {{{ $loan->lid }}}
    </legend>

    <div class="row" id="items">
        <div class="col-sm-12 loan-options">

            <div class="form-group">
                <label for="loan-userid" class="control-label col-sm-4">User ID</label>

                <div class="col-sm-8">
                    {{ Form::text('userid', null, array('class' => 'form-control', 'id' => 'loan-userid', 'disabled', ) ) }}
                </div>
            </div>

            @if ($loan->kitid)
            <div class="form-group">
                <label for="loan-kitid" class="control-label col-sm-4">Kit ID</label>

                <div class="col-sm-8">
                    {{ Form::text('kitid', null, array('class' => 'form-control', 'id' => 'loan-kitid', 'disabled', ) ) }}
                </div>
            </div>
            @else
            <div class="form-group">
                <label for="loan-equipmentid" class="control-label col-sm-4">Equipment ID</label>

                <div class="col-sm-8">
                    {{ Form::text('equipmentid', null, array('class' => 'form-control', 'id' => 'loan-equipmentid', 'disabled', ) ) }}
                </div>
            </div>
            @endif

            <div class="form-group">
                <label for="loan-notes" class="control-label col-sm-4">Notes</label>

                <div class="col-sm-8">
                    {{ Form::textarea('notes', '', array('class' => 'form-control', 'id' => 'loan-notes', 'rows' => 3, ) ) }}
                </div>
            </div>
            <div class="form-group">
                   <label class="control-label col-sm-4" for="loan-issue_date">Issue Date</label>
                   <div class="col-sm-8">
                       {{ Form::text('issue_date', $loan->issue_date->format('m/d/Y'), array('class' => 'form-control', 'id' => 'loan-issue_date', 'required' )) }}
                   </div>
            </div>
            <div class="form-group">
                    <label for="loan-length" class="control-label col-sm-4">Loan Length</label>
                    <div class="col-sm-8">
                       {{ Form::text('loan_length', null, array('class' => 'form-control', 'id' => 'loan-loan_length', 'required', ) ) }}
                    </div>
            </div>
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
    $(function(){

        var FormSettings = {
            // form
            form: $('.form-ajax')
        };

        $('#loan-issue_date').datepicker({
                    autoclose: true,
                    startDate: '{{ date("m/d/Y") }}'
                }).on('hide', function() {
                    $(this).trigger('input');
                 });


        // Validate individual loan items
        FormSettings.form
        .bind('ajaxValidate', function(e) {

            var $container = $('.equipment');

            // Loop through all containers
            for (var i = 0; i < $container.length; i++)
            {
                // get all input elements in container
                var $equipment = $('.equipment-wrapper', $container);

                if ($equipment.length != $equipment.filter('.bg-warning,.bg-danger,.scanned').length)
                {
                    new PNotify({
                        title: 'Error',
                        text: 'All equipment not damaged or missing needs to be checked in.',
                        type: 'error'
                    });
                    e.preventDefault();
                    $('#items').trigger('refresh');

                    return;
                }

            }

        });

        $('#items').on('click', '.equipment-actions .fa', function(){
            $parent = $(this).closest('.equipment-wrapper');
            if ($(this).hasClass('fa-wrench'))
            {
                if ($parent.hasClass('bg-warning'))
                {
                    $parent.removeClass('bg-warning bg-danger').addClass('bg-info');
                    $('.condition', $parent).text('Good');
                }
                else
                {
                    $parent.addClass('bg-warning').removeClass('bg-info bg-danger');
                    $('.condition', $parent).text('Damaged');
                }
            }
            if ($(this).hasClass('fa-times'))
            {
                if ($parent.hasClass('bg-danger'))
                {
                    $parent.removeClass('bg-warning bg-danger').addClass('bg-info');
                    $('.condition', $parent).text('Good');
                }
                else
                {
                    $parent.addClass('bg-danger').removeClass('bg-warning bg-info');
                    $('.condition', $parent).text('Missing');
                }
            }
            $('input', $parent).val($('.condition', $parent).text());
        }).on('click', '.item-close', function(){
            $parent = $(this).closest('fieldset');
            $parent.transitionOut({
                callback: function() {
                    $('#items').trigger('refresh');
                }
            });

        }).on('refresh', function(){
            $('.equipment-wrapper', this).removeClass('scanned');
            $('.inputs input', this).each(function(i, e) {
                $('.equipment-wrapper[data-id="'+$(e).val()+'"]').addClass('scanned');
            });

            // Update text
            $('#items .count').each(function(i, e) {
                $(e).text(i+1);
            });
            $('[type=submit]', FormSettings.form).prop('disabled', false);

        }).on('change blur keyup', '.inputs input', function(){
            $('#items').trigger('refresh');
        }).trigger('refresh');

    });
</script>