@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'users', 'title' => link_to_route('users.index', 'Users'), 'subtitle' => 'Create New User' )) }}
@stop

@section('content')
{{ Form::open( array('route' => 'users.store', 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('users.index') ) ) }}

<div class="row">

    <div class="col-md-6">

        <div class="form-group">
            <label for="user-userid" class="control-label col-sm-4">USC User name</label>
            <div class="col-sm-8">
                {{ Form::text('userid', '', array('class' => 'form-control', 'id' => 'user-userid', 'required', 'maxlength' => 20 ) ) }}
                <span class="help-block with-errors">Maximum 20 characters.</span>
            </div>
        </div>

        <div class="form-group">
            <label for="user-usc_id" class="control-label col-sm-4">USC ID</label>
            <div class="col-sm-8">
                {{ Form::text('usc_id', '', array('class' => 'form-control', 'id' => 'user-usc_id', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-password" class="control-label col-sm-4">Password</label>
            <div class="col-sm-8">
                {{ Form::password('password', array('class' => 'form-control', 'id' => 'user-password', 'required', 'maxlength' => 20, 'data-bv-identical' => 'true', 'data-bv-identical-field' => 'confirm' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-confirm" class="control-label col-sm-4">Confirm</label>
            <div class="col-sm-8">
                {{ Form::password('confirm', array('class' => 'form-control', 'id' => 'user-confirm', 'data-bv-identical' => 'true', 'data-bv-identical-field' => 'password' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-fname" class="control-label col-sm-4">First name</label>
            <div class="col-sm-8">
                {{ Form::text('fname', '', array('class' => 'form-control', 'id' => 'user-fname', 'required', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-lname" class="control-label col-sm-4">Last name</label>
            <div class="col-sm-8">
                {{ Form::text('lname', '', array('class' => 'form-control', 'id' => 'user-lname', 'required', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-email" class="control-label col-sm-4">Email</label>
            <div class="col-sm-8">
                {{ Form::email('email', '', array('class' => 'form-control', 'id' => 'user-email', 'required', 'maxlength' => 40 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-phone" class="control-label col-sm-4">Phone</label>
            <div class="col-sm-8">
                {{ Form::text('phone', '', array('class' => 'form-control', 'id' => 'user-phone', 'required', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-address" class="control-label col-sm-4">Address</label>
            <div class="col-sm-8">
                {{ Form::text('address', '', array('class' => 'form-control', 'id' => 'user-address', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-city" class="control-label col-sm-4">City</label>
            <div class="col-sm-8">
                {{ Form::text('city', '', array('class' => 'form-control', 'id' => 'user-city', 'maxlength' => 20 ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-state" class="control-label col-sm-4">State</label>
            <div class="col-sm-8">
                {{ Form::select('state', Config::get('dropdowns.states'), 'CA', array('class' => 'form-control', 'id' => 'user-state', )) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-zip" class="control-label col-sm-4">Zip</label>
            <div class="col-sm-8">
                {{ Form::text('zip', '', array('class' => 'form-control', 'id' => 'user-zip', 'maxlength' => 10 ) ) }}
            </div>
        </div>

    </div>

    <div class="col-md-6">

        <div class="form-group">
            <label for="user-class" class="control-label col-sm-4">Class</label>
            <div class="col-sm-8">
                {{ Form::select('class', $classes, '', array('class' => 'form-control', 'id' => 'user-class', )) }}
            </div>
        </div>

        <div class="form-group">
            <strong class="control-label col-sm-4">Departments</strong>
            <div class="col-sm-8">
                @foreach ($departments as $id => $name)
                <div class="checkbox col-sm-6">
                    <label>
                        {{ Form::checkbox('dept[]', $id, false, array('data-bv-notempty' => 'true', 'data-bv-notempty-message' => 'Please specify at least one department.', 'class' => 'dept' ) ) }}
                        {{ $name }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <strong class="control-label col-sm-4">Access Areas</strong>
            <div class="col-sm-8">
                @foreach ($access as $area)
                <div class="checkbox col-sm-6" data-access-dept="{{{ $area->deptID }}}">
                    <label>
                        {{ Form::checkbox('access[]', $area->id, false, array(  ) ) }}
                        {{ $area->accessarea }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label for="user-role" class="control-label col-sm-4">Role</label>
            <div class="col-sm-8">
                {{ Form::select('role', $roles, '', array('class' => 'form-control', 'id' => 'user-role', )) }}
            </div>
        </div>

        <div class="form-group">
            <label for="user-notes" class="control-label col-sm-4">Notes</label>
            <div class="col-sm-8">
                {{ Form::textarea('notes', '', array('class' => 'form-control', 'id' => 'user-notes', 'rows' => 3 )) }}
            </div>
        </div>
        
    </div>

</div>

<div class="row">
    <div class="col-sm-offset-2 col-sm-10"><button type="submit" class="btn btn-primary btn-lg">Submit</button></div>
</div>

{{ Form::close() }}

@stop

@section('footer')
@include('users.scripts')
@stop