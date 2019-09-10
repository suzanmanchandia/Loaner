<?php

use Illuminate\Mail\Message;

class EquipmentController extends \Loaner\AdminController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $equipment = Equipment::query()->where('status', '<>', -1);

        $equipment->with('kit', 'condition', 'category', 'location');

        if (Input::get('q')) {
            $equipment->where(function($query){
                    $query->orWhere('equipment_desc', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('notes', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('model', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('manufSerialNum', 'like', '%' . Input::get('q') . '%');
                    $query->orWhere('equipmentid', 'like', '%' . Input::get('q') . '%');
                });
        }
        if (Input::get('s')) {
            $equipment->where('status', '=', Input::get('s'));
        }
        if (Input::get('cat')) {
            $equipment->where('equipCatID', '=', Input::get('cat'));
        }
        if (Input::get('subcat')) {
            $equipment->where('equipSubCatID', '=', Input::get('subcat'));
        }
        if (Input::get('cond')) {
            $equipment->where('condID', '=', Input::get('cond'));
        }
        
        if (!Input::get('d')) {
            $equipment->where('deptID', '=', $this->deptID);
        }
        else {
            $equipment->with('department');
        }

        $direction  = Input::get('dir') == 'desc' ? 'desc' : 'asc';
        $department = new Department();

        // Sortable
        switch (Input::get('sf'))
        {
            case 'equipment_desc':
            case 'loan_lengthEQ':
            case 'status':
            case 'kitid':
                $equipment->orderBy(Input::get('sf'), $direction);
                break;
            case 'deptName':
//                $equipment->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->equipment()->getQualifiedParentKeyName());
                $equipment->orderBy('deptID', $direction);
                break;
            default:
                $equipment->orderBy('equipmentid', $direction);
        }

        $paginate = (int) Input::get('pp', Equipment::PERPAGE);

        $this->layout->content = View::make('equipment.index', array(
			'equipment' => $equipment->paginate($paginate, array( with(new Equipment)->getTable().'.*') )
			)
		);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $equipment = new Equipment;

        $access = Department::find($this->deptID)->accesssAreas;
        $this->scriptsInit();

        $this->layout->content = View::make('equipment.create', array(
            'equipment' => $equipment,
            'access' => $access,
            'deptID' => $this->deptID,
            'access' => AccessArea::all(),
        ));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        Equipment::unguard();

        $equipment = new Equipment();

        $equipment->fill( Input::only('equipmentid', 'kitid', 'loan_lengthEQ', 'manufacturer', 'manufSerialNum', 'expectedLifetime', 'model', 'equipCatID', 'locationID', 'condID', 'equipment_desc', 'notes', 'purchasePrice', 'ipAddress', 'macAddress', 'hostName', 'connectType', 'warrantyInfo', 'deptID') );
        $equipment->status = Equipment::STATUS_ACTIVE;

        if (Input::has('manufactureDate'))
        {
            $equipment->manufactureDate = strtotime(Input::get('manufactureDate'));
        }
        if (Input::has('purchaseDate'))
        {
            $equipment->purchaseDate = strtotime(Input::get('purchaseDate'));
        }

        $equipment->validate();

        $access = array_filter( (array) Input::get('access') );
        $errors = $equipment->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area.';
        }

        if (empty($errors))
        {
            try
            {
                DB::beginTransaction();
                $equipment->save();
                $equipment->accessAreas()->attach($access);
                DB::commit();
            }
            catch (Exception $ex)
            {
                DB::rollBack();
                return $this->errorResponse(array($ex->getMessage()));
            }

            Notification::success('Equipment created!');
            return $this->successResponse('Equipment created!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $equipment = Equipment::find($id);
        /* @var $equipment Equipment */

        if (!$equipment)
        {
            App::abort(404);
        }

        $equipment->checkStatus();

        $this->layout = View::make('equipment.show', array('item' => $equipment));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment || !is_dept($equipment->deptID)) {
            Notification::error('The equipment was not found');
            return Redirect::route('equipment.index');
        }

        $access = Department::find($this->deptID)->accesssAreas;
        $this->scriptsInit();

        $this->layout->content = View::make('equipment.edit', array(
                'equipment' => $equipment,
                'access' => $access,
                'access' => AccessArea::all(),
                'equipmentaccess' => $equipment->accessAreas()->lists('id'),
            ));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function duplicate($id)
    {
        $equipment = Equipment::find($id);

        if (!$equipment || !is_dept($equipment->deptID)) {
            Notification::error('The equipment was not found');
            return Redirect::route('equipment.index');
        }

        $access = Department::find($this->deptID)->accesssAreas;

        $this->scriptsInit();

        $this->layout->content = View::make('equipment.duplicate', array(
                'equipment' => $equipment,
                'access' => $access,
                'equipmentaccess' => $equipment->accessAreas()->lists('id'),
            ));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        $equipment = Equipment::find($id);
        /* @var $equipment Equipment */

        if (!($equipment && is_dept($equipment->deptID))) {
            return $this->notFoundResponse();
        }

        $equipment->unguard();

        $equipment->fill( Input::only('kitid', 'loan_lengthEQ', 'manufacturer', 'manufSerialNum', 'expectedLifetime', 'model', 'equipCatID', 'locationID', 'condID', 'equipment_desc', 'notes', 'purchasePrice', 'ipAddress', 'macAddress', 'hostName', 'connectType', 'warrantyInfo', 'deptID') );

        if (Input::has('manufactureDate'))
        {
            $equipment->manufactureDate = strtotime(Input::get('manufactureDate'));
        }
        else
        {
            $equipment->manufactureDate = 0;
        }
        if (Input::has('purchaseDate'))
        {
            $equipment->purchaseDate = strtotime(Input::get('purchaseDate'));
        }
        else
        {
            $equipment->purchaseDate = 0;
        }

        $equipment->validateUniques();

        $access = array_filter( (array) Input::get('access') );
        $errors = $equipment->errors()->all();

        if (empty($access))
        {
            $errors[] = 'You must select at least one access area';
        }

        if (empty($errors))
        {

            if ($equipment->isDirty('deptID') && $equipment->status == Equipment::STATUS_LOANED) {
                return $this->errorResponse(array(
                    'This equipment is already on loan. Please return the loan before you can change the department.',
                ));
            }

            $equipment->updateUniques();
            $equipment->accessAreas()->sync($access);
            Notification::success('Equipment updated!');
            return $this->successResponse('Equipment updated!');
        }
        else
        {
            return $this->errorResponse($errors);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show inactive equipment
     */
    public function inactive()
    {
        $equipment = InactiveEquipment::query()->where('deptID', '=', $this->deptID)->orderBy('equipmentid', 'asc');

        return View::make('equipment.inactive', array('equipment' => $equipment->get()));
    }

    /**
     * Reactivate a piece of equipment
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate($id)
    {
        $equipment = InactiveEquipment::query()->where('deptID', '=', $this->deptID)->find($id);
        /* @var $equipment InactiveEquipment */

        if (!$equipment)
        {
            Notification::error('The equipment you tried to activate was not found.');
        }
        else
        {
            Eloquent::unguard();

            $equipment->activate();


            Notification::success(sprintf('Equipment %s has been reactivated!', $id));
        }

        return Redirect::route('equipment.index');

    }


    /**
     * Show the form for deactivating the specified equipment.
     *
     * @param  int $id
     * @return Response
     */
    public function deactivate($id)
    {
        $equipment = $this->dept->equipment()->find($id);

        if (!$equipment)
        {
            App::abort(404);
        }

        $this->layout = View::make('equipment.deactivate', array('equipment' => $equipment));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function processDeactivate($id)
    {
        $equipment = $this->dept->equipment()->find($id);
        /* @var $equipment equipment */

        if (!$equipment)
        {
            App::abort(404);
        }

        if ($equipment->loans()->unavailable()->count())
        {
            return $this->errorResponse(array('The equipment is currently loaned out or reserved.'));
        }

        $note = new DeactivationNote();
        $note->fill(Input::only(array('deactivationNotes', 'disposalType')));
        $note->deptID = $equipment->deptID;
        $note->equipmentid  = $id;
        $note->userid = Auth::getUser()->userid;
        $note->name   = Auth::getUser()->fname . ' ' . Auth::getUser()->lname;

        $data = array(
            'equipment' => $equipment,
            'note' => $note
        );

        if ($note->save())
        {
            Mail::send('emails.equipment.deactivation', $data, function (Message $message) use ($equipment, $data){
                    $message->subject(sprintf('Equipment %s deactivated.', $equipment->equipmentid));

                    foreach (NotificationEmail::all() as $email)
                    {
                        $message->to($email->emailid, Config::get('mail.from.name'));
                    }

                    if (Config::get('mail.pretend')) Log::info(View::make('emails.equipment.deactivation', $data)->render());
                });

            $equipment->deactivate();

            return $this->successResponse('Equipment deactivated.');
        }

        return $this->errorResponse($note->errors()->all());
    }

    protected function scriptsInit()
    {
        $subs = array();

        foreach (EquipmentSubCategory::all() as $category)
        {
            $subs['cat'.$category->equipCatID][] = array('id' => $category->equipSubCatID, 'name' => $category->equipSubName);
        }

        if (is_role(User::ROLE_SYSTEM_ADMIN)) {
            $departments = Department::query();
        }
        else {
            $departments = Auth::getUser()->departments();
        }

        $departments->select('dept.deptID', 'deptName')->where('dept.deptID', '>', '0');


        View::share('subcategories', $subs);
        View::share('departments', $departments->lists('deptName', 'deptID'));
    }


}
