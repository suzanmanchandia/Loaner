<?php

use Carbon\Carbon;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;

class ReportController extends \Loaner\AdminController {

    protected function getDept()
    {
        if (array_key_exists('dept', Input::all()))
        {
            Session::set('report.dept', Input::get('dept'));
        }

        View::share('report_dept', Session::get('report.dept', $this->deptID));

        return Session::get('report.dept', $this->deptID);
    }

    protected function loanQuery(Carbon $start, Carbon $end)
    {
        //
		if ($this->getDept()) {
			$depts = array($this->getDept());
		} else {
            $depts = array_keys($this->departments());
        }

        $query = Loan::query()->whereIn('loans.deptID', $depts)->with('user')
            ->orderBy('return_date', 'asc')
            ->orderBy('due_date', 'asc');
        /* @var $query \Illuminate\Database\Query\Builder */

        $loans = clone $query;

        $kits = clone $query;
        $kits->where('equipmentid', '=', '')->with('kit');

        $equipment = clone $query;
        $equipment->where('kitid', '=', '')->with('equipment');

        $kitsOverdue = clone $kits;
        $equipmentOverdue = clone $equipment;

        $kitFines = clone $query;
        $equipmentFines = clone $query;

        $loans
            ->where(function($query) use ($start, $end){
                return $query->orWhere(function($q) use ($start, $end){
                    return $q
                        ->where('issue_date', '>=', $start->format('U'))
                        ->where('issue_date', '<=', $end->format('U'));
                })->orWhere(function($q) use ($start, $end){
                    return $q
                        ->where('return_date', '>=', $start->format('U'))
                        ->where('return_date', '<=', $end->format('U'));
                });
            })
            ->with('kit', 'equipment');
        $returned = clone $loans;
        $overdue =  clone $loans;

        $loans->active()->typeLoan();
        $returned->returned();
        $overdue->overdueOpen();

        $kits
            ->where('issue_date', '>=', $start->format('U'))
            ->where('issue_date', '<=', $end->format('U'))
            ->where('status', '<=', Loan::STATUS_RETURNED);
        $equipment
            ->where('issue_date', '>=', $start->format('U'))
            ->where('issue_date', '<=', $end->format('U'))
            ->where('status', '<=', Loan::STATUS_RETURNED);
        $kitsOverdue
            ->where('due_date', '>=', $start->format('U'))
            ->where('due_date', '<=', $end->format('U'))
            ->where('due_date', '<', DB::raw('return_date'));
        $equipmentOverdue
            ->where('due_date', '>=', $start->format('U'))
            ->where('due_date', '<=', $end->format('U'))
            ->where('due_date', '<', DB::raw('return_date'));
        $kitFines
            ->where('issue_date', '>=', $start->format('U'))
            ->where('issue_date', '<=', $end->format('U'));
        $equipmentFines
            ->where('issue_date', '>=', $start->format('U'))
            ->where('issue_date', '<=', $end->format('U'));

        return array(
            'start' => $start,
            'end' => $end,
            'kits' => $kits->remember(1)->count(),
            'equipment' => $equipment->remember(1)->count(),
            'kitsOverdue' => $kitsOverdue->remember(1)->count(),
            'equipmentOverdue' => $equipmentOverdue->remember(1)->count(),
            'loans' => $loans->remember(1)->get(),
            'returned' => $returned->remember(1)->get(),
            'overdue' => $overdue->remember(1)->get(),
            'kitFines' => $kitFines->remember(1)->sum('fine'),
            'equipmentFines' => $equipmentFines->remember(1)->sum('fine'),
        );
    }

