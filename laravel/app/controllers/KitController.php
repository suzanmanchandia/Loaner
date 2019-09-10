<?php

use Illuminate\Mail\Message;

class KitController extends \Loaner\AdminController
{

    /**
     * Display a listing of the kit.
     *
     * @return Response
     */
    public function index()
    {
        $kits = Kit::query()->whereActive();

        if (Input::get('q')) {
			$kits->where(function($query){
				$query->orWhere('kit_desc', 'like', '%' . Input::get('q') . '%');
				$query->orWhere('kitid', 'like', '%' . Input::get('q') . '%');
				$query->orWhere('notes', 'like', '%' . Input::get('q') . '%');
			});
        }

        if (!Input::get('d')) {
            $kits->where('deptID', '=', $this->deptID);
        }
        if (!Input::get('d')) {
            $kits->with('department');
        }

        $direction  = Input::get('dir') == 'desc' ? 'desc' : 'asc';
        $department = new Department();

        // Sortable
        switch (Input::get('sf'))
        {
            case 'kit_desc':
            case 'loan_length':
            case 'status':
                $kits->orderBy(Input::get('sf'), $direction);
                break;
            case 'deptName':
//                $kits->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->kit()->getQualifiedOtherKeyName());
                $kits->orderBy('deptID', $direction);
                break;
            default:
                $kits->orderBy('kitid', $direction);
        }

        $paginate = (int) Input::get('pp', Kit::PERPAGE);

        $this->layout->content = View::make('kits.index', array('kits' => $kits->paginate($paginate, array( with(new Kit)->getTable().'.*'))));
    }


    /**
     * Show the form for creating a new kit.
     *
     * @return Response
     */
    public function create()
    {
        $kit = new Kit;

        $access = $this->dept->accesssAreas;
        $this->scriptsInit();

        $this->layout->content = View::make('kits.create', array(
            'kit' => $kit,
            'access' => $access,
            'deptID' => $this->deptID,
            'access' => AccessArea::all(),
        ));
    }


    /**
     * Store a newly created kit in storage.
     *
     * @return Response
     */
    public function store()
    {
        Kit::unguard();

        $kit = new Kit();

        $kit->fill( Input::only('kitid', 'loan_length', 'kit_desc', 'notes', 'deptID') );
        $kit->status = Kit::STATUS_ACTIVE;

        $kit->validate();

        $access = array_filter( (array) Input::get('access') );
        $errors = $kit->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area.';
        }

        if (empty($errors))
        {
            $kit->save();
            $kit->accessAreas()->attach($access);
            Notification::success('Kit created!');
            return $this->successResponse('Kit created!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Display the specified kit.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $kit = Kit::find($id);
        /* @var $kit Kit */

        if (!$kit)
        {
            App::abort(404);
        }

        if ($kit->inactive())
        {
            $this->layout = View::make('kits.inactive', array('kit' => $kit));
        }
        else
        {
            $kit->checkStatus();
            $this->layout = View::make('kits.show', array('kit' => $kit));
        }

    }


    /**
     * Show the form for editing the specified kit.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $kit = $this->dept->kits()->find($id);

        if (!$kit || !is_dept($kit->deptID)) {
            Notification::error('The kit was not found');
            return Redirect::route('kits.index');
        }

        $access = Department::find($this->deptID)->accesssAreas;
        $this->scriptsInit();

        $this->layout->content = View::make('kits.edit', array(
                'kit' => $kit,
                'access' => $access,
                'access' => AccessArea::all(),
                'kitaccess' => $kit->accessAreas()->lists('id'),
            ));
    }


    /**
     * Show the form for editing the specified kit.
     *
     * @param  int $id
     * @return Response
     */
    public function duplicate($id)
    {
        $kit = $this->dept->kits()->find($id);

        if (!$kit) {
            Notification::error('The kit was not found');
            return Redirect::route('kits.index');
        }

        $access = Department::find($this->deptID)->accesssAreas;
        $this->scriptsInit();

        $this->layout->content = View::make('kits.duplicate', array(
                'kit' => $kit,
                'access' => $access,
                'kitaccess' => $kit->accessAreas()->lists('id'),
            ));
    }


    /**
     * Update the specified kit in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $kit = $this->dept->kits()->find($id);
        /* @var $kit Kit */

        if (!$kit) {
            return $this->notFoundResponse();
        }

        $kit->unguard();

        $kit->fill( Input::only('loan_length', 'kit_desc', 'notes', 'deptID') );

        $kit->validateUniques();

        $access = array_filter( (array) Input::get('access') );
        $errors = $kit->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area';
        }

        if (empty($errors))
        {
            if ($kit->isDirty('deptID') && $kit->status == Kit::STATUS_LOANED) {
                return $this->errorResponse(array(
                    'This kit is already on loan. Please return the loan before you can change the department.',
                ));
            }

            $kit->updateUniques();
            $kit->accessAreas()->sync($access);
            Notification::success('Kit updated!');
            return $this->successResponse('Kit updated!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Remove the specified kit from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    /**
     * Show the form for deactivating the specified kit.
     *
     * @param  int $id
     * @return Response
     */
    public function deactivate($id)
    {
        $kit = $this->dept->kits()->find($id);

        if (!$kit)
        {
            App::abort(404);
        }

        $this->layout = View::make('kits.deactivate', array('kit' => $kit));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function processDeactivate($id)
    {
        $kit = $this->dept->kits()->find($id);
        /* @var $kit Kit */

        if (!$kit)
        {
            App::abort(404);
        }

        if ($kit->loans()->unavailable()->count())
        {
            return $this->errorResponse(array('The kit is currently loaned out or reserved.'));
        }

        $note = new DeactivationNote();
        $note->fill(Input::only(array('deactivationNotes', 'disposalType')));
        $note->deptID = $kit->deptID;
        $note->kitid  = $id;
        $note->userid = Auth::getUser()->userid;
        $note->name   = Auth::getUser()->fname . ' ' . Auth::getUser()->lname;

        $data = array(
            'kit' => $kit,
            'note' => $note
        );

        if ($note->save())
        {
            Mail::send('emails.kit.deactivation', $data, function (Message $message) use ($kit, $data){
                    $message->subject(sprintf('Kit %s deactivated.', $kit->kitid));

                    foreach (NotificationEmail::all() as $email)
                    {
                        $message->to($email->emailid, Config::get('mail.from.name'));
                    }

                    if (Config::get('mail.pretend')) Log::info(View::make('emails.kit.deactivation', $data)->render());
                });

            $kit->deactivate();

            return $this->successResponse('Kit deactivated.');
        }

        return $this->errorResponse($note->errors()->all());
    }

    /**
     * Show inactive kits
     */
    public function inactive()
    {
        $kits = Kit::query()->whereInActive()->where('deptID', '=', $this->deptID)->orderBy('kitid', 'asc');

        return View::make('kits.inactive', array('kits' => $kits->get()));
    }

    /**
     * Reactivate a kit
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate($id)
    {
        $kit = Kit::query()->whereInActive()->where('deptID', '=', $this->deptID)->find($id);

        if (!$kit)
        {
            Notification::error('The kit you tried to activate was not found.');
        }
        else
        {
            $kit->activate();
            Notification::success(sprintf('Kit %s has been reactivated!', $id));
        }
        
        return Redirect::route('kits.index');

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggest()
    {
        $q = sprintf('%%%s%%', Input::get('q'));

        $kits = Kit::query()->whereActive();

        $results = array();

        $kits->where(function($query) use ($q) {
                $query->orWhere('kit_desc', 'like', $q);
                $query->orWhere('kitid', 'like', $q);
            });

        foreach ($kits->get() as $kit)
        {
            $results[] = array(
                'id' => $kit->kitid,
                'name' => $kit->kit_desc,
            );
        }

        return Response::json($results);
    }

    protected function scriptsInit()
    {
        if (is_role(User::ROLE_SYSTEM_ADMIN)) {
            $departments = Department::query();
        }
        else {
            $departments = Auth::getUser()->departments();
        }

        $departments->select('dept.deptID', 'deptName')->where('dept.deptID', '>', '0');
        View::share('departments', $departments->lists('deptName', 'deptID'));
    }

}
