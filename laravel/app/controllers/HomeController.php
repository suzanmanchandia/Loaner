<?php

class HomeController extends \Loaner\AdminController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (is_role(User::ROLE_STUDENT))
        {
            $user = Auth::getUser();

            $this->layout->content = View::make('dashboard.student', array(
                    'department' => $this->dept->deptName,
                    'overdue'    => $user->loans()->where('due_date', '<=', time())
                        ->whereIn('loans.deptID', Auth::getUser()->departments()->get()->lists('deptID'))
                        ->with('kit')
                        ->with('equipment')
                        ->active()
                        ->get(),
                    'reservations' => $user->loans()->where(DB::raw("FROM_UNIXTIME(issue_date, '%Y-%m-%d')"), '=', date('Y-m-d'))
                        ->reservations()
                        ->get()
                ));

            return;
        }


		$depts = array($this->deptID);

        $this->layout->content = View::make('dashboard.generic', array(
                'department' => $this->dept->deptName,
                'overdue'    => Loan::query()->where('due_date', '<=', time())
					->whereIn('loans.deptID', $depts)
                    ->with('kit')
                    ->with('equipment')
                    ->active()
                    ->get(),
                'reservations' => Loan::query()->where(DB::raw("FROM_UNIXTIME(issue_date, '%Y-%m-%d')"), '=', date('Y-m-d'))
					->whereIn('loans.deptID', $depts)
                    ->reservations()
                    ->get()
            ));
    }

    public function logout()
    {
        Auth::logout();

        return Redirect::route('login');
    }

    public function setDept()
    {
        $dept = Input::get('deptID');
        $user = Auth::getUser();

        if ($dept && ($user->role == User::ROLE_SYSTEM_ADMIN || in_array($dept, $user->departments()->get()->lists('dept.deptID')))) {
            Session::set('deptID', $dept);
        }

        return Response::json(array('Yes!'));
    }


}