    protected function deactivationQuery(Carbon $start, Carbon $end)
    {
        if ($this->getDept()) {
            $depts = array($this->getDept());
        }
		else {
			$depts = array_keys($this->departments());
		}

        $query = DeactivationNote::query()->whereIn('deactivationNotes.deptID', $depts)->with('user')
            ->orderBy('deactivate_date', 'asc');
        /* @var $query \Illuminate\Database\Query\Builder */
        $query->with('kit', 'equipment');
        $query->where('deactivate_date', '>=', $start->format('U'))
            ->where('deactivate_date', '<=', $end->format('U'));

        return array(
            'start' => $start,
            'end' => $end,
            'notes' => $query->remember(1)->get(),
        );
    }

    protected function megaQuery(Carbon $start, Carbon $end)
    {
		$additions = Equipment::query();
		$deletions = InactiveEquipment::query();

		$equipment = new Equipment;
		$inactive  = new InactiveEquipment();

		$additions->with('kit', 'category', 'subCategory', 'department', 'location', 'condition');
		$deletions->with('kit', 'category', 'subCategory', 'department', 'location', 'condition');

		// Filter department
		if ($this->getDept())
		{
			$additions->where(sprintf('%s.%s', $equipment->getTable(), $equipment->department()->getForeignKey()), '=', $this->getDept());
			$deletions->where(sprintf('%s.%s', $inactive->getTable(), $inactive->department()->getForeignKey()), '=', $this->getDept());
		}
		// Filter category
		if (Input::get('cat'))
		{
			$additions->where(sprintf('%s.%s', $equipment->getTable(), $equipment->category()->getForeignKey()), '=', Input::get('cat'));
			$deletions->where(sprintf('%s.%s', $inactive->getTable(), $inactive->category()->getForeignKey()), '=', Input::get('cat'));
		}
		// Filter sub category
		if (Input::get('sub'))
		{
			$additions->where(sprintf('%s.%s', $equipment->getTable(), $equipment->subCategory()->getForeignKey()), '=', Input::get('sub'));
			$deletions->where(sprintf('%s.%s', $inactive->getTable(), $inactive->subCategory()->getForeignKey()), '=', Input::get('sub'));
		}
		// Filter sub category
		if (Input::get('cond'))
		{
			$additions->where(sprintf('%s.%s', $equipment->getTable(), $equipment->condition()->getForeignKey()), '=', Input::get('cond'));
			$deletions->where(sprintf('%s.%s', $inactive->getTable(), $inactive->condition()->getForeignKey()), '=', Input::get('cond'));
		}

		$additions->where(sprintf('%s.%s', $equipment->getTable(), Equipment::CREATED_AT), '>=', $start->format('U'));
		$deletions->where(sprintf('%s.%s', $inactive->getTable(), InactiveEquipment::UPDATED_AT), '>=', $start->format('U'));

        return array(
            'start' => $start,
            'end' => $end,
            'additions' => $additions->remember(1)->get(),
            'deletions' => $deletions->remember(1)->get(),
			'loans' => $this->loanQuery($start, $end),
        );
    }

