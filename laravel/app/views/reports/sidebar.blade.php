
<div class="well">
    <ul class="fa-ul">
		<li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.mega') }}">All Activity</a></li>
        <li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.index') }}">Loan Activity (Entire System)</a></li>
        <li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.users') }}">Loan Activity (By User)</a></li>
        {{--<li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.byuser') }}">Equipment (By User)</a></li>--}}
        <li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.categories') }}">Equipment (By Category)</a></li>
        <li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.cycle') }}">Replacement Cycle</a></li>
        <li><i class="fa-li fa fa-bar-chart-o"></i><a href="{{ route('reports.deactivation') }}">Deactivation Report</a></li>
    </ul>
</div>