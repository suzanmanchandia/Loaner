<?php

use Carbon\Carbon;

class LoanController extends \Loaner\AdminController {

	/**
	 * Display a listing of loans.
	 *
	 * @return Response
	 */
	public function index()
	{
        $loans = Loan::query();

        $loans->with('department', 'user', 'kit', 'equipment');

        $pageTitle = 'Loans';

        // Using the same controller action
        // so we check which to display and change the query
        switch(Route::currentRouteName())
        {
            case 'loans.returned':
                $loans->returned();
                $pageTitle = 'Returned Loans';
                $currentRoute = 'loans.returned';
                break;
            case 'loans.long':
                $loans->longTerm();
                $pageTitle = 'Long-term Loans';
                $currentRoute = 'loans.long';
                break;
            case 'loans.overdue':
                $loans->overdue();
                $pageTitle = 'Overdue Loans';
                $currentRoute = 'loans.overdue';
                break;
            default:
                $loans->active();
                $currentRoute = 'loans.index';
        }

        if (Input::get('r')) {
            $loans->where('role', '=', Input::get('r'));
        }

        if (!Input::get('d')) {
            $loans->where('loans.deptID', '=', $this->deptID);
        }

        $direction  = Input::get('dir') == 'desc' ? 'desc' : 'asc';
        $department = new Department();
        $user = new User();
        $kit = new Kit();
        $equipment = new Equipment();
		$table = with(new Loan)->getTable();

		/* @var $query \Illuminate\Database\Eloquent\Builder */
		$loans->leftJoin($equipment->getTable(), $equipment->getTable().'.equipmentid', '=', $table.'.equipmentid');
		$loans->leftJoin($kit->getTable(), $kit->getTable().'.kitid', '=', $table.'.kitid');
		$loans->leftJoin($user->getTable(), $user->getTable().'.userNum', '=', $table.'.userNum');

		if (Input::get('q')) {
			$loans->where(
				function ($query) use ($table, $kit, $equipment, $user) {
					$query->orWhere($table.'.lid', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($table.'.notes', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($table.'.kitid', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($table.'.equipmentid', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($kit->getTable().'.kit_desc', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($kit->getTable().'.notes', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($equipment->getTable().'.equipment_desc', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($equipment->getTable().'.model', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($equipment->getTable().'.notes', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($user->getTable().'.fname', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($user->getTable().'.lname', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($user->getTable().'.userid', 'like', '%' . Input::get('q') . '%');
					$query->orWhere($user->getTable().'.usc_id', 'like', '%' . Input::get('q') . '%');
				}
			);
		}

        // Sortable
        switch (Input::get('sf')) {
            case 'lname':
                $loans->orderBy('lname', $direction);
                $loans->orderBy('fname', $direction);
                break;
            case 'desc':
                $loans->orderBy(sprintf("%1\$s.kit_desc", $kit->getTable()), $direction);
                $loans->groupBy('loans.lid');
                break;
            case 'kitid':
            case 'equipmentid':
            case 'issue_date':
            case 'userid':
            case 'due_date':
            case 'fine':
                $loans->orderBy(Input::get('sf'), $direction);
                break;
            case 'deptName':
//                $loans->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->loans()->getQualifiedParentKeyName());
                $loans->orderBy('deptID', $direction);
                break;
            default:
                $loans->orderBy('issue_date', $direction);
        }

        $paginate = (int)Input::get('pp', Loan::PERPAGE);

        $this->layout->content = View::make('loans.index', array(
            'loans' => $loans->paginate($paginate, array($table.'.*')),
            'pageTitle' => $pageTitle,
            'currentRoute' => $currentRoute
        ));
	}


    /**
     * Show the form for creating a new loan.
     *
     * @param mixed $id
     * @return Response
     */
	public function create($id = '')
	{
		$loan = new Loan();

        $type = '';
        $userid = '';
        $item   = false;
        /* @var $item \Loaner\EquipmentInterface */

        switch (Route::currentRouteName())
        {
            case 'loans.user':
                $userid = $id;
                break;
            case 'loans.kits':
                $type = 'kit';
                $item = Kit::find($id);
                break;
            case 'loans.equipment':
                $type = 'equipment';
                $item = Equipment::find($id);
                break;
        }

        if ($type && !$item) {
            Notification::error('Could not find that item!');
        }
        elseif ($item) {
            $item->checkStatus();

            if ($item->status == Kit::STATUS_LOANED) {
                Notification::error('The item you tried to check out is currently on loan!');
                return Redirect::to($this->previousOrRoute('loans.index'));
            }
        }

        $this->layout->content = View::make('loans.create', array(
                'loan' => $loan,
                'userid' => $userid,
                'id' => $id,
                'type' => $type,
            ));
	}


	/**
	 * Store a newly created loan
	 *
	 * @return Response
	 */
	public function store()
	{
        try
        {
            DB::beginTransaction();

            $user = User::query()->where('userid', '=', Input::get('userid'))->first();
			/* @var $user User */

            if (!$user)
            {
                throw new ErrorException('The user you tried to issue a loan for does not exist.');
            }
			elseif ($user->suspended)
			{
				return $this->errorResponse(array(
					'Sorry, you cannot issue a loan to a suspended user.')
				);
			}

            // Update conditions
            $equipmentConditions = (array) Input::get('equipment');
            $conditions          = Condition::query()->lists('condID', 'condName');
            $equipment           = Equipment::query()->with('condition')->whereIn('equipmentid', array_keys($equipmentConditions)+array(0))->get();
			$areas               = $user->accessAreas()->lists('accessid');
            /* @var $equipment Equipment[] */

            foreach ($equipment as $item)
            {
                $condition = array_get($conditions, array_get($equipmentConditions, $item->equipmentid));

                if (!$condition || $condition == $item->condID)
                {
                    continue;
                }

                $item->condID = $condition;
                $item->forceSave();
            }

            $items = (array) Input::get('item');

            foreach ($items as $item) {
                $loan = new Loan();

                $loan->userid      = $user->userid;
                $loan->userNum     = $user->userNum;
                $loan->issuedBy    = Auth::getUser()->userid;
                $loan->issue_date  = time();
                $loan->loan_length = $item['loan_length'];
                $loan->status      = $item['status'];
                $loan->notes       = $item['notes'];

				$itemAccess        = array();

                if ($item['loan_length'] != 0) {
                    $loan->due_date = mktime(13, 0, 0) + ($item['loan_length'] * 24 * 60 * 60) - 60;
                } elseif ($item['loan_length'] == 0) {
                    $loan->due_date = mktime(17, 0, 0) - 60;
                }

                $unavailable = Loan::query()
                    ->where('due_date', '>=', $loan->issue_date->format('U'))
                    ->where('issue_date', '<=', $loan->due_date->format('U'))
                    ->whereNotIn('status', array(Loan::STATUS_RETURNED, Loan::STATUS_CANCELED));

                switch ($item['type']) {
                    case 'kit':
                        $loan->kitid       = $item['itemid'];
                        $loan->equipmentid = '';

						$kit        = Kit::find($loan->kitid);
						$itemAccess = $kit->accessAreas()->lists('accessid');

                        // Check if kit or any equipment under it is checked out
                        $unavailable->where(
                            function ($query) use ($loan) {
                                $query->orWhere('kitid', '=', $loan->kitid);

                                if ($loan->kit->equipment()->count()) {
                                    $query->orWhereIn('equipmentid', $loan->kit->equipment->lists('equipmentid')+array(0));
                                }
                            }
                        );

                        break;
                    case 'equipment':
                        $loan->equipmentid = $item['itemid'];
                        $loan->kitid       = '';

						$equipment         = Equipment::find($item['itemid']);
						$itemAccess        = $equipment->accessAreas()->lists('accessid');

                        // Check if equipment or kit it belongs to it is checked out
                        $unavailable->where(
                            function ($query) use ($loan) {
                                $query->orWhere('equipmentid', '=', $loan->equipmentid);

                                if ($loan->equipment->kitid && $loan->equipment->kit) {
                                    $query->orWhereIn('kitid', $loan->equipment->kit()->lists('kitid')+array(0));
                                }
                            }
                        );
                        break;
                }

                //// Uncomment to restrict loan access.
				if (!count(array_intersect($areas, $itemAccess)))
				{
					return $this->errorResponse(array(
						sprintf('This user does not have access to %s #%s', $loan->kitid ? 'kit' : 'equipment', $item['itemid'])
					));
				}

				// Only issue loans within the department for the current kit or equipment.
				if ($loan->kitid)
				{
					$kit = Kit::findOrFail($loan->kitid);
                    $kit->checkStatus();
					$loan->deptID = $kit->deptID;
				}
				elseif ($loan->equipmentid)
				{
					$equipment = Equipment::findOrFail($loan->equipmentid);
                    $equipment->checkStatus();
					$loan->deptID = $equipment->deptID;
				}

                // Reservation or loan exists
                if ($unavailable->count() > 0) {
                    return $this->errorResponse(
                        array(
                            sprintf('There is a conflicting reservation or loan for %s: %s', $item['type'], $item['itemid'])
                        )
                    );
                }

                if (!$loan->save())
                {
                    return $this->errorResponse($loan->errors()->all());
                }

                if ($loan->kitid) {
                    $kit = $loan->kit;
                    $kit->status = Kit::STATUS_LOANED;
                    $kit->forceSave();

                    foreach ($kit->equipment as $equipment) {
                        $equipment->status = Kit::STATUS_LOANED;
                    }
                } else {
                    $equipment = $loan->equipment;
                    $equipment->status = Kit::STATUS_LOANED;
                    $equipment->forceSave();
                }
            }

            DB::commit();

            return $this->successResponse('All loan items were successfully checked out.');
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $this->errorResponse(array($ex->getMessage(), $ex->getCode(), $ex->getTraceAsString()));
        }
	}


	/**
	 * Display the specified loan. Used by the modal dialog
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
    {
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        $areas = array();
        $description = '';
        $equipment = array();

        if ($loan->hasKit())
        {
            $areas = $loan->kit->accessAreas;
            $description = $loan->kit->kit_desc;
            $equipment = $loan->kit->equipment;
        }
        elseif ($loan->hasEquipment())
        {
            $areas = $loan->equipment->accessAreas;
            $description = $loan->equipment->model;
            $equipment[] = $loan->equipment;
        }

        $this->layout = View::make('loans.show', array(
                'loan' => $loan,
                'areas' => $areas,
                'description' => $description,
                'equipment' => $equipment,
            ));
	}


	/**
	 * Show the form for editing the fine.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        $this->layout = View::make('loans.fine', array(
                'loan' => $loan,
            ));
	}

    /**
     * Show the form for editing the loan length.
     *
     * @param  int  $id
     * @return Response
     */
    public function editlength($id)
    {
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        $this->layout = View::make('loans.editloanlength', array(
            'loan' => $loan,
        ));
    }


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        $loan->fine = Input::get('fine');
        $loan->forceSave();
        $loan->user->calculateFine();

        return $this->successResponse('Fine updated.');
	}


    /**
     * Update the length of the loan.
     *
     * @param  int  $id
     * @return Response
     */
    public function updateloanlength($id)
    {
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }
        $updatedDueDate = Carbon::createFromFormat('m/d/Y', Input::get('new_due_date'));
        $newloanlength = $updatedDueDate->diff($loan->issue_date)->days;

        $loan->loan_length = $newloanlength;

        //Check if new date is same as before
        if($updatedDueDate->format('m/d/Y') == $loan->due_date->format('m/d/Y'))
        {
            return $this->errorResponse(array('This new due date is the same as before.'));
        }

        // Renenw count for short-term
        if ($loan->renew_count >=3 && $loan->status == Loan::STATUS_SHORT_TERM && !is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN))
        {
            return $this->errorResponse(array('This item has already been renewed a maximum of three times.'));
        }

        // Past due, only admin and system admin can renew
        if (!is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN) && $loan->isPastDue())
        {
            return $this->errorResponse(array('This loan is past due and cannot be renewed.'));
        }

        $end   = Carbon::now()->setTime(13, 0, 0)->addDays($loan->loan_length)->subMinute();

        if ($loan->hasUpcomingReservation())
        {
            return $this->errorResponse(array('This item cannot be renewed because there is an upcoming reservation.'));
        }

        $loan->renew();

        return $this->successResponse(sprintf('Loan successfully changed. Due back on %s', $end->format('m-d-y -- h:i A')));
    }


	/**
	 * Renew the loan
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function renew($id)
	{
        $loan = Loan::find($id);

        if (!$loan)
        {
            Notification::error('The loan you tried to renew was not found');
            return Redirect::to($this->previousOrRoute('loans.index'));
        }
        /* @var $loan Loan */

        // Renenw count for short-term
        if ($loan->renew_count >=3 && $loan->status == Loan::STATUS_SHORT_TERM)
        {
            Notification::error('This item has already been renewed a maximum of three times.');
            return Redirect::to($this->previousOrRoute('loans.index'));
        }

        // Past due, only admin and system admin can renew
        if (!is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN) && $loan->isPastDue())
        {
            Notification::error('This loan is past due and cannot be renewed.');
            return Redirect::to($this->previousOrRoute('loans.index'));
        }

        $end   = Carbon::now()->setTime(13, 0, 0)->addDays($loan->loan_length)->subMinute();

        if ($loan->hasUpcomingReservation())
        {
            Notification::error('This item cannot be renewed because there is an upcoming reservation.');
            return Redirect::to($this->previousOrRoute('loans.index'));
        }

        $loan->renew();

        Notification::success(sprintf('Loan successfully renewed. Due back on %s', $end->format('m-d-y -- h:i A')));
        return Redirect::to($this->previousOrRoute('loans.index'));
	}


	/**
	 * Return the current loan.
	 * <Damn, php, can't have a function called return>
	 * @param  int  $id
	 * @return Response
	 */
	public function getReturn($id)
	{
        if ($id)
        {
            $loan = Loan::query()->active()->find($id);

            if (!$loan)
            {
                Notification::error('The loan you tried to return was not found');
                return Redirect::route('loans.index');
            }

            if ($loan->kitid)
            {
                $equipment = $loan->kit->equipment;
            }
            else
            {
                $equipment = array($loan->equipment);
            }

            $this->layout = View::make('loans.return.simple', array(
                    'loan' => $loan,
                    'equipment' => $equipment
                ));
        }
        else
        {
            $loan = new Loan;

            $this->layout->content = View::make('loans.return.advanced', array(
                    'loan' => $loan,
                ));
        }
	}

	/**
	 * Return the current loan.
	 * <Damn, php, can't have a function called return>
	 * @param  int  $id
	 * @return Response
	 */
	public function processReturn($id)
	{
        $loan = Loan::find($id);
		/* @var $loan Loan */

        if (!$loan)
        {
			return $this->errorResponse(array('The loan you tried to return was not found'));
        }

        try
        {
            DB::beginTransaction();


            $equipmentConditions = (array) Input::get('equipment');
            $conditions          = Condition::query()->lists('condID', 'condName');
            $equipment           = Equipment::query()->with('condition')->whereIn('equipmentid', array_keys($equipmentConditions) + array(0) )->get();
            /* @var $equipment Equipment[] */

            foreach ($equipment as $item)
            {
                $condition = array_get($conditions, array_get($equipmentConditions, $item->equipmentid));

                if (!$condition || $condition == $item->condID)
                {
                    continue;
                }

                $format = array_search($condition, $conditions);

                switch ($format)
                {
                    case 'Missing':
                        $format = 'Lost';
                }

                // Update the item condition
                $item->condID = $condition;
                // Place the blame
                $item->notes  = trim($item->notes . PHP_EOL . PHP_EOL
                    . sprintf('%s by %s on %s', $format, $loan->userid, date(Config::get('formatting.dates.friendly'))));
                $user         = $loan->user;
                $user->notes  = trim($item->notes . PHP_EOL . PHP_EOL
                    . sprintf('%s equipment %s on %s', $format, $item->equipmentid, date(Config::get('formatting.dates.friendly'))));
                $item->forceSave();
                $user->forceSave();
            }

            // Update return date, status and notes
            $loan->return_date = time();
            $loan->status      = Loan::STATUS_RETURNED;
            $loan->save();

            if ($loan->kitid && $loan->kit)
            {
                $loan->kit->checkStatus();
            }
            elseif ($loan->equipmentid && $loan->equipment)
            {
                $loan->equipment->checkStatus();
            }

            DB::commit();

            Log::debug(print_r(DB::getQueryLog(), true));

            return $this->successResponse(sprintf('%s returned', $loan->kitid ? 'Kit' : 'Equipment'));
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $this->errorResponse(array($ex->getMessage(), $ex->getCode()));
        }
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        //
	}

    /**
     * Display user suggestions
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestUser()
    {
        $items = User::query()->whereActive();

        $results = array();

        foreach ($items->get() as $item)
        {
            $results[] = array(
                'id' => $item->userid,
                'name' => sprintf('%s %s <%s> %s', $item->fname, $item->lname, $item->email, $item->usc_id),
            );
        }

        return Response::json($results);
    }

    /**
     * Display equipment suggestions
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestEquipment()
    {
        $items = $this->dept->equipment();

        $results = array();

        foreach ($items->get() as $item)
        {
            $results[] = array(
                'id' => $item->equipmentid,
                'name' => sprintf('Model: %s', $item->model),
            );
        }

        return Response::json($results);
    }

    /**
     * Display kit suggestions for autosuggest
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestKit()
    {
        $items = $this->dept->kits()->whereActive();

        $results = array();

        foreach ($items->get() as $item)
        {
            $results[] = array(
                'id' => $item->kitid,
                'name' => sprintf('%s', $item->kit_desc),
            );
        }

        return Response::json($results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadKit()
    {
        $results = array('equipment' => array());
        $kit     = Kit::find(Input::get('q'));
        /* @var $kit Kit */

        if (!$kit)
        {
            return $this->errorResponse([
                sprintf('This kit %s does not exist.', Input::get('q'))
            ]);
            return $this->dataResponse($results);
        }

        $kit->checkStatus();

        if ($kit->status == Kit::STATUS_LOANED) {
            return $this->errorResponse([
                sprintf('This kit %s is currently on loan', $kit->kitid)
            ]);
        }

		/* @var $kit Kit */
		$results['loan_length'] = $kit->loan_length;

        foreach ($kit->equipment as $item)
        {
            $results['equipment'][] = $item->toJsonLoan();
        }

        return $this->dataResponse($results);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadEquipment()
    {
        $results = array();
        $item     = Equipment::find(Input::get('q'));
        /* @var $item Equipment */

        if (!$item)
        {
            return $this->dataResponse($results);
        }

        $item->checkStatus();

        if ($item->status == Equipment::STATUS_LOANED) {
            return $this->errorResponse([
                sprintf('This equipment %s is currently on loan.', $item->equipmentid)
            ]);
        }

        $results[] = $item->toJsonLoan();

        return $this->dataResponse($results);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actions()
    {
        $ids = (array) Input::get('ids');
        $start = Carbon::now();

        switch (Input::get('action'))
        {
            case 'renew':
                $loans = Loan::query()->whereIn('lid', $ids+array(0))->get();
                $success = array();
                $past    = array();
                $reserve = array();
                /* @var $loans Loan[] */
                foreach ($loans as $loan) {
                    if (!is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN) && $loan->isPastDue()) {
                        $past[] = $loan->lid;
                    }
                    elseif ($loan->hasUpcomingReservation()) {
                        $reserve[] = $loan->lid;
                    }
                    else {
                        $loan->renew();
                        $success[] = $loan->lid;
                    }
                }

                if ($success) {
                    Notification::success(sprintf('Loan(s) %s renewed successfully.', join(', ', $success)));
                }
                if ($past) {
                    Notification::error(sprintf('Loan(s) %s could not be renewed as they were past due.', join(', ', $past)));
                }
                if ($reserve) {
                    Notification::error(sprintf('Loan(s) %s conflict with upcoming reservations and cannot be renewed.', join(', ', $reserve)));
                }

                break;
        }

        return Redirect::to($this->previousOrRoute('loans.index'));
    }

    /**
     * Display form for returning multiple items
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getMultiReturn()
    {
        $ids = (array) Input::get('ids') + array(0);

        $loans = Loan::active()->whereIn('lid', $ids);

        if (!is_role(User::ROLE_SYSTEM_ADMIN))
        {
            $loans->whereIn('deptID', Auth::getUser()->departments()->remember(10)->get()->lists('deptID') + array(0));
        }

        $loans = $loans->get();
        /* @var $loans Loan[] */
        if (!count($loans)) {
            return $this->errorResponse(['Please select an available loan.']);
        }

        return Response::view('loans.return.multi', ['loans' => $loans]);
    }

    /**
     * Process multiple loans return
     * @return \Illuminate\Http\JsonResponse
     */
    public function processMultiReturn()
    {
        try
        {
            DB::beginTransaction();

            $loans    = Input::get('loans');
            $returned = array();

            foreach ($loans as $id => $value)
            {
                $loan                = Loan::find($id);
                $equipmentConditions = (array) array_get($value, 'equipment');
                $conditions          = Condition::query()->lists('condID', 'condName');
                $equipment           = Equipment::query()->with('condition')->whereIn('equipmentid', array_keys($equipmentConditions) + array(0) )->get();
                /* @var $equipment Equipment[] */
                /* @var $loan Loan */

                foreach ($equipment as $item)
                {
                    $condition = array_get($conditions, array_get($equipmentConditions, $item->equipmentid));

                    if (!$condition || $condition == $item->condID)
                    {
                        continue;
                    }

                    $format = array_search($condition, $conditions);

                    switch ($format)
                    {
                        case 'Missing':
                            $format = 'Lost';
                    }

                    // Update the item condition
                    $item->condID = $condition;
                    // Place the blame
                    $item->notes  = trim($item->notes . PHP_EOL . PHP_EOL
                        . sprintf('%s by %s on %s', $format, $loan->userid, date(Config::get('formatting.dates.friendly'))));
                    $user         = $loan->user;
                    $user->notes  = trim($item->notes . PHP_EOL . PHP_EOL
                        . sprintf('%s equipment %s on %s', $format, $item->equipmentid, date(Config::get('formatting.dates.friendly'))));
                    $item->forceSave();
                    $user->forceSave();
                }

                // Update return date, status and notes
                $loan->return_date = time();
                $loan->status      = Loan::STATUS_RETURNED;
                $loan->notes       = array_get($value, 'notes', '');
                $loan->save();

                if ($loan->hasKit())
                {
                    $loan->kit->checkStatus();
                }
                elseif ($loan->hasEquipment())
                {
                    $loan->equipment->checkStatus();
                }

                $returned[] = $loan->lid;
            }

            DB::commit();

            return $this->successResponse(sprintf('Loan(s) %s returned', join(', ', $returned)));
        }
        catch (Exception $ex)
        {
            DB::rollBack();
            return $this->errorResponse(array($ex->getMessage(), $ex->getCode()));
        }
    }

}
