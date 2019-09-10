@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'dashboard', 'title' => 'Roski Loaner', 'subtitle' => $department . ' Department' )) }}
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="col-md-6">
            <h4>Loans Overdue</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($overdue as $loan)
                    <tr>
                        <td>{{{ $loan->kitid ? $loan->kit->kit_desc : $loan->equipment->model }}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h4>Reservation</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($reservations as $loan)
                    <tr>
                        <td>{{{ $loan->kitid ? $loan->kit->kit_desc : $loan->equipment->model }}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
