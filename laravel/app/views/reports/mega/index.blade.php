@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Reports', 'subtitle' => 'All Activity (Entire System)')) }}
@stop

@section('content')

<div class="row">
    <div class="col-md-3">
        @include('reports.sidebar')
    </div>
    <div class="col-md-9">

		<form class="form-search form-inline" method="get" action="">
		<div class="form-group">
			<label>
				Department:
				{{ Form::select('dept', array('' => 'All Departments', ) + $departments, $report_dept, array('class' => 'form-control') ) }}
			</label>
			<input type="submit" class="btn btn-default sr-only" value="Filter">
		</div>
		</form>
        @if($customDateIsSet)
        <ul class="nav nav-tabs" role="tablist">
                    <li><a href="#week" role="tab" data-toggle="tab">Past Week</a></li>
                    <li><a href="#month" role="tab" data-toggle="tab">Past Month</a></li>
                    <li><a href="#year" role="tab" data-toggle="tab">Last 3 Months</a></li>
                    <li><a href="#sixmonths" role="tab" data-toggle="tab">Last 6 Months</a></li>
                    <li><a href="#twelvemonths" role="tab" data-toggle="tab">Last 12 Months</a></li>
                    <li class="active"><a href="#custom" role="tab" data-toggle="tab">Custom</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade" id="week">
                        @include('reports.mega.tab', $week)
                    </div>
                    <div class="tab-pane fade" id="month">
                        @include('reports.mega.tab', $month)
                    </div>
                    <div class="tab-pane fade" id="year">
                        @include('reports.mega.tab', $year)
                    </div>
                    <div class="tab-pane fade" id="sixmonths">
                        @include('reports.mega.tab', $sixmonths)
                    </div>
                    <div class="tab-pane fade" id="twelvemonths">
                        @include('reports.mega.tab', $twelvemonths)
                    </div>
                    <div class="tab-pane fade in active" id="custom">
                        @include('reports.mega.tab', $custom)
                    </div>
                </div>
        @else
        <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#week" role="tab" data-toggle="tab">Past Week</a></li>
                    <li><a href="#month" role="tab" data-toggle="tab">Past Month</a></li>
                    <li><a href="#year" role="tab" data-toggle="tab">Last 3 Months</a></li>
                    <li><a href="#sixmonths" role="tab" data-toggle="tab">Last 6 Months</a></li>
                    <li><a href="#twelvemonths" role="tab" data-toggle="tab">Last 12 Months</a></li>
                    <li><a href="#custom" role="tab" data-toggle="tab">Custom</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="week">
                        @include('reports.mega.tab', $week)
                    </div>
                    <div class="tab-pane fade" id="month">
                        @include('reports.mega.tab', $month)
                    </div>
                    <div class="tab-pane fade" id="year">
                        @include('reports.mega.tab', $year)
                    </div>
                    <div class="tab-pane fade" id="sixmonths">
                        @include('reports.mega.tab', $sixmonths)
                    </div>
                    <div class="tab-pane fade" id="twelvemonths">
                        @include('reports.mega.tab', $twelvemonths)
                    </div>
                    <div class="tab-pane fade in" id="custom">
                        @include('reports.mega.tab', $custom)
                    </div>
                </div>
        @endif


    </div>
</div>
@stop

@section('footer')
@include('reports.scripts');
@stop

