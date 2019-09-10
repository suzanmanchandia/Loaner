@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'loans', 'title' => link_to_route('loans.index', 'Loans'), 'subtitle' => 'Create New Loan' )) }}
@stop

@section('content')
{{ Form::open( array('route' => 'loans.store', 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('loans.index') ) ) }}

<div class="row">

    <div class="form-group">
        <label class="control-label col-sm-1" for="loan-userid">User ID</label>
        <div class="col-sm-5 control-group">
            {{ Form::text('userid', $userid, array('class' => 'form-control', 'id' => 'loan-userid', 'maxLength' => '10', 'data-suggest' => 'yes', 'data-cache' => 'no', 'data-source' => route('loans.suggest.user'), 'required' )) }}
        </div>
    </div>

    <div id="items" class="clearfix loan-wrap"></div>

    <p class="text-center">
        <button type="button" class="btn btn-info">Add Kit</button>
        <button type="button" class="btn btn-success">Add Equipment</button>
        <button type="submit" class="btn btn-primary btn-lg8">Issue Loan</button>
    </p>

</div>

{{ Form::close() }}
@stop

@section('footer')
@include('loans.scripts')

<script>
    $(function(){

        var c = 1;

        var FormSettings = {
            // form
            form: $('.form-ajax'),
            // new kit template
            kit: Handlebars.compile($('#kit-template').html()),
            // new equipment template
            equipment: Handlebars.compile($('#equipment-template').html()),
            // single equipment
            equipmentSingle: Handlebars.compile($('#equipment-single-template').html()),
            suggestionTemplate: Handlebars.compile('<p><span class="title">#@{{id}}</span><br>@{{name}}</p>'),
            suggestionEmptyTemplate: [
                '<div class="empty-message">',
                'unable to find any items that match the current query',
                '</div>'
            ].join('\n'),
            kitSuggest: new Bloodhound({
                datumTokenizer: function(val) { return Bloodhound.tokenizers.whitespace(val.id + ' ' + val.name); },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: {
                    url: "{{ route('loans.suggest.kit') }}",
                    ttl: 200
                }
            }),
            equipmentSuggest: new Bloodhound({
                datumTokenizer: function(val) { return Bloodhound.tokenizers.whitespace(val.id + ' ' + val.name); },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                prefetch: {
                    url: "{{ route('loans.suggest.equipment') }}",
                    ttl: 200
                }
            }),
            kitLoader: function() {
                if (!$(this).val() || $(this).data('current-val') == $(this).val())
                {
                    return;
                }

                // Store to prevent doube triggering
                $(this).data('current-val', $(this).val());

                // Validate field after selecting
                var field = $(this).data('bvField');
                if (field)
                {
                    FormSettings.form
                        .data('bootstrapValidator')
                        .updateStatus(field, 'NOT_VALIDATED', null)
                        .validateField(field);
                }

                var kit = $(this), inputs = kit.closest('.loan-options').siblings('.inputs'), equip = inputs.siblings('.equipment');

                inputs.empty();
                equip.empty();

                $.get( "{{ route('loans.load.kit') }}", { q: $(this).val() }, function(d) {

                    if (d.error) {
                        new PNotify({
                            title: 'Error',
                            text: d.message,
                            type: 'error'
                        });
                        return;
                    }

                	$('.loan-length', kit.closest('fieldset')).val(d.data.loan_length);

                    for (var i = 0; i < d.data.equipment.length; i++)
                    {
                        // Add inputs for all equipment
                        var item = $('<input />').attr({
                            'data-bv-unique': 'true',
                            'data-bv-unique-message': 'Duplicate!',
                            'data-bv-unique-matches': '.unique-equipment'
                        }).addClass('form-control unique-equipment').appendTo(inputs).wrap('<p />');
                        if(i == 0) { //if first input field
                            item.focus(); //focus on it
                        }
                        //FormSettings.form.bootstrapValidator('addField', $(item) );

                        // Add list of equipment to display
                        equip.append(FormSettings.equipmentSingle(d.data.equipment[i]));
                    }

                    $('[type=submit]', FormSettings.form).prop('disabled', false);

                }, 'json' );
            },
            equipmentLoader: function() {
                if (!$(this).val() || $(this).data('current-val') == $(this).val())
                {
                    return;
                }

                // Store to prevent doube triggering
                $(this).data('current-val', $(this).val());

                // Validate field after selecting
                var field = $(this).data('bvField');
                if (field)
                {
                    FormSettings.form
                        .data('bootstrapValidator')
                        .updateStatus(field, 'NOT_VALIDATED', null)
                        .validateField(field);
                }

                var kit = $(this), inputs = kit.closest('.loan-options').siblings('.inputs'), equip = inputs.siblings('.equipment');

                inputs.empty();
                equip.empty();

                $.get( "{{ route('loans.load.equipment') }}", { q: $(this).val() }, function(d) {

                    if (d.error) {
                        new PNotify({
                            title: 'Error',
                            text: d.message,
                            type: 'error'
                        });
                        return;
                    }

                    for (var i = 0; i < d.data.length; i++)
                    {
                		$('.loan-length', kit.closest('fieldset')).val(d.data[i].loan_length);
                        // Add inputs for all equipment
                        var item = $('<input />').attr({
                            'readonly': 'readonly'
                        }).addClass('form-control unique-equipment').val(d.data[i].id).appendTo(inputs).wrap('<p />');

                        //FormSettings.form.bootstrapValidator('addField', $(item) );

                        // Add list of equipment to display
                        equip.append(FormSettings.equipmentSingle(d.data[i]));

                        $('#items').trigger('refresh');
                    }

                    $('[type=submit]', FormSettings.form).prop('disabled', false);

                }, 'json' );
            }
        };

        FormSettings.kitSuggest.initialize();
        FormSettings.equipmentSuggest.initialize();

        // Validate individual loan items
        FormSettings.form.on('ajaxValidate', function(e) {

            var $container = $('.equipment');

            if (!$container.length)
            {
                new PNotify({
                    title: 'Error',
                    text: 'You need to add at least one kit or piece of equipment.',
                    type: 'error'
                });
                e.preventDefault();
                $('[type=submit]', FormSettings.form).prop('disabled', false);
                return;
            }

            // Loop through all containers
            for (var i = 0; i < $container.length; i++)
            {
                // get all input elements in container
                var $equipment = $('.equipment-wrapper', $container);

                if ($equipment.length != $equipment.filter('.bg-warning,.bg-danger,.scanned').length)
                {
                    new PNotify({
                        title: 'Error',
                        text: 'All equipment not damaged or missing needs to be checked out.',
                        type: 'error'
                    });
                    e.preventDefault();

                    $('[type=submit]', FormSettings.form).prop('disabled', false);

                    return;
                }

            }
        });

        FormSettings.form.on('status.field.bv', function(e, data){
			data.bv.disableSubmitButtons(false);
        });

        $('#loan-userid').data('ttTypeahead').autoselect = true;

        $('#loan-userid').bind('typeahead:selected', function() {
            //$(this).prop('readonly', true);
        }).bind('keydown', function(event){
        	if (event.keyCode == 13) {
        		event.preventDefault();
        	}
        });

        $('#items').on('keydown', '.tt-input', function(event){
        	if (event.keyCode == 13) {
        		event.preventDefault();
        		$.tabNext();
        		$.tabNext();
        	}
        });

        $('.btn-info').click(function(ev){

            $('#items').append(FormSettings.kit({
                id: c
            }));

            var v = $('#items fieldset').last();

            $(':input', v).each(function(i, e){
                FormSettings.form.bootstrapValidator('addField', $(e) );
            });

            $('.suggest', v).each(function(i, e){
                $(e).bind('change.kitid typeahead:selected', FormSettings.kitLoader).typeahead({
                    items: 4,
                    highlight: true
                },{
                    name: 'items',
                    source: FormSettings.kitSuggest.ttAdapter(),
                    displayKey: 'id',
                    templates: {
                        empty: FormSettings.suggestionEmptyTemplate,
                        suggestion: FormSettings.suggestionTemplate
                    }
                });
            });

            $('#items').trigger('refresh');

            if (ev.originalEvent)
            {
            	$('.tt-input', $('#items fieldset').last()).focus();
            }

            c++;
        });

        $('.btn-success').click(function(ev){

            $('#items').append(FormSettings.equipment({
                id: c
            }));

            var v = $('#items fieldset').last();

            $(':input', v).each(function(i, e){
                FormSettings.form.bootstrapValidator('addField', $(e) );
            });

            $('.suggest', v).each(function(i, e){
                $(e).bind('change.equipmentid typeahead:selected', FormSettings.equipmentLoader).typeahead({
                    items: 4,
                    highlight: true
                },{
                    name: 'items',
                    source: FormSettings.equipmentSuggest.ttAdapter(),
                    displayKey: 'id',
                    templates: {
                        empty: FormSettings.suggestionEmptyTemplate,
                        suggestion: FormSettings.suggestionTemplate
                    }
                });
            });

            $('#items').trigger('refresh');

            if (ev.originalEvent)
            {
            	$('.tt-input', $('#items fieldset').last()).focus();
            }

            c++;
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

        }).on('change blur', '.inputs input', function(){
            $('#items').trigger('refresh');
        });


    });
</script>
@if ($userid)
<script>
    $('#loan-userid').prop('readonly', true);
</script>
@else
<script>
    $(function(){
        $('#loan-userid').focus();
    });
</script>
@endif

@if ($type == 'kit')
<script>
    $(function(){
        $('.btn-info').trigger('click');
        var $item = $('#items fieldset').last();
        $('.tt-input', $item).typeahead('val', {{ json_encode($id) }}).trigger('typeahead:selected');
    });
</script>
@endif

@if ($type == 'equipment')
<script>
    $(function(){
        $('.btn-success').trigger('click');
        var $item = $('#items fieldset').last();
		$('.tt-input', $item).typeahead('val', {{ json_encode($id) }}).trigger('typeahead:selected');
    });
</script>
@endif

@stop