@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'dashboard', 'title' => 'Roski Loaner', 'subtitle' => $department . ' Department' )) }}
@stop

@section('content')

<div class="row">
    <div class="col-md-3">
        <h4>Actions</h4>
        <ul class="fa-ul">
            <li><i class="fa-li fa fa-user"></i><a href="{{ route('users.create') }}">Create User</a></li>
            <li><i class="fa-li fa fa-briefcase"></i><a href="{{ route('kits.create') }}">Create Kit</a></li>
            <li><i class="fa-li fa fa-desktop"></i><a href="{{ route('equipment.create') }}">Create Equipment</a></li>
            <li><i class="fa-li fa fa-plus-square"></i><a href="{{ route('loans.create') }}">Take Out Loan</a></li>
            <li><i class="fa-li fa fa-calendar"></i><a href="{{ route('reservations.create') }}">Issue Reservation</a></li>
        </ul>
    </div>
    <div class="col-md-9">
        <div class="col-md-6">
            <h4>Loans Overdue</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($overdue as $loan)
                    <tr>
                        <td>
                            @if (is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR))
                            <a data-toggle="tooltip" title="Send Email" class="btn btn-xs btn-info" href="javascript:;" data-href="{{ route( 'users.mail', array($loan->user->userNum) ) }}" data-dialog="lg" data-title="Email {{{ $loan->user->fname }}} {{{ $loan->user->lname }}}"><i class="fa fa-envelope"></i></a>
                            @endif
                        </td>
                        <td>{{{ $loan->user->fname }}} {{{ $loan->user->lname }}}</td>
                        <td>{{{ $loan->hasKit() ? $loan->kit->kit_desc : '' }}} {{{ $loan->hasEquipment() ? $loan->equipment->model : '' }}}</td>
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
                        <th>Name</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($reservations as $loan)
                    <tr>
                        <td>{{{ $loan->user->fname }}} {{{ $loan->user->lname }}}</td>
                        <td>{{{ $loan->hasKit() ? $loan->kit->kit_desc : '' }}} {{{ $loan->hasEquipment() ? $loan->equipment->model : '' }}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
