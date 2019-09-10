

@if (is_depts($user))

<a data-toggle="tooltip" title="Edit User" class="btn btn-xs btn-info" href="{{ route( 'users.edit', array($user->userNum) ) }}"><i class="fa fa-pencil"></i></a>

@if (is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->status == User::STATUS_LOCKED)
<a data-toggle="tooltip" title="Lock" class="btn btn-xs btn-success" href="{{ route( 'users.unlock', array($user->userNum) ) }}" data-confirm="Are you sure you want to unlock this user?" data-method="put"><i class="fa fa-unlock"></i></a>
@elseif(is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->status != USER::STATUS_DEACTIVATED)
<a data-toggle="tooltip" title="Lock" class="btn btn-xs btn-danger" href="{{ route( 'users.lock', array($user->userNum) ) }}" data-confirm="Are you sure you want to lock this user?" data-method="put"><i class="fa fa-lock"></i></a>
@endif

@if (is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->suspended)
<a data-toggle="tooltip" title="Unsuspend" class="btn btn-xs btn-success" href="{{ route( 'users.unsuspend', array($user->userNum) ) }}" data-confirm="Are you sure you want to unsuspend this user?" data-method="put"><i class="fa fa-check"></i></a>
@elseif(is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->status != USER::STATUS_DEACTIVATED)
<a data-toggle="tooltip" title="Suspend" class="btn btn-xs btn-danger" href="{{ route( 'users.suspend', array($user->userNum) ) }}" data-confirm="Are you sure you want to suspend this user?" data-method="put"><i class="fa fa-ban"></i></a>
@endif

@if ( is_role( User::ROLE_SYSTEM_ADMIN )  || (is_role( User::ROLE_WORK_STUDY, User::ROLE_ADMINISTRATOR ) && is_depts($user)) )
@if ($user->status == USER::STATUS_DEACTIVATED)
<a data-toggle="tooltip" title="Reactivate" class="btn btn-xs btn-success" href="{{ route( 'users.unlock', array($user->userNum) ) }}" data-confirm="Are you sure you want to reactivate this user?" data-method="put"><i class="fa fa-check"></i></a>
@else
<a data-toggle="tooltip" title="Deactivate" class="btn btn-xs btn-danger" href="{{ route( 'users.destroy', array($user->userNum) ) }}" data-confirm="Are you sure you want to deactivate this user?" data-method="delete"><i class="fa fa-times"></i></a>
@endif
<a data-toggle="tooltip" title="Issue Loan" class="btn btn-xs btn-warning" href="{{ route( 'loans.user', array($user->userid) ) }}"><i class="fa fa-plus-square"></i></a>
@endif

@if ( is_role( User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR ) )
<a data-toggle="tooltip" title="Send Email" class="btn btn-xs btn-info" href="javascript:;" data-href="{{ route( 'users.mail', array($user->userNum) ) }}" data-dialog="lg" data-title="Email {{{ $user->fname }}} {{{ $user->lname }}}"><i class="fa fa-envelope"></i></a>
@endif

@endif


<?php return; ?>

<div class="btn-group">
    <a class="btn btn-info btn-sm" href="{{ route( 'users.edit', array($user->userNum) ) }}">Edit</a>
    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">

        @if (is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->status == User::STATUS_LOCKED)
        <li><a href="{{ route( 'users.unlock', array($user->userNum) ) }}" data-confirm="Are you sure you want to unlock this user?" data-method="put"><i class="fa fa-unlock"></i> Unlock</a> </li>
        @elseif(is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR))
        <li><a href="{{ route( 'users.lock', array($user->userNum) ) }}" data-confirm="Are you sure you want to lock this user?" data-method="put"><i class="fa fa-lock"></i> Lock</a> </li>
        @endif


        @if (is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR) && $user->suspended)
        <li><a href="{{ route( 'users.unsuspend', array($user->userNum) ) }}" data-confirm="Are you sure you want to suspend this user?" data-method="put"><i class="fa fa-ban"></i> Suspend</a></li>
        @elseif(is_role(User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR))
        <li><a href="{{ route( 'users.suspend', array($user->userNum) ) }}" data-confirm="Are you sure you want to unsuspend this user?" data-method="put"><i class="fa fa-check"></i> Unsuspend</a></li>
        @endif

        @if ( is_role( User::ROLE_SYSTEM_ADMIN, User::ROLE_WORK_STUDY, User::ROLE_ADMINISTRATOR ) )
        <li><a href="{{ route( 'users.destroy', array($user->userNum) ) }}" data-confirm="Are you sure you want to delete this user?" data-method="delete"><i class="fa fa-trash-o"></i> Delete</a></li>
        <li><a href="{{ route( 'loans.user', array($user->userNum) ) }}"><i class="fa fa-plus-square"></i> Issue Loan</a></li>
        @endif

        @if ( is_role( User::ROLE_SYSTEM_ADMIN, User::ROLE_ADMINISTRATOR ) )
        <li class="divider"></li>
        <li><a href="javascript:;" data-href="{{ route( 'users.mail', array($user->userNum) ) }}" data-dialog="lg" data-title="Email {{{ $user->fname }}} {{{ $user->lname }}}"><i class="fa fa-envelope"></i> Send Email</a></li>
        @endif
    </ul>
</div>