@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => link_to_route('kits.index', 'Kits'), 'subtitle' => 'Create New Kit' )) }}
@stop

@section('content')
{{ Form::open( array('route' => 'kits.store', 'method' => 'post', 'class' => 'form-horizontal form-ajax', 'data-toggle' => 'validator', 'data-redirect' => route('kits.index') ) ) }}

<div class="row">

    <div class="col-md-6">

        <div class="form-group">
            <label for="kit-id" class="control-label col-sm-4">Kit ID</label>
            <div class="col-sm-8">
            {{ Form::text('kitid', $kit->suggestId($deptID), array('class' => 'form-control', 'id' => 'kit-id', 'required', 'maxlength' => 15 ) ) }}
            <span class="help-block with-errors">Maximum 15 characters.</span>
            </div>
        </div>

        <div class="form-group">
            <label for="kit-ll" class="control-label col-sm-4">Loan Length</label>
            <div class="col-sm-8">
            {{ Form::input('number', 'loan_length', 3, array('type' => 'number', 'class' => 'form-control', 'id' => 'kit-ll', 'required', 'min' => 1 ) ) }}
            <span class="help-block with-errors">Default 3.</span>
            </div>
        </div>

        <div class="form-group">
            <strong class="control-label col-sm-4">Department</strong>
            <div class="col-sm-8">
                    {{ Form::select('deptID', $departments, $deptID, array('class' => 'form-control', 'id' => 'equipment-deptID' ) ) }}
            </div>
        </div>

        <div class="form-group">
            <strong class="control-label col-sm-4">Access Areas</strong>
            <div class="col-sm-8">
                @foreach ($access as $area)
                    <div class="checkbox col-sm-6" data-access-dept="{{{ $area->deptID }}}">
                    <label>
                        {{ Form::checkbox('access[]', $area->id, false, array('data-bv-choice' => 'true', 'data-bv-choice-min' => 1, 'data-bv-choice-message' => 'Please specify at least one access area', 'data-bv-container' => '.access-container' ) ) }}
                        {{ $area->accessarea }}
                    </label>
                    </div>
                @endforeach
				<p class="clearfix access-container"></p>
            </div>
        </div>

    </div>
    <div class="col-md-6">

        <div class="form-group">
            <label for="kit-desc" class="control-label col-sm-4">Kit Description</label>
            <div class="col-sm-8">
                {{ Form::textarea('kit_desc', '', array('class' => 'form-control', 'id' => 'kit-desc', 'rows' => 3  ) ) }}
            </div>
        </div>

        <div class="form-group">
            <label for="kit-notes" class="control-label col-sm-4">Notes</label>
            <div class="col-sm-8">
                {{ Form::textarea('notes', '', array('class' => 'form-control', 'id' => 'kit-notes', 'rows' => 3,  ) ) }}
            </div>
        </div>


    </div>

    <div class="col-sm-offset-2 col-sm-10"><button type="submit" class="btn btn-primary btn-lg">Submit</button></div>

</div>

{{ Form::close() }}
@stop

@section('footer')
@include('kits.scripts')
@stop