<form class="form-search form-inline" method="get" action="{{ route($route) }}">
    <div class="form-group">
        <input type="search" name="q" value="{{{ Input::get('q') }}}" class="form-control" size="40">
        <input type="submit" class="btn btn-default" value="Search">
    </div>
    <div class="form-group">
        <label for="pp">Per Page</label>
        {{ Form::select('pp', Config::get('dropdowns.pp'), Input::get('pp', 100), array('class' => 'form-control auto-submit', 'id' => 'pp') ) }}
        {{ Form::hidden('sf', Input::get('sf')) }}
        {{ Form::hidden('dir', Input::get('dir')) }}
        {{ Form::hidden('s', Input::get('s')) }}
    </div>

    <p>{{ Form::select('d', array('' => 'Current Department', '-1' => 'All Departments'), Input::get('d'), array('class' => 'auto-submit form-control') ) }}

    {{ isset($extra) ? $extra : '' }}</p>
</form>

