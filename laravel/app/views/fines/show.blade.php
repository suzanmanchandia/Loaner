<div class="panel panel-default">
    <div class="panel-heading">{{{ $user->fname }}} {{{ $user->lname }}} - {{{ $user->userid }}} </div>
    <div class="panel-body">
    <table class="table">
        <thead>
            <tr>
                <th>Fine</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Days Late</th>
                <th>Amount ($)</th>
            </tr>
        </thead>
        <tfoot>
        <tr class="info">
            <th colspan="4">Total</th>
            <td>${{ $total }}</td>
        </tr>
        </tfoot>
        <tbody>
        @foreach ($loans as $loan)
        <tr>
            <td>
                Loan: <a href="javascript:;" data-href="{{ route('loans.show', $loan->lid) }}">{{ $loan->lid }}</a><br>
                @if ($loan->kit)
                Kit ID: <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->kit->kit_desc }}}" title="View Info" data-href="{{ route( 'kits.show', array($loan->kit->kitid) ) }}" href="javascript:;">{{ $loan->kit->kitid }}</a>
                @elseif ($loan->equipment)
                Equipment ID: <a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $loan->equipment->equipmentid }}}" title="View Info" data-href="{{ route( 'equipment.show', array($loan->equipment->equipmentid) ) }}" href="javascript:;">{{ $loan->equipment->equipmentid }}</a>
                @endif
            </td>
            <td>
                @if ($loan->due_date)
                {{ $loan->due_date->format(Config::get('formatting.dates.friendly')) }}
                @endif
            </td>
            <td>
                @if ($loan->return_date)
                {{ $loan->return_date->format(Config::get('formatting.dates.friendly')) }}
                @else
                --
                @endif
            </td>
            <td>{{ $loan->getDaysLate() }}</td>
            <td>{{ $loan->fine }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th>Payment</th>
                <th>Date Paid</th>
                <th>Accepted By</th>
                <th>Amount ($)</th>
            </tr>
        </thead>
        <tfoot>
        <tr class="info">
            <th colspan="3">Total</th>
            <td>${{ $totalPayments }}</td>
        </tr>
        <tr class="danger">
            <th colspan="3">Outstanding Balance</th>
            <td>${{ $balance }}</td>
        </tr>
        </tfoot>
        <tbody>
        @foreach ($payments as $payment)
        <tr>
            <td>
                Payment {{ counter() }}
            </td>
            <td>
                @if ($payment->timestamp)
                {{ $payment->timestamp->format(Config::get('formatting.dates.friendly')) }}
                @endif
            </td>
            <td>{{ $payment->accepted_by }}</td>
            <td>{{ $payment->amount }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if ($user->fine > 0)
    <p>
        <button class="btn btn-block btn-success btn-lg" data-url="{{ route('fines.update', $user->userNum) }}" data-fine="{{ $user->fine }}" data-title="Pay Fine for {{{ $user->fname }}} {{{ $user->lname }}}">Pay Fine</button>
    </p>
    @endif

</div>