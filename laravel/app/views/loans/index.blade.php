@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'loans', 'title' => $pageTitle )) }}
@stop

@section('content')

<div class="row">
<div class="col-md-2">
    <div class="well">
        <p><a href="{{ route('loans.create') }}" class="btn btn-primary btn-block">Issue Loan</a></p>
        <ul class="list-unstyled">
            <li><a href="{{ route('loans.index') }}">Active Loans</a></li>
            <li><a href="{{ route('loans.returned') }}">Returned Loans</a></li>
            <li><a href="{{ route('loans.long') }}">Long Term Loans</a></li>
            <li><a href="{{ route('loans.overdue') }}">Overdue Loans</a></li>
        </ul>
    </div>

</div>
<div class="col-md-10">

    {{ View::make( 'utils.search', array('route' => $currentRoute ) ) }}
    {{ $loans->appends(Input::except('page'))->links() }}

    {{ Form::open(['route' => 'loans.actions', 'method' => 'put']) }}

    <p class="text-right">
        <span class="pull-left">
            <a href="javascript:;" class="btn btn-link btn-mark">Mark All</a>
            |
            <a href="javascript:;" class="btn btn-link btn-unmark">Unmark All</a>
        </span>
        <button type="submit" class="btn btn-success" name="action" value="renew" data-confirm="Are you sure you want to renew these loans?"><i class="fa fa-refresh"></i> Renew</button>
        <a class="btn btn-warning" href="javascript:;" data-table-dialog="lg" data-href="{{ route('loans.return.multi') }}" title="Return Loans"><i class="fa fa-reply"></i> Return</a>
    </p>

    <div class="table-responsive">
        <table class="table table-striped table-hover action-table sortable-table">
            <thead>
            <tr>
                <th></th>
                {{ View::make( 'utils.table.header', array('name' => 'Kit ID', 'id' => 'kitid' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'EQ ID', 'id' => 'equipmentid' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'User ID', 'id' => 'userid' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'Name', 'id' => 'lname' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'Description', 'id' => 'desc' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'Issued', 'id' => 'issue_date', 'default' => 'issue_date' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'Due', 'id' => 'due_date' ) ) }}
                {{ View::make( 'utils.table.header', array('name' => 'Fines', 'id' => 'fine' ) ) }}
                @if (Input::has('d'))
                {{ View::make( 'utils.table.header', array('name' => 'Department', 'id' => 'deptName' ) ) }}
                @endif
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($loans as $item)
            <tr>
                <td>
                    @if (in_array($item->status, [Loan::STATUS_LONG_TERM, Loan::STATUS_OWING, Loan::STATUS_SHORT_TERM]) && (is_dept($item->deptID) && is_role(User::ROLE_WORK_STUDY, User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN) && $item->status != Loan::STATUS_RETURNED))
                    {{ Form::checkbox('ids[]', $item->lid) }}
                    @else
                    &nbsp;
                    @endif
                </td>
                <td>@if ($item->kit)
                    <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->kit->kit_desc }}}" data-href="{{ route( 'kits.show', array($item->kitid) ) }}" href="javascript:;">{{ $item->kit->kitid }}</a>
                    @endif
                </td>
                <td>@if ($item->equipment)
                    <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->equipment->model }}}" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipment->equipmentid }}</a>
                    @endif
                </td>
                <td>@if ($item->user)
                    <a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($item->userNum) ) }}" href="javascript:;">{{ $item->userid }}</a>
                    @endif
                </td>
                <td>@if ($item->user)
                    {{ $item->user->fname }}
                    {{ $item->user->lname }}
                    @endif
                </td>
                <td>
                    @if ($item->kit && $item->kitid)
                    {{ $item->kit->kit_desc }}
                    @endif
                    @if ($item->equipment && $item->equipmentid)
                    {{ $item->equipment->model }}
                    @endif
                </td>
                <td class="single-line">
                    @if ($item->issue_date)
                    {{ $item->issue_date->format(Config::get('formatting.dates.short')) }}
                    @endif
                </td>
                <td class="single-line">
                    @if ($item->due_date)
                    {{ $item->due_date->format(Config::get('formatting.dates.short')) }}
                    @endif
                </td>
                <td>{{ $item->fine }}</td>
                @if (Input::has('d'))
                <td>{{ $item->department ? $item->department->deptName : 'N/A' }}</td>
                @endif
                <td class="text-right">
                    <a data-toggle="tooltip" data-dialog="yes" data-title="Loan Details" title="View Info" class="btn btn-xs btn-default" data-href="{{ route( 'loans.show', array($item->lid) ) }}" href="javascript:;"><i class="fa fa-book"></i></a>
                    @include('loans.actions', array('item' => $item))
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $loans->appends(Input::except('page'))->links() }}
    {{ Form::close() }}
</div>
</div>
@stop
