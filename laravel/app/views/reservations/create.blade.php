@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'loans', 'title' => link_to_route('reservations.index', 'Reservations'), 'subtitle' => 'Create New Reservation' )) }}
@stop

@section('content')
{{ Form::open( array('route' => 'reservations.store', 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('reservations.index') ) ) }}

<div class="row">

    <div class="col-sm-6">

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-userid">User ID</label>
            <div class="col-sm-5 control-group">
                {{ Form::text('userid', null, array('class' => 'form-control', 'id' => 'loan-userid', 'maxLength' => '10', 'data-suggest' => 'yes', 'data-cache' => 'no', 'data-source' => route('loans.suggest.user'), 'required' )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-type">Type</label>
            <div class="col-sm-5 control-group">
                {{ Form::select('type', array('kit' => 'Kit', 'equipment' => 'Equipment'), null, array('class' => 'form-control', 'id' => 'loan-type', )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-itemid">Item ID</label>
            <div class="col-sm-5 control-group">
                {{ Form::text('itemid', null, array('class' => 'form-control', 'id' => 'loan-itemid', 'required' )) }}
            </div>
        </div>

    </div>

    <div class="col-sm-6">

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-loan_length">Loan Period</label>
            <div class="col-sm-5 control-group">
                {{ Form::input('number', 'loan_length', null, array('class' => 'form-control', 'id' => 'loan-loan_length', 'min' => 1, 'required' )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-issue_date">Issue Date</label>
            <div class="col-sm-5 control-group">
                {{ Form::text('issue_date', null, array('class' => 'form-control', 'id' => 'loan-issue_date', 'required' )) }}
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="loan-notes">Notes</label>
            <div class="col-sm-5 control-group">
                {{ Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'loan-notes', 'rows' => 4 )) }}
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

    <fieldset>
        <legend>Listed Equipment</legend>
        <div id="items"></div>
    </fieldset>

    <div class="text-center"><button type="submit" class="btn btn-primary btn-lg">Submit</button> <a href="{{ route('reservations.create') }}" class="btn btn-default btn-lg">Reset</a></div>

</div>

{{ Form::close() }}
@stop

@section('footer')
@include('reservations.scripts')
<script>
    $(function(){

        var c = 1;

        var FormSettings = {
            // form
            form: $('.form-ajax'),
            itemInput: $('#loan-itemid'),
            // new equipment template
            equipment: Handlebars.compile($('#equipment-template').html()),
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
            })
        };

        FormSettings.kitSuggest.initialize();
        FormSettings.equipmentSuggest.initialize();

        FormSettings.form.on('ajaxValidate', function(e){
            if ($('#items .equipment-wrapper').length < 1)
            {
                new PNotify({
                    title: 'Error',
                    text: 'You must reserve at least one piece of equipment.',
                    type: 'error'
                });
                e.preventDefault();
                $('#loan-itemid').prop('readonly', false).focus();
            }
        });

        $('#loan-type').change(function(){
            FormSettings.itemInput.typeahead('destroy');
            FormSettings.itemInput.typeahead({
                items: 4,
                highlight: true
            },{
                name: 'items',
                source: $(this).val() == 'kit' ? FormSettings.kitSuggest.ttAdapter() : FormSettings.equipmentSuggest.ttAdapter(),
                displayKey: 'id',
                templates: {
                    empty: FormSettings.suggestionEmptyTemplate,
                    suggestion: FormSettings.suggestionTemplate
                }
            });
        }).trigger('change');

        $('#loan-userid, #loan-itemid').bind('typeahead:selected', function() {
            $(this).prop('readonly', true);
        });

        $('#loan-userid').data('ttTypeahead').autoselect = true;

        $('#loan-itemid').bind('typeahead:selected change', function() {
            $.get("{{ route('reservations.load') }}", {type: $('#loan-type').val(), id: $('#loan-itemid').val()}, function(d){
                $('#items').empty();
                switch (d.status)
                {
                    case 200:
                        $('#items').empty();
                        for (var i = 0; i < d.data.equipment.length; i++)
                        {
                            $('#items').append(FormSettings.equipment(d.data.equipment[i]));
                        }

                        $('#loan-loan_length').val(d.data.loan_length);

                        break;
                    default:
                        $('#loan-itemid').prop('readonly', false);
                }
            } ).fail(function(x, s, t){
                new PNotify({
                    title: 'Error',
                    text: t,
                    type: 'error'
                });
                $('#loan-itemid').prop('readonly', false);
            });
        });

        $('#loan-issue_date').datepicker({
            autoclose: true,
            startDate: '{{ date("m/d/Y") }}'
        }).on('hide', function() {
            $(this).trigger('input');
        });


    });
</script>
@stop