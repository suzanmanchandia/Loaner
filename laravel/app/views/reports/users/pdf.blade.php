@include('reports.style')
<h4 class="text-center">{{ $user->fname }} {{ $user->lname }} - {{ $user->userid }}</h4>
<h5 class="text-center">{{ date(Config::get('formatting.dates.friendlyTime')) }}</h5>

<h6>Active Loans</h6>
@include('reports.users.tab', $active)

<h6>Overdue Loans</h6>
@include('reports.users.tab', $overdue)

<h6>Returned Loans</h6>
@include('reports.users.tab', $returned)
