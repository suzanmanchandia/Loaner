<h4 class="text-center">{{ $user->fname }} {{ $user->lname }} - {{ $user->userid }}</h4>
<h5 class="text-center">{{ date(Config::get('formatting.dates.friendlyTime')) }}</h5>

<span class="pull-right">
    <a href="{{ route('reports.users.pdf', $user->userNum) }}" class="btn btn-default">Export to PDF</a>
    <span class="btn btn-default btn-print">Print</span>
</span>

<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#active" role="tab" data-toggle="tab">Active Loans</a></li>
    <li><a href="#overdue" role="tab" data-toggle="tab">Overdue Loans</a></li>
    <li><a href="#returned" role="tab" data-toggle="tab">Returned Loans</a></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade in active" id="active">
        @include('reports.users.tab', $active)
    </div>
    <div class="tab-pane fade" id="overdue">
        @include('reports.users.tab', $overdue)
    </div>
    <div class="tab-pane fade" id="returned">
        @include('reports.users.tab', $returned)
    </div>
</div>