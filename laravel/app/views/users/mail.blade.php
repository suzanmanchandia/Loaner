{{ Form::model( $user, array('route' => array('users.mail.send', $user->userNum), 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-bv-excluded' => ':disabled', 'data-toggle' => 'validator', 'id' => 'user_mail' ) ) }}

    <div class="row-fluid">
        <div class="col-md-7">

            <div class="form-group">
                <label for="mail-to" class="control-label col-sm-4">To</label>
                <div class="col-sm-8">
                    {{ Form::input('email', 'to', $user->email, array('class' => 'form-control', 'id' => 'mail-to', 'required', 'maxlength' => 40 ) ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="mail-from" class="control-label col-sm-4">From</label>
                <div class="col-sm-8">
                    {{ Form::text('from', Auth::getUser()->fname . ' ' . Auth::getUser()->lname, array('class' => 'form-control', 'id' => 'mail-from', 'required', ) ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="mail-sender" class="control-label col-sm-4">Email</label>
                <div class="col-sm-8">
                    {{ Form::input('email', 'sender', Auth::getUser()->email, array('class' => 'form-control', 'id' => 'mail-sender', 'required', 'maxlength' => 40 ) ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="mail-subject" class="control-label col-sm-4">Subject</label>
                <div class="col-sm-8">
                    {{ Form::text('subject', '', array('class' => 'form-control', 'id' => 'mail-subject', 'required', 'placeholder' => 'Please type the subject' ) ) }}
                </div>
            </div>

            <div class="form-group">
                <label for="mail-message" class="control-label col-sm-4">Message</label>
                <div class="col-sm-8">
                    {{ Form::textarea('message', '', array('class' => 'form-control hide', 'id' => 'mail-message', 'required', 'rows' => 3, 'placeholder' => 'Please type the subject' ) ) }}
                    <div contenteditable="true" class="form-control" data-link-with="#mail-message"></div>
                </div>
            </div>

        </div>

        <div class="col-md-5">
            <div class="history form-control">
                <table width="100%" cellpadding="5">
                    <thead>
                        <tr align="left">
                            <th>DUE DATE</th>
                            <th>FINES</th>
                            <th>DESCRIPTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($loans as $loan)
                    <tr>
                        <td>{{ $loan->due_date->format(Config::get('formatting.dates.short')) }}</td>
                        <td>{{ $loan->fine }}</td>
                        <td>{{ $loan->kitid ? $loan->kit->kit_desc : $loan->equipment->model }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <p><button type="button" class="btn btn-default" id="attach-message">&lt;&lt; Attach Message</button></p>
        </div>

        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary btn-lg">Submit</button>
            <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
        </div>
    </div>

    <div class="clearfix"></div>

{{ Form::close() }}
<script>
    $(function(){
        $('div[contenteditable]').on('keyup change blur', function(){
            $('#mail-message').val($(this).html()).trigger('input');
        });
        $('#attach-message').click(function(){
            $('<p />').text('--------------------').appendTo('div[contenteditable]');
            $('div[contenteditable]').append($('.history').html()).trigger('change');
        });

        // Hide modal after sending email
        $('#user_mail').on('contentUpdate', function(){
            $Loaner.modal.modal('hide');
        });

    });
</script>