	/**
	 * @return array
	 */
	protected function departments()
	{
		return is_role(User::ROLE_SYSTEM_ADMIN) ? Department::query()->where('dept.deptID', '>', 0)->lists('deptName', 'deptID') : Auth::getUser()->departments()->where('deptID', '>', 0)->lists('deptName', 'deptID');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function deactivation()
	{
        $customFromDate = Input::get('fromDate');
        $customToDate = Input::get('toDate');



        $today = Carbon::createFromTime(13, 0, 0); // obvious
        $present  = clone $today; // past week end
        $year  = Carbon::createFromDate(null, 1, 1)->setTime(13, 0, 0);
        $month  = Carbon::createFromDate(null, null, 1)->setTime(13, 0, 0);

        $pastWeek = clone $present;
        $pastWeek->subWeeks(1);;

        $pastMonth = clone $present;
        $pastMonth->subMonths(1);

        $pastYear = clone $present;
        $pastYear->subYears(1);

        $years = range(2010, date("Y"));
        $months = range(0,12);
        $days = range(0,31);

        if($customFromDate && $customToDate) {
            $customFromDate = Carbon::createFromFormat('m/d/Y', $customFromDate);
            $customToDate = Carbon::createFromFormat('m/d/Y', $customToDate);

            $this->layout->content = View::make('reports.deactivation.index', array(
                'year' => $this->deactivationQuery($pastYear, $today) + array('format' => 'year'),
                'month' => $this->deactivationQuery($pastMonth, $today) + array('format' => 'month'),
                'week' => $this->deactivationQuery($pastWeek, $present) + array('format' => 'week'),
                'custom' => $this->deactivationQuery($customFromDate, $customToDate) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => $customFromDate->format('m/d/Y'),
                'customToDate' => $customToDate->format('m/d/Y'),
                'customDateIsSet' => true,
                //  'months' => $months,
                //  'days' => $days,
            ));
        }
        else {
            $this->layout->content = View::make('reports.deactivation.index', array(
                'year' => $this->deactivationQuery($pastYear, $today) + array('format' => 'year'),
                'month' => $this->deactivationQuery($pastMonth, $today) + array('format' => 'month'),
                'week' => $this->deactivationQuery($pastWeek, $present) + array('format' => 'week'),
                'custom' => $this->deactivationQuery($present, $present) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => '',
                'customToDate' => '',
                'customDateIsSet' => false,
            ));
        }
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $customFromDate = Input::get('fromDate');
        $customToDate = Input::get('toDate');

        $today = Carbon::createFromTime(13, 0, 0); // obvious
        $present  = clone $today; // past week end
        $year  = Carbon::createFromDate(null, 1, 1)->setTime(13, 0, 0);
        $month  = Carbon::createFromDate(null, null, 1)->setTime(13, 0, 0);

        $pastWeek = clone $present;
        $pastWeek->subWeeks(1);

        $pastMonth = clone $present;
        $pastMonth->subMonths(1);

        $pastYear = clone $present;
        $pastYear = $pastYear->subYears(1);

        if($customFromDate && $customToDate) {
            $customFromDate = Carbon::createFromFormat('m/d/Y', $customFromDate);
            $customToDate = Carbon::createFromFormat('m/d/Y', $customToDate);

            $this->layout->content = View::make('reports.loans.index', array(
                'year' => $this->loanQuery($pastYear, $today) + array('format' => 'year'),
                'month' => $this->loanQuery($pastMonth, $today) + array('format' => 'month'),
                'week' => $this->loanQuery($pastWeek, $present) + array('format' => 'week'),
                'custom' => $this->loanQuery($customFromDate, $customToDate) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => $customFromDate->format('m/d/Y'),
                'customToDate' => $customToDate->format('m/d/Y'),
                'customDateIsSet' => true,
            ));
        }else {
            $this->layout->content = View::make('reports.loans.index', array(
                'year' => $this->loanQuery($pastYear, $today) + array('format' => 'year'),
                'month' => $this->loanQuery($pastMonth, $today) + array('format' => 'month'),
                'week' => $this->loanQuery($pastWeek, $present) + array('format' => 'week'),
                'custom' => $this->loanQuery($present, $present) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => '',
                'customToDate' => '',
                'customDateIsSet' => false,
            ));
        }
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function loansPDF($format)
	{
        $today = Carbon::createFromTime(13, 0, 0); // obvious
        $present  = clone $today; // past week end
        $year  = Carbon::createFromDate(null, 1, 1)->setTime(13, 0, 0);
        $month  = Carbon::createFromDate(null, null, 1)->setTime(13, 0, 0);

        $past = clone $present;
        $past->subDays(7);

        switch ($format)
        {
            case 'year':
                $data = $this->loanQuery($year, $today) + array('format' => 'year');
                break;
            case 'month':
                $data = $this->loanQuery($month, $today) + array('format' => 'month');
                break;
            default:
                $data = $this->loanQuery($past, $present) + array('format' => 'week');
                break;
        }

        $pdf = PDF::loadHTML(View::make('reports.loans.pdf', $data)->__tostring());

        return $pdf->stream();
	}


	/**
	 * List all users.
	 *
	 * @return Response
	 */
	public function users()
	{
        $users = User::query();

		if ($this->getDept()) {
			$depts = array($this->getDept());
		}
		else {
			$depts = array_keys($this->departments());
		}

		$depts = $depts + array(0);
		$users->whereHas('departments', function($q) use ($depts)
		{
			$q->whereIn('dept.deptID', $depts);
		});

        $this->layout->content = View::make('reports.users.index', array(
                'users' => $users->get(),
				'departments' => $this->departments(),
            ));
	}

    public function showUser($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $this->layout = View::make('reports.users.show', array(
                'user' => $user,
                'active' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->active()->get()),
                'overdue' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->overdue()->get()),
                'returned' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->returned()->get()),
            ));
    }

    public function usersPDF($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $pdf = PDF::loadHTML(View::make('reports.users.pdf', array(
                    'user' => $user,
                    'active' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->active()->get()),
                    'overdue' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->overdue()->get()),
                    'returned' => array('user' => $user, 'loans' => $user->loans()->with('kit', 'equipment')->returned()->get()),
                )));

        return $pdf->stream();
    }


	/**
	 * Display the specified resource.
	 *
	 * @return Response
	 */
	public function categories()
	{
        $categories = EquipmentCategory::query()->get();

        $this->layout->content = View::make('reports.categories.index', array(
                'categories' => $categories,
            ));
	}

    public function showCategory($id)
    {
        $category = EquipmentCategory::find($id);

        if (!$category)
        {
            App::abort(404);
        }

		$equipment = $category->equipment()->with('location', 'subCategory');

		if ($this->getDept()) {
			$depts = array($this->getDept());
		}
		else {
			$depts = array_keys($this->departments());
		}

		$depts = $depts + array(0);
		$equipment->whereIn('deptID', $depts);

        $this->layout = View::make('reports.categories.show', array(
                'category' => $category,
                'equipment' => $equipment->get(),
				'departments' => $this->departments(),
            ));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function categoriesExport($id)
	{
        $category = EquipmentCategory::find($id);

        if (!$category)
        {
            App::abort(404);
        }

        $data = array(
            array(
                'Barcode',
                'Equipment',
                'Owner',
                'Location',
                'Type',
                'Year Manufactured',
            )
        );

        $equipment = $category->equipment()->with('location', 'subCategory')->get();
        /* @var $equipment Equipment[] */
        foreach ($equipment as $item)
        {
            $data[] = array(
                $item->equipmentid,
                $item->model,
                $item->getOwner(),
                $item->location ? $item->location->locationName : '',
                $item->subCategory ? $item->subCategory->equipSubName : '',
                $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '',
            );
        }

        $config = new ExporterConfig();
        $exporter = new Exporter($config);

        header('Content-type: text/csv');
        $exporter->export('php://output', $data);
        exit;
	}

    public function byUser()
    {
        $users = User::query();

		if ($this->getDept()) {
			$depts = array($this->getDept());
		}
		else {
			$depts = array_keys($this->departments());
		}
		$users->whereHas('departments', function($q) use ($depts)
		{
			$q->whereIn('dept.deptID', $depts + array(0));
		});

        $this->layout->content = View::make('reports.byuser.index', array(
                'users' => $users->get(),
				'departments' => $this->departments(),
            ));
    }

    public function showByUser($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $this->layout = View::make('reports.byuser.show', array(
                'user' => $user,
                'active' => $user->loans()->with('kit', 'equipment', 'kit.equipment')->active()->get(),
            ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function byUserExport($id)
    {
        $user = User::find($id);

        if (!$user)
        {
            App::abort(404);
        }

        $data = array(
            array(
                'Issue Date',
                'Barcode',
                'Equipment',
                'Location',
                'Type',
                'Year Manufactured',
            )
        );

        $active = $user->loans()->with('kit', 'equipment', 'kit.equipment')->active()->get();
        /* @var $equipment Equipment[] */
        foreach ($active as $loan)
        {
            if ($loan->equipmentid && $loan->equipment)
            {
                $item = $loan->equipment;
                $data[] = array(
                    $loan->issue_date ? $loan->issue_date->format(Config::get('formatting.dates.friendly')) : '',
                    $item->equipmentid,
                    $item->model,
                    $item->location ? $item->location->locationName : '',
                    $item->subCategory ? $item->subCategory->equipSubName : '',
                    $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '',
                );
            }
            elseif ($loan->kitid && $loan->kit)
            {
                foreach ($loan->kit->equipment as $item)
                {
                    $data[] = array(
                        $loan->issue_date ? $loan->issue_date->format(Config::get('formatting.dates.friendly')) : '',
                        $item->equipmentid,
                        $item->model,
                        $item->location ? $item->location->locationName : '',
                        $item->subCategory ? $item->subCategory->equipSubName : '',
                        $item->getOriginal('manufactureDate') ? $item->manufactureDate->format('Y') : '',
                    );
                }
            }


        }

        $config = new ExporterConfig();
        $exporter = new Exporter($config);

        header('Content-type: text/csv');
        $exporter->export('php://output', $data);
        exit;
    }

    private function cycleResults($year)
    {
        $equipment = Equipment::query()->with('department');

		if ($this->getDept()) {
        	$depts = array($this->getDept());
		}
		else {
			$depts = array_keys($this->departments());
        }

		$equipment->whereIn('equipments.deptID', $depts)
            ->where('manufactureDate', '>', 0)
            ->orderBy('manufactureDate');

        if ($year == date('Y'))
        {
            $equipment->where( DB::raw('YEAR(DATE_ADD(FROM_UNIXTIME(manufactureDate), INTERVAL expectedLifetime YEAR))'), '<=', $year );
        }
        else
        {
            $equipment->where( DB::raw('YEAR(DATE_ADD(FROM_UNIXTIME(manufactureDate), INTERVAL expectedLifetime YEAR))'), '=', $year );
        }


        return $equipment->get();
    }

    public function cycle()
    {
        $depts   = $this->getDept() ? array($this->getDept()) : array_keys($this->departments());
        //Debugbar::info($depts);

        $years   = DB::table('equipments')
            ->whereIn('deptID', $depts)
            ->where('manufactureDate', '>', 0)
            ->orderBy('y')
            ->select(array(DB::raw('DISTINCT DISTINCT IF(YEAR((DATE_ADD(FROM_UNIXTIME(manufactureDate), INTERVAL expectedLifetime YEAR))) > YEAR(NOW()), YEAR((DATE_ADD(FROM_UNIXTIME(manufactureDate), INTERVAL expectedLifetime YEAR))), YEAR(NOW())) as y')))
            ->lists('y')
        ;
        $results = array();
        $categoriesObject = array(EquipmentCategory::query()->lists('equipCatName', 'equipCatID'));
        $categories = $categoriesObject[0];

        $totalNumberOfItems = 0;

        foreach ($years as $year)
        {
            $results[$year] = $this->cycleResults($year);
            $totalNumberOfItems += count($results[$year]);
        }
         //BEGIN LOAN STUFF
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
                break;
            case 'loans.long':
                $loans->longTerm();
                $pageTitle = 'Long-term Loans';
                break;
            case 'loans.overdue':
                $loans->overdue();
                $pageTitle = 'Overdue Loans';
                break;
            default:
                $loans->active();
        }

        if (Input::get('r')) {
            $loans->where('role', '=', Input::get('r'));
        }

        $loans->whereIn('loans.deptID', $depts);


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
                $loans->join($department->getTable(), $department->getQualifiedKeyName(), '=', $department->loans()->getQualifiedParentKeyName());
                $loans->orderBy('deptName', $direction);
                break;
            default:
                $loans->orderBy('issue_date', $direction);
        }

        //$paginate = (int)Input::get('pp', Loan::PERPAGE);
        // END LOAN STIFF
        $loanInfo = array();

       // Debugbar::info($loans->get());
        $newloans = $loans->get()->toArray();
        //$loans = $loans->paginate(null, array($table.'.*'));

//        $it = $loans->getIterator();
//        $loanArray = array();
//        while($it->valid())
//        {
//            Debugbar::info($it->current());
//            $it->next();
//        }
      //  Debugbar::info(count($newloans));
       // Debugbar::info($newloans);
        //Debugbar::info($loans->getTotal());
       // Debugbar::info($totalNumberOfItems);
        $count = 0;
        foreach ($newloans as $loan) {
            foreach($years as $year) {
                foreach($results[$year] as $item) {
                    if (($loan['equipmentid'] == $item->equipmentid)) {
                        $count++;
                        $loanInfo[$item->equipmentid] = $loan;
                    }
                }
            }
        }


        //Debugbar::info($results);

        foreach($results as $year => $result) {
            foreach($result as $item) {
                //Debugbar::info($item);
                }
        }



        $this->layout->content = View::make('reports.cycle.index', array(
                'results' => $results,
                'categories' => $categories,
				'departments' => $this->departments(),
                'loanInfo' => $loanInfo,
                'loans' => $newloans,
            ));
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function mega()
	{
        $customFromDate = Input::get('fromDate');
        $customToDate = Input::get('toDate');

		$today = Carbon::createFromTime(13, 0, 0); // obvious
		$present  = clone $today; // past week end
		$year  = Carbon::createFromTime(13, 0, 0)->subMonths(3);
        $sixmonths = Carbon::createFromTime(13, 0, 0)->subMonths(6);
        $twelvemonths = Carbon::createFromTime(13, 0, 0)->subMonths(12);
		$month = Carbon::createFromTime(13, 0, 0)->subMonths(1);

		$past = clone $present;
		$past->subDays(7);

        if($customFromDate && $customToDate) {
            $customFromDate = Carbon::createFromFormat('m/d/Y', $customFromDate);
            $customToDate = Carbon::createFromFormat('m/d/Y', $customToDate);

            $this->layout->content = View::make('reports.mega.index', array(
                'year' => $this->megaQuery($year, $today) + array('format' => 'year'),
                'month' => $this->megaQuery($month, $today) + array('format' => 'month'),
                'week' => $this->megaQuery($past, $present) + array('format' => 'week'),
                'sixmonths' => $this->megaQuery($sixmonths, $present) + array('format' => 'sixmonths'),
                'twelvemonths' => $this->megaQuery($twelvemonths, $present) + array('format' => 'twelvemonths'),
                'custom' => $this->megaQuery($customFromDate, $customToDate) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => $customFromDate->format('m/d/Y'),
                'customToDate' => $customToDate->format('m/d/Y'),
                'customDateIsSet' => true,
            ));
        } else {

            $this->layout->content = View::make('reports.mega.index', array(
                'year' => $this->megaQuery($year, $today) + array('format' => 'year'),
                'month' => $this->megaQuery($month, $today) + array('format' => 'month'),
                'week' => $this->megaQuery($past, $present) + array('format' => 'week'),
                'sixmonths' => $this->megaQuery($sixmonths, $present) + array('format' => 'sixmonths'),
                'twelvemonths' => $this->megaQuery($twelvemonths, $present) + array('format' => 'twelvemonths'),
                'custom' => $this->megaQuery($present, $present) + array('format' => 'custom'),
                'departments' => $this->departments(),
                'customFromDate' => '',
                'customToDate' => '',
                'customDateIsSet' => false,
            ));
        }
	}


}
