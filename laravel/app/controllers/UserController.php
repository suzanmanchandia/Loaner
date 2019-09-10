<?php

use Carbon\Carbon;
use Illuminate\Mail\Message;

class UserController extends \Loaner\AdminController
{
    public function index()
    {
        $users = User::query();

        $users->with('departments')->remember(0.5);

        $that = $this;

        if (Input::get('q')) {
            $users->where(
                function ($query) {
                    $query->orWhere('userid', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('usc_id', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('email', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('fname', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('lname', 'like', '%' . Input::get('q') . '%');
                }
            );
        }
        if (Input::get('s')) {
            $users->where('status', '=', Input::get('s'));
        } else {
            $users->where('status', '!=', User::STATUS_DEACTIVATED); //dont show deactivated users
        }
        if (Input::get('r')) {
            $users->where('role', '=', Input::get('r'));
        }
        if (!Input::get('d')) {
            $users->whereHas(
                'departments',
                function ($q) use ($that) {
                    $q->where('departments_users.deptID', '=', $that->deptID);

                }
            );
        }

        $direction  = Input::get('dir') == 'desc' ? 'desc' : 'asc';
        $department = new Department();

        // Sortable
        switch (Input::get('sf')) {
            case 'fname':
                $users->orderBy('fname', $direction);
                $users->orderBy('lname', $direction);
                break;
            case 'email':
            case 'role':
                $users->orderBy(Input::get('sf'), $direction);
                break;
            case 'deptName':
                //$users->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->users()->getQualifiedParentKeyName());
                //$users->orderBy('deptName', $direction);
                break;
            default:
                $users->orderBy('userid', $direction);
        }

        $paginate = (int)Input::get('pp', User::PERPAGE);

//        foreach($users->paginate($paginate, array( with(new User)->getTable().'.*')) as $user)
//        {
//            if($user->userid == "sampleworkstudy")
//            {
//                Debugbar::info("found ");
//                Debugbar::info($user->userid);
//                Debugbar::info("inside CONTROLLER with status: ");
//                Debugbar::info($user->status);
//            }
//        }

        $this->layout->content = View::make('users.index', array('users' => $users->paginate($paginate, array( with(new User)->getTable().'.*'))));
    }

    public function create()
    {
        switch (Auth::getUser()->role)
        {
            case User::ROLE_ADMINISTRATOR:
                $roles = array_except( Config::get('dropdowns.user_roles' ), array( User::ROLE_SYSTEM_ADMIN ) );
                break;
            case User::ROLE_WORK_STUDY:
                $roles = array_except( Config::get('dropdowns.user_roles' ), array( User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN, User::ROLE_CASHIER ) );
                break;
            default:
                $roles = Config::get('dropdowns.user_roles' );
                break;
        }
        switch (Auth::getUser()->role)
        {
            case User::ROLE_SYSTEM_ADMIN:
                $departments = Department::query()->where('dept.deptID', '>', 0)->lists('deptName', 'deptID');
                break;
            default:
                $departments = Auth::getUser()->departments()->get()->lists('deptName', 'deptID');
                break;
        }


        $this->layout->content = View::make('users.create', array(
                'access' => AccessArea::all(),
                'classes' => $this->dept->classes->lists('classname', 'classname'),
                'roles' => $roles,
                'departments' => $departments,
            ));
    }

    public function store()
    {
        User::unguard();

        $user = new User();

        $user->fill( Input::only('userid', 'usc_id', 'password', 'fname', 'lname', 'email', 'phone', 'address', 'city', 'state', 'zip', 'class', 'role', 'notes') );
        $user->status = 1;

        $user->validate();

        $access = array_filter( (array) Input::get('access') );
        $depts = array_filter( (array) Input::get('dept') );
        $errors = $user->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area.';
        }
        if (empty($depts))
        {
            $errors[] = 'You must select at least one department.';
        }

        if (empty($errors))
        {
            $user->password = sha1($user->password);
            $user->save();
            $user = User::query()->where('userid', '=', $user->userid)->first();
            $user->departments()->attach($depts);
            $user->accessAreas()->attach($access, array('userid' => $user->userid));
            Notification::success('User created!');
            return $this->successResponse('User created!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Show the form for editing the specified user.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = User::find($id);

        if (!$user) {
            Notification::error('The user was not found');
            return Redirect::route('users.index');
        }

        switch (Auth::getUser()->role)
        {
            case User::ROLE_ADMINISTRATOR:
                $roles = array_except( Config::get('dropdowns.user_roles' ), array( User::ROLE_SYSTEM_ADMIN ) );
                break;
            case User::ROLE_WORK_STUDY:
                $roles = array_except( Config::get('dropdowns.user_roles' ), array( User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN, User::ROLE_CASHIER ) );
                break;
            default:
                $roles = Config::get('dropdowns.user_roles' );
                break;
        }
        switch (Auth::getUser()->role)
        {
            case User::ROLE_SYSTEM_ADMIN:
                $departments = Department::query()->where('dept.deptID', '>', 0)->get()->lists('deptName', 'deptID');
                break;
            default:
                $departments = Auth::getUser()->departments()->where('dept.deptID', '>', 0)->get()->lists('deptName', 'deptID');
                break;
        }

        $this->layout->content = View::make('users.edit', array(
                'user' => $user,
                'access' => AccessArea::all(),
                'classes' => $this->dept->classes->lists('classname', 'classname'),
                'roles' => $roles,
                'departments' => $departments,
                'depts' => $user->departments()->get()->lists('deptID'),
                'areas' => $user->accessAreas()->lists('accessid'),
            ));
    }

    public function update($id)
    {
        User::unguard();
        $user = User::find($id);
        /* @var $user USer */

        if (!$user) {
            return $this->notFoundResponse();
        }

        $user->fill( Input::only('usc_id', 'fname', 'lname', 'email', 'phone', 'address', 'city', 'state', 'zip', 'class', 'role', 'notes', 'status') );

        $user->validateUniques();

        $access = array_filter( (array) Input::get('access') );
        $depts = array_filter( (array) Input::get('dept') );
        $errors = $user->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area.';
        }
        if (empty($depts))
        {
            $errors[] = 'You must select at least one department.';
        }

        if (empty($errors))
        {
            $user->updateUniques();
            $user->departments()->sync($depts);
            $user->accessAreas()->detach();
            $user->accessAreas()->attach($access, array('userid' => $user->userid));
            Notification::success('User updated!');
            return $this->successResponse('User updated!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Display the specified user.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $this->layout = View::make('users.show', array('user' => $user));
    }


    /**
     * Mail form for the specified user.
     *
     * @param  int $id
     * @return Response
     */
    public function mail($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $loans = $user->loans()->with('Equipment', 'Kit')->overdue()->get();

        $this->layout = View::make('users.mail', array('user' => $user, 'loans' => $loans));
    }


    /**
     * Mail form for the specified user.
     *
     * @param  int $id
     * @return Response
     */
    public function sendMail($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $validation = Validator::make(Input::all(),
            array(
                'to' => 'required|email',
                'from' => 'required',
                'sender' => 'required|email',
                'message' => 'required',
            ));

        if ($validation->fails())
        {
            return $this->errorResponse($validation->errors()->all());
        }

        $that = $this;

        Mail::send('emails.blank', array('content' => Input::get('message') ), function (Message $message) use ($user, $that){
            $message->subject(Input::get('subject'));
            $message->from(Input::get('sender'), Input::get('from'));
            $message->to(Input::get('to'), $user->fname . ' ' . $user->lname);

            foreach ($that->dept->emails as $email)
            {
                $message->cc($email);
            }

            if (Config::get('mail.pretend')) Log::info(View::make('emails.blank', array('content' => Input::get('message') ))->render());
        });

        return $this->successResponse('Message sent!');
    }

    /**
     * Lock a user
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lock($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            Notification::error('The user you tried to lock was not found.');
        }
        else
        {
            $user->status = User::STATUS_LOCKED;
            $user->timestamps = false;
            $user->forceSave();
            Notification::success(sprintf('User %s has been locked!', $user->userid));
        }

        return Redirect::route('users.index');

    }

    /**
     * Lock a user
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlock($id)
    {
        $user = User::find($id);
        if (!$user)
        {
            Notification::error('The user you tried to unlock was not found.');
        }
        else
        {
            $user->status = User::STATUS_UNLOCKED;
            $user->timestamps = false;
            $user->forceSave();
            Notification::success(sprintf('User %s has been unlocked!', $user->userid));
        }

        return Redirect::to($this->previousOrRoute('users.index'));
    }

    /**
     * Lock a user
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            Notification::error('The user you tried to suspend was not found.');
        }
        else
        {
            $user->suspended = 1;
            $user->timestamps = false;
            $user->forceSave();
            Notification::success(sprintf('User %s has been suspended!', $user->userid));
        }

        return Redirect::to($this->previousOrRoute('users.index'));

    }

    /**
     * Lock a user
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unsuspend($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            Notification::error('The user you tried to unsuspend was not found.');
        }
        else
        {
            $user->suspended = 0;
            $user->timestamps = false;
            $user->forceSave();
            Notification::success(sprintf('User %s has been unsuspended!', $user->userid));
        }

        return Redirect::route('users.index');

    }

	/**
	 * Suspend all students
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function suspendAll()
	{
		$count = $this->dept->users()->suspended()->update(array('suspended' => 1));

		Notification::success(sprintf('%d students have been suspended!', $count));
		return Redirect::to($this->previousOrRoute('users.index'));

	}

	/**
	 * Suspend all students
	 * @param $id
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function unsuspendAll()
	{
		$count = $this->dept->users()->unsuspended()->update(array('suspended' => 1));

		Notification::success(sprintf('%d students have been unsuspended!', $count));
		return Redirect::route('users.index');

	}

    /**
     * Lock a user
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

		$currentUser = Auth::getUser();
		/* @var $currentUser User */

		if (!$user)
		{
			Notification::error('The user you tried to delete was not found.');
		}
		elseif ($currentUser->getKey() == $user->getKey())
		{
			Notification::error('You cannot delete yourself.');
		}
		elseif (!is_role(User::ROLE_SYSTEM_ADMIN) && $user->role > $currentUser->role)
		{
			Notification::error('You cannot delete this user.');;
		}
		elseif (!(is_role(User::ROLE_SYSTEM_ADMIN) || is_depts($user)))
		{
			Notification::error('You cannot delete a user in a different department.');;
		}
        else
        {
            //$user->delete(); //hard deletes the user, we don't want this

            $user->status = User::STATUS_DEACTIVATED;
            $user->timestamps = false;
            $user->forceSave();
            Notification::success(sprintf('User %s has been deactivated!', $user->userid));
            //Notification::success('The user has been deleted successfully.');
        }

        return Redirect::route('users.index');

    }

}