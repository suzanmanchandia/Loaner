<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DepartmentCombine extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::transaction(function() {

                $combinations = array(
                    6 => array(
                        'IFT Equipment Cage',
                        array(9),
                    ),
                    2 => array(
                        'HAR Equipment Cage',
                        array(10, 12),
                    ),
                );

                foreach ($combinations as $dept => $combination)
                {
                    list($name, $items) = $combination;

                    $groups = array();
                    $areas  = AccessArea::query()->whereIn('deptID', array_merge($items, array($dept)) )->get();
                    foreach ($areas as $area)
                    {
                        $groups[$area->accessarea][] = $area;
                    }

                    foreach ($groups as $group)
                    {
                        if (count($group) < 2)
                        {
                            continue;
                        }

                        $top = array_pop($group);

                        foreach ($group as $area)
                        {
                            foreach ($area->users as $user)
                            {
                                /* @var $user User */
                                $user->accessAreas()->detach($area->id);
                                try
                                {
                                    $user->accessAreas()->attach(array($top->id), array('userid' => $user->userid));
                                }
                                catch (Exception $ex)
                                {
                                    // fail silently
                                }
                            }
                            foreach ($area->equipment as $item)
                            {
                                /* @var $item Equipment */
                                $item->accessAreas()->detach($area->id);
                                $item->accessAreas()->sync(array($top->id), false);
                            }

                            $area->delete();
                        }
                    }

                    DB::table('accessareas')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('classes')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('deactivationNotes')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('equipments')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('fineDefault')->whereIn('deptID', $items)->delete();
                    DB::table('finePayments')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('fines')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('inactiveEquipment')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('kits')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('loans')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('locations')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('departments_users')->whereIn('deptID', $items)->update(array('deptID' => $dept));
                    DB::table('dept')->whereIn('deptID', $items)->delete();
                    DB::table('dept')->where('deptID', '=', $dept)->update(array('deptName' => $name));
                }

                if (!DB::table('dept')->where('deptName', '=', 'The Garage')->count())
                {
                    DB::table('dept')->insert(array(
                            'deptName' => 'The Garage',
                        ));
                }

                print_r(DB::getQueryLog());

            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	}

}
