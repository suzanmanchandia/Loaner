
@if (is_dept($item->deptID))

@if (is_role(User::ROLE_WORK_STUDY, User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN) && $item->status != Loan::STATUS_RETURNED)
<a data-toggle="tooltip" title="Edit Fine" href="javascript:;" class="btn btn-xs btn-success" data-dialog="yes" data-href="{{ route( 'loans.edit', array($item->lid) ) }}"><i class="fa fa-usd"></i></a>
<a data-toggle="tooltip" title="Edit Loan Length" href="javascript:;" class="btn btn-xs btn-info" data-dialog="yes" data-href="{{ route( 'loans.editlength', array($item->lid) ) }}"><i class="fa fa-pencil-square-o"></i></a>
<a data-toggle="tooltip" title="Renew" class="btn btn-xs btn-success" data-confirm="Are you sure you want to renew this loan?" data-method="put" href="{{ route( 'loans.renew', array($item->lid) ) }}"><i class="fa fa-refresh"></i></a>
<a data-toggle="tooltip" title="Return Loan" href="javascript:;" class="btn btn-xs btn-warning" data-dialog="lg" data-href="{{ route( 'loans.return', array($item->lid) ) }}"><i class="fa fa-reply"></i></a>
@endif
@if (is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN))
<a data-toggle="tooltip" title="Send Email" class="btn btn-xs btn-info" href="javascript:;" data-href="{{ route( 'users.mail', array($item->user->userNum) ) }}" data-dialog="lg" data-title="Email {{{ $item->user->fname }}} {{{ $item->user->lname }}}"><i class="fa fa-envelope"></i></a>
@endif

@endif