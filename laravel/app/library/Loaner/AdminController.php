<?php

namespace Loaner;

use \BaseController;
use \Menu;
use \Auth;
use \Session;
use \Department;
use \Response;
use \User;
use \App;

class AdminController extends BaseController
{
	
	const REMEMBER = 20;

    /**
     * @var string
     */
    public $layout = 'layouts/main';
    /**
     * @var int
     */
    public $deptID;
    /**
     * @var Department
     */
    public $dept;

    public function __construct()
    {
        $user = Auth::getUser();
        $dept = Department::find(Session::get('deptID', $user->departments()->remember(static::REMEMBER)->first()->deptID));

        if (!Session::get('deptID')) {
			$dept = Department::find(Session::get('deptID', $user->departments()->remember(static::REMEMBER)->first()->deptID));
            Session::set('deptID', $dept->deptID);
        }

        $this->dept   = $dept;
        $this->deptID = $dept->deptID;

        \View::share('deptID', $this->deptID);

		if (App::environment('local'))
		{
			$this->beforeFilter(function()
			{
				\Event::fire('clockwork.controller.start');
			});

			$this->afterFilter(function()
			{
				\Event::fire('clockwork.controller.end');
			});
		}


    }

    /**
     * Setup menu and other layout
     */
    public function setupLayout()
    {
        if ($this->layout == 'layouts/main') {
            Menu::setOption('item.active_child_class', 'active');

            $menu = Menu::handler('main', array('class' => 'nav navbar-nav'));
            $dept = $this->dept;

            if (is_role(User::ROLE_SYSTEM_ADMIN)) {
                $departments = Department::query()->where('dept.deptID', '>', 0)->where('deptID', '<>', $dept->deptID)->orderBy('deptName')->remember(static::REMEMBER)->get();
            } else {
                $departments = Auth::getUser()->departments()->where('dept.deptID', '>', 0)->where('dept.deptID', '<>', $dept->deptID)->orderBy('deptName')->remember(static::REMEMBER)->get();
            }

            if ($departments->count() == 0) {
                $menu->add(route('home'), $dept->deptName);
            } else {
                $deptMenu = Menu::items('departments', array(''));

                foreach ($departments as $d) {
                    $deptMenu->add('javascript:;', $d->deptName, null, array('data-dept' => $d->deptID));
                }

                $menu->raw(
                    sprintf(
                        '<div class="brand dropdown-toggle" data-toggle="dropdown" id="current-visible">%s<b class="caret"></b></div>',
                        $dept->deptName
                    ),
                    $deptMenu,
                    array('class' => 'dropdown')
                );

                $menu->add(route('home'), 'Dashboard');
            }

            if (is_role(array(User::ROLE_WORK_STUDY, User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN))) {
                $menu->add(route('users.index'), 'Users')->activePattern('\/users\/(.*)');
                $menu->add(route('kits.index'), 'Kits')->activePattern('\/kits\/(.*)');
                $menu->add(route('equipment.index'), 'Equipment')->activePattern('\/equipment\/(.*)');
                $menu->add(route('loans.index'), 'Loans')->activePattern('\/loans\/(.*)');
                $menu->add(route('reservations.index'), 'Reservations')->activePattern('\/reservations\/(.*)');
                $menu->add(route('system'), 'System Options');
            }
            if (is_role(array(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN))) {
                $menu->add('#', 'Reports',
                    Menu::items()
						->add( route('reports.mega'), 'All Activity')
                        ->add( route('reports.index'), 'Loan Activity (Entire System)' )
                        ->add( route('reports.users'), 'Loan Activity (By User)' )
                        //->add( route('reports.byuser'), 'Equipment (By User)' )
                        ->add( route('reports.categories'), 'Equipment (By Category)' )
                        ->add( route('reports.cycle'), 'Replacement Cycle' )
                        ->add( route('reports.deactivation'), 'Deactivation Report' )
                )->activePattern('\/reports\/(.*)');
            }
            if (is_role(User::ROLE_ADMINISTRATOR, User::ROLE_SYSTEM_ADMIN, User::ROLE_CASHIER)) {
                $menu->add(route('fines.index'), 'Fines');
            }

            
            Menu::handler('main')->getItemsAtDepth(0)->map(
                function ($item) {
                    if ($item->hasChildren()) {
                        $item->addClass('dropdown');

                        $item->getChildren()
                            ->addClass('dropdown-menu');

                        $item->getContent()
                            ->addClass('dropdown-toggle')
                            ->dataToggle('dropdown')
                            ->nest(' <b class="caret"></b>');
                    }
                }
            );
        }


        parent::setupLayout();
    }

    /**
     * @param array $results
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function resultResponse(array $results, $status = 200)
    {
        return Response::json(array(
                'status' => $status,
                'count' => count($results),
                'results' => $results,
                'error' => false,
            ));
    }

    /**
     * @param array $data
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function dataResponse(array $data, $status = 200)
    {
        return Response::json(array(
                'status' => $status,
                'data' => $data,
                'error' => false,
            ));
    }

    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($message = 'OK', $status = 200)
    {
        return Response::json(array(
                'status' => $status,
                'message' => $message,
                'error' => false,
        ));
    }

    /**
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(array $errors = null, $status = 400)
    {
        return Response::json(array(
                'status' => $status,
                'message' => join("\n", $errors),
                'error' => true,
                'errors' => $errors
            ));
    }

    /**
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notFoundResponse($status = 404)
    {
        return $this->errorResponse(array('The requested resource was not found.'), null, $status);
    }

    /**
     * @param array $parameters
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function missingMethod($parameters = array())
    {
        return $this->notFoundResponse();
    }

}
