
@if (is_dept($kit->deptID))
<a data-toggle="tooltip" title="Edit Kit" class="btn btn-xs btn-info" href="{{ route( 'kits.edit', array($kit->kitid) ) }}"><i class="fa fa-pencil"></i></a>
<a data-toggle="tooltip" title="Deactivate" class="btn btn-xs btn-danger" href="javascript:;" data-href="{{ route( 'kits.deactivate', array($kit->kitid) ) }}" data-dialog="yes" data-title="Deactivate Kit #{{{ $kit->kitid }}}"><i class="fa fa-minus-circle"></i></a>
<a data-toggle="tooltip" title="Duplicate" class="btn btn-xs btn-success" href="{{ route( 'kits.duplicate', array($kit->kitid) ) }}"><i class="fa fa-copy"></i></a>
<a data-toggle="tooltip" title="Issue Loan" class="btn btn-xs btn-warning" href="{{ route( 'loans.kits', array($kit->kitid) ) }}"><i class="fa fa-plus-square"></i></a>
@endif