
@if (is_dept($item->deptID))
<a data-toggle="tooltip" title="Edit Equipment" class="btn btn-xs btn-info" href="{{ route( 'equipment.edit', array($item->equipmentid) ) }}"><i class="fa fa-pencil"></i></a>
<a data-toggle="tooltip" title="Deactivate" class="btn btn-xs btn-danger" href="javascript:;" data-href="{{ route( 'equipment.deactivate', array($item->equipmentid) ) }}" data-dialog="yes" data-title="Deactivate Equipment #{{{ $item->equipmentid }}}"><i class="fa fa-minus-circle"></i></a>
<a data-toggle="tooltip" title="Duplicate" class="btn btn-xs btn-success" href="{{ route( 'equipment.duplicate', array($item->equipmentid) ) }}"><i class="fa fa-copy"></i></a>
<a data-toggle="tooltip" title="Issue Loan" class="btn btn-xs btn-warning" href="{{ route( 'loans.equipment', array($item->equipmentid) ) }}"><i class="fa fa-plus-square"></i></a>
@endif