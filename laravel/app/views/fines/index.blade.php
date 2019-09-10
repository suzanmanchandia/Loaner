@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'dashboard', 'title' => 'Fines')) }}
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Users</div>
            <div class="panel-body">
                <p><input type="search" class="form-control" data-filter=".list-group" data-items=".list-group-item"></p>

                <div class="list-group">
                @foreach ($users as $user)
                    <div class="list-group-item" data-load="{{ route('fines.show', $user->userNum) }}" data-target="#fine-info">
                        <h4 class="list-group-item-heading">{{{ $user->fname }}} {{{ $user->lname }}} - {{{ $user->userid }}}</h4>
                        <p class="list-group-item-text">
                            <strong>Fine: $<span class="fine">{{ $user->fine }}</span></strong>
                        </p>
                    </div>
                @endforeach
            </div>

            </div>
        </div>
    </div>
    <div class="col-md-8" id="fine-info">
    </div>
</div>
@stop

@section('footer')
<script>
    $(function() {
        $('#fine-info').on('click', '[data-fine]', function() {
            var amount = prompt($(this).data('title'));

            if (!amount)
            {
                return;
            }

            amount = parseFloat(amount);

            if (!amount)
            {
                alert('You entered an invalid value.');
                return;
            }
            else if (amount > $(this).data('fine'))
            {
                alert('You cannot make a payment greater than the current fine.');
                return;
            }

            $.post($(this).data('url'), {amount: amount, _method: 'put'}, function(d) {

                if (d.error)
                {
                    new PNotify({
                        title: 'Error',
                        text: '<strong>There were some errors:</strong>\n' + d.message,
                        type: 'error'
                    });
                }
                else
                {
                    PNotify.removeAll();
                    new PNotify({
                        title: 'Success',
                        text: 'A payment has successfully been recorded.',
                        type: 'success'
                    });
                    $('.list-group-item.active').trigger('click');
                    $('.list-group-item.active .fine').text(d.data.fine);
                }

            }).fail(function() {
                new PNotify({
                    title: 'Error',
                    text: 'There was an error processing your payment.',
                    type: 'error'
                });
            });
        });
    })
</script>
@stop
