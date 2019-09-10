@section('jumbotron')
{{ View::make('utils.jumbotron', array('class' => 'kits', 'title' => 'Reports', 'subtitle' => 'Replacement Cycle')) }}
@stop
@section('content')
<?php
//Debugbar::info($loanInfo);
?>

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
				<input type="submit" class="btn btn-default" value="Filter">
				</div>
				<!-- Category filter !-->
				<div class="container"><div class="row"><div class="col-md-6">
                <form role="form">

                <div class="checkbox">
                    <label>
                    <input name="Select All" type="checkbox"> <b>Select All</b>
                   </label>
                </div>
                </br>
                @foreach($categories as $category)
                 <div class="checkbox">
                   <label>
                    <input name="{{$category}}" type="checkbox"> {{ $category }}

                   </label>
                 </div>
                 @endforeach


                </form>
                  </div></div></div>
            <!-- END Category filter !-->

		</form>

        <ul class="nav nav-tabs" role="tablist">
            @foreach($results as $year => $result)
            <li><a href="#{{ $year }}" role="tab" data-toggle="tab">{{ $year }}</a></li>
            @endforeach
        </ul>

        <div class="tab-content">

            @foreach($results as $year => $result)
            <div class="tab-pane fade" id="{{ $year }}">

                <span class="pull-right">
                    <span class="btn btn-default btn-print">Print</span>
                </span>

                <h4 class="text-center">Equipment Replacement Cycle</h4>
                <h5 class="text-center">{{ $year }}</h5>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Equipment ID</th>
                            <th>Description</th>
                            <th>Department</th>
                            <th>Category</th>
                            <th>Purchase Price</th>
                            <th>Manufacture Date</th>
                            <th>Expected Lifetime</th>
                            <th>On loan to:</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($result as $item)
                        <?php
                            if(is_object($item->category)){
                            $itemClass = str_replace('/', '', $item->category->equipCatName);
                            $itemClass = str_replace(' ', '', $itemClass);
                            if(array_key_exists ( $item->equipmentid , $loanInfo )) {
                            $itemLoan = $loanInfo[$item->equipmentid];
                            $userID = $itemLoan['userid'];
                            $userNum = $itemLoan['userNum'];
                            }
                            else {
                            $userID = "";
                            $userNum = "";
                            }
                            }
                            else{
                            //Debugbar::info($item->cateogry);
                            $itemClass = 'NA';
                            }
                         ?>
                        <tr class={{ $itemClass }}>
                            <td><a data-toggle="tooltip" data-dialog="yes" data-title="{{{ $item->model }}} {{ $item->equipmentid }}" title="View Info" data-href="{{ route( 'equipment.show', array($item->equipmentid) ) }}" href="javascript:;">{{ $item->equipmentid }}</a></td>
                            <td>{{ $item->model }}</td>
                            <td>{{ $item->department->deptName }}</td>
                            <td><?php if(is_object($item->category)){echo $item->category->equipCatName;}else{echo "N/A";} ?></td>
                            <td>{{ $item->purchasePrice  }}</td>
                            <td>{{ $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '' }}</td>
                            <td>{{ $item->expectedLifetime }}</td>
                            <td>
                            @if($item->status == Equipment::STATUS_LOANED)
                            <a data-toggle="tooltip" data-dialog="yes" data-title="User Information" data-href="{{ route( 'users.show', array($userNum) ) }}" href="javascript:;">{{ $userID }}</a>
                            @endif
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
@stop
@section('footer')
@include('reports.scripts');
<script>
    $(function() {
        $('.nav-tabs a').first().click();
    })

    $('input[type=checkbox]').each(function () {
        var defaultChecked = ["Computers", "Printer/Copier",  "Scanners",  "Camera",  "Projector",
                                "Tripod", "3D Equipment"];
        if($.inArray(this.name, defaultChecked) > -1)
        {
            this.checked = true;
         }
       else
        this.checked = false;
    });
    $('input[type="checkbox"]').change(function() {
        if(this.checked && this.name == "Select All") {
            $('input[type=checkbox]').each(function () {
                                this.checked = true;
                                var className = this.name;
                                className = className.replace('/','');
                                className = className.replace(' ','');
                                $('.' + className).show();
                            });
        }
        else if(this.checked != true && this.name == "Select All") {
             $('input[type=checkbox]').each(function () {
                                            this.checked = false;
                                            var className = this.name;
                                            className = className.replace('/','');
                                            className = className.replace(' ','');
                                           $('.' + className).hide();
                                        });
        }
        else if(this.checked) {
             var className = this.name;
             className = className.replace('/','');
             className = className.replace(' ','');
            $('.' + className).show();
        }
        else {
            var className = this.name;
            className = className.replace('/','');
            className = className.replace(' ','');
            $('.' + className).hide();
        }
    });
</script>
@stop

