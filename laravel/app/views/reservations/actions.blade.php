
@if (is_dept($item->deptID))

@if ($item->status != Loan::STATUS_CANCELED)

<a data-toggle="tooltip" title="Edit" class="btn btn-xs btn-info" data-dialog="true" data-href="{{ route( 'reservations.edit', array($item->lid) ) }}"><i class="fa fa-edit"></i></a>
<a data-toggle="tooltip" title="Issue" class="btn btn-xs btn-success" data-dialog="lg" data-href="{{ route( 'reservations.issue', array($item->lid) ) }}"><i class="fa fa-reply"></i></a>
<a data-toggle="tooltip" title="Cancel" class="btn btn-xs btn-danger" data-method="delete" data-confirm="This will cancel reservation #{{$item->lid}}. Are you sure you want to continue?" href="{{ route( 'reservations.destroy', array($item->lid) ) }}"><i class="fa fa-times"></i></a>
@endif

@endif