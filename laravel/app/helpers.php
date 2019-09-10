<?php

function js_string($value)
{
    return $value;
    return json_encode( $value, JSON_HEX_QUOT );
}

/**
 * Get user role
 * @return int
 */
function role()
{
    return Auth::getUser()->role;
}

/**
 * Current user has a role
 * @param $roles
 * @return bool
 */
function is_role($roles)
{
    if (is_array($roles))
    {
        return in_array(role(), $roles);
    }

    $roles = func_get_args();

    return in_array(role(), $roles);
}

/**
 * Current user can make changes in department
 * @param $deptID
 * @return bool
 */
function is_dept($deptID)
{
    if (is_role(User::ROLE_SYSTEM_ADMIN))
    {
        return true;
    }
    if (is_role(User::ROLE_STUDENT))
    {
        return false;
    }

    $depts = Auth::getUser()->departments()->remember(10)->get()->lists('deptID');

    return in_array($deptID, $depts);
}

/**
 * Current user can make changes in department
 * @param $deptID
 * @return bool
 */
function is_depts(User $user)
{
    if (is_role(User::ROLE_SYSTEM_ADMIN))
    {
        return true;
    }
    if (is_role(User::ROLE_STUDENT))
    {
        return false;
    }

    $depts = $user->departments()->remember(10)->get()->lists('deptID');
    $current = Auth::getUser()->departments()->remember(10)->get()->lists('deptID');

    return count(array_intersect($depts, $current)) > 0;

}

/**
 * @param bool $reset
 * @return int
 */
function counter($reset = false)
{
    static $count = 0;

    if ($reset)
    {
        $count = 1;
    }
    else
    {
        $count++;
    }

    return $count;
}