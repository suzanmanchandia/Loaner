@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Reports', 'subtitle' => 'Equipment (By Category)')) }}
@stop

@section('content')

<div class="row">
    <div class="col-md-3">
        @include('reports.sidebar')

        <div class="panel panel-default">
            <div class="panel-heading">Categories</div>
            <div class="panel-body">
                <p><input type="search" class="form-control" data-filter=".list-group" data-items=".list-group-item"></p>

                <div class="list-group">
                    @foreach ($categories as $item)
                    <div class="list-group-item" data-load="{{ route('reports.categories.show', $item->equipCatID) }}" data-target="#fine-info">
                        <h5 class="list-group-item-heading">{{{ $item->equipCatName }}}</h5>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-9" id="fine-info">

    </div>
</div>
@stop

@section('footer')
@include('reports.scripts');
@stop

