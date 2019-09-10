<?php

use Carbon\Carbon;

class ReservationController extends \Loaner\AdminController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $loans = Loan::query();

        $loans->with('department', 'user', 'kit', 'equipment');

        switch (Route::currentRouteName()) {
            case 'reservations.canceled':
                $loans->canceled();
                break;
            default:
                $loans->reservations();
        }

		$direction  = Input::get('dir') == 'desc' ? 'desc' : 'asc';
		$department = new Department();
		$user = new User();
		$kit = new Kit();
		$equipment = new Equipment();
		$table = with(new Loan)->getTable();

        if (!Input::get('d')) {
            $loans->where('loans.deptID', '=', $this->deptID);
        }

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
				//$loans->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->loans()->getQualifiedParentKeyName());
				$loans->orderBy('deptID', $direction);
				break;
			default:
				$loans->orderBy('issue_date', $direction);
		}

		$paginate = (int)Input::get('pp', Loan::PERPAGE);

        $this->layout->content = View::make('reservations.index', array('loans' => $loans->paginate($paginate, array($table.'.*'))));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $loan = new Loan();

        $this->layout->content = View::make(
            'reservations.create',
            array(
                'loan' => $loan,
            )
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        try {
            DB::beginTransaction();

            $user = User::query()->where('userid', '=', Input::get('userid'))->first();

            if (!$user) {
                return $this->errorResponse(array('The user you tried to issue a loan for does not exist.'));
            }
			elseif ($user->suspended)
			{
				return $this->errorResponse(array(
						'Sorry, you cannot create a reservation for a suspended user.')
				);
			}

            $item = Input::only('itemid', 'loan_length', 'type', 'notes', 'issue_date');


            $loan = new Loan();

            $loan->userid      = $user->userid;
            $loan->userNum     = $user->userNum;
            $loan->issuedBy    = Auth::getUser()->userid;
            $loan->issue_date  = Carbon::createFromTimestamp(strtotime($item['issue_date'] . ' 13:00:00'));
            $loan->loan_length = $item['loan_length'];
            $loan->status      = Loan::STATUS_RESERVED;
            $loan->notes       = $item['notes'];
			$areas             = $user->accessAreas()->lists('accessid');

            if ($item['loan_length'] != 0) {
                $loan->due_date = with(clone $loan->issue_date)->addDays($item['loan_length'])->subMinute();
            } else {
                return $this->errorResponse(array('You need a loan length.'));
            }

            $unavailable = Loan::query()
                ->where('due_date', '>=', $loan->issue_date->format('U'))
                ->where('issue_date', '<=', $loan->due_date->format('U'))
                ->whereNotIn('status', array(Loan::STATUS_RETURNED, Loan::STATUS_CANCELED));

            switch ($item['type']) {
                case 'kit':
                    $loan->kitid       = $item['itemid'];
                    $loan->equipmentid = '';

					$kit        = Kit::find($item['itemid']);
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

			if (!count(array_intersect($areas, $itemAccess)))
			{
//				return $this->errorResponse(array(
//					sprintf('This user does not have access to %s #%s', $loan->kitid ? 'kit' : 'equipment', $item['itemid'])
//				));
			}

            // Reservation or loan exists
            if ($unavailable->count() > 0) {
                return $this->errorResponse(
                    array(
                        sprintf('There is a conflicting reservation or loan for %s: %s', $item['type'], $item['itemid'])
                    )
                );
            }

			// Only issue loans within the department for the current kit or equipment.
			if ($loan->kitid)
			{
				$kit = Kit::findOrFail($loan->kitid);
				$loan->deptID = $kit->deptID;
			}
			elseif ($loan->equipmentid)
			{
				$equipment = Equipment::findOrFail($loan->equipmentid);
				$loan->deptID = $equipment->deptID;
			}

            if (!$loan->save()) {
                return $this->errorResponse($loan->errors()->all());
            }

            DB::commit();

            return $this->successResponse('Reservation made.');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->errorResponse(array($ex->getMessage(), $ex->getCode(), $ex->getTraceAsString()));
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

        $this->layout = View::make('reservations.show', array(
                'loan' => $loan,
                'areas' => $areas,
                'description' => $description,
                'equipment' => $equipment,
            ));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //

        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        if ($loan->kitid)
        {
            $equipment = $loan->kit->equipment;
        }
        else
        {
            $equipment = array($loan->equipment);
        }

        $this->layout = View::make('reservations.edit', array(
            'loan' => $loan,
            'equipment' => $equipment
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
        //
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        /* @var $loan Loan */


        $updatedDate = Carbon::createFromFormat('m/d/Y', Input::get('issue_date'));
        $loanLength = Input::get('loan_length');

        $unavailable = Loan::query()
            ->where('issue_date', '<=', $updatedDate->format('U'))
            ->where('due_date', '>=', $updatedDate->format('U'))
            ->where('lid', '!=', $id)
            ->whereNotIn('status', array(Loan::STATUS_RETURNED, Loan::STATUS_CANCELED));



        if ($loan->hasKit()) {
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
        }
        else {
            $unavailable->where('equipmentid', '=', $loan->equipmentid);
        }

        if($unavailable->count() > 0)
        {
            return $this->errorResponse(array('The equipment is currently unavailable.'));
        }
        else
        {
            $loan->issue_date = $updatedDate;
            $loan->due_date = $updatedDate->setTime(13,0,0)->addDays($loanLength)->subMinute();
            if (!$loan->save())
            {
                return $this->errorResponse($loan->errors()->all());
            }
            return $this->successResponse('Loan has been successfully updated.');
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
        $loan = Loan::reservations()->find($id);

        if (!$loan)
        {
            Notification::error('The reservation you tried to issue was not found');
            return Redirect::route('reservations.index');
        }

        $loan->status = Loan::STATUS_CANCELED;
        $loan->forceSave();

        Notification::success('You have successfuly canceled the reservation.');
        return Redirect::route('reservations.index');
    }

    public function load()
    {
        $equipment = Equipment::query();

		$results = array('equipment' => array());

        switch (Input::get('type')) {
            case 'kit':
				$kit = Kit::find(Input::get('id'));
				/* @var $kit Kit */
				if (!$kit)
				{
					return $this->dataResponse($results);
				}
				$results['loan_length'] = $kit->loan_length;
                $equipment->where('kitid', '=', Input::get('id'));
                break;
            default:
                $equipment->where('equipmentid', '=', Input::get('id'));
                break;
        }

        foreach ($equipment->get() as $item) {
			/* @var $item Equipment */
			if (Input::get('type') != 'kit') {
				$results['loan_length'] = $item->loan_lengthEQ;
			}

            $results['equipment'][] = $item->toJsonLoan();
        }

        return $this->dataResponse($results);
    }

    /**
     * Return the current loan.
     * <Damn, php, can't have a function called return>
     * @param  int  $id
     * @return Response
     */
    public function issue($id)
    {
        $loan = Loan::find($id);

        if (!$loan)
        {
            App::abort(404);
        }

        if ($loan->kitid)
        {
            $equipment = $loan->kit->equipment;
        }
        else
        {
            $equipment = array($loan->equipment);
        }

        $this->layout = View::make('reservations.issue', array(
                'loan' => $loan,
                'equipment' => $equipment
            ));
    }

    /**
     * Issue the reservation as a loan.
     * <Damn, php, can't have a function called return>
     * @param  int  $id
     * @return Response
     */
    public function processIssue($id)
    {
        $loan = Loan::find($id);
		/* @var $loan Loan */

        if (!$loan || $loan->status != Loan::STATUS_RESERVED)
        {
            App::abort(404);
        }

//        if ($loan->issue_date->gt(Carbon::createFromTime(13, 0, 0)))
//        {
//            return $this->errorResponse(array('The loan cannot be issued until ' . $loan->issue_date->format(Config::get('formatting.dates.friendlyTime'))));
//        }
		elseif ($loan->user->suspended)
		{
			return $this->errorResponse(array(
					'Sorry, you cannot issue a loan to a suspended user.')
			);
		}

        try
        {
            DB::beginTransaction();

            // Update conditions
            $equipmentConditions = (array) Input::get('equipment');
            $conditions          = Condition::query()->lists('condID', 'condName');
            $equipment           = Equipment::query()->with('condition')->whereIn('equipmentid', array_keys($equipmentConditions)+array(0))->get();
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

            $loan->status = Loan::STATUS_SHORT_TERM;

            // Check if this item is already on loan
            if ($loan->kitid) {
                $item = $loan->kit;
                $item->checkStatus();

                if ($item->status == Kit::STATUS_LOANED) {
                    return $this->errorResponse([sprintf('Kit #%s is already on loan.', $item->kitid)]);
                }
            } else {
                $item = $loan->equipment;
                $item->checkStatus();

                if ($item->status == Equipment::STATUS_LOANED) {
                    return $this->errorResponse([sprintf('Equipment #%s is already on loan.', $item->equipmentid)]);
                }
            }

            $loan->issue_date  = time();
            if (!$loan->save())
            {
                return $this->errorResponse($loan->errors()->all());
            }

            // Update status
            if ($loan->kitid) {
                $loan->kit->checkStatus();
            } else {
                $loan->equipment->checkStatus();
            }

            DB::commit();

            return $this->successResponse('Loan has been issued.');

        }
        catch (Exception $ex)
        {
            return $this->errorResponse(array($ex->getMessage(), $ex->getTraceAsString()));
        }

    }



}
