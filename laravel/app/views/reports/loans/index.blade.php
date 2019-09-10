@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Reports', 'subtitle' => 'Loan Activity (Entire System)')) }}
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
					{{ Form::select('dept', array('' => 'All Departments', ) + $departments, $report_dept, array('class' => 'form-control auto-submit') ) }}
				</label>
				<input type="submit" class="btn btn-default sr-only" value="Filter">
			</div>
		</form>

        @if($customDateIsSet)
        <ul class="nav nav-tabs" role="tablist">
            <li><a href="#week" role="tab" data-toggle="tab">Past Week</a></li>
            <li><a href="#month" role="tab" data-toggle="tab">Past Month</a></li>
            <li><a href="#year" role="tab" data-toggle="tab">Past Year</a></li>
            <li class="active"><a href="#custom" role="tab" data-toggle="tab">Custom</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade" id="week">
                @include('reports.loans.tab', $week)
            </div>
            <div class="tab-pane fade" id="month">
                @include('reports.loans.tab', $month)
            </div>
            <div class="tab-pane fade" id="year">
                @include('reports.loans.tab', $year)
            </div>
            <div class="tab-pane fade in active" id="custom">
                @include('reports.loans.tab', $custom)
            </div>
        </div>
        @else
        <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#week" role="tab" data-toggle="tab">Past Week</a></li>
                    <li><a href="#month" role="tab" data-toggle="tab">Past Month</a></li>
                    <li><a href="#year" role="tab" data-toggle="tab">Past Year</a></li>
                    <li><a href="#custom" role="tab" data-toggle="tab">Custom</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="week">
                        @include('reports.loans.tab', $week)
                    </div>
                    <div class="tab-pane fade" id="month">
                        @include('reports.loans.tab', $month)
                    </div>
                    <div class="tab-pane fade" id="year">
                        @include('reports.loans.tab', $year)
                    </div>
                    <div class="tab-pane fade" id="custom">
                        @include('reports.loans.tab', $custom)
                    </div>
                </div>
        @endif

    </div>
</div>
@stop

@section('footer')
@include('reports.scripts');
@stop

