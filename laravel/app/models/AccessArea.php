<?php

/**
 * AccessArea
 *
 * @property boolean $id
 * @property string $accessarea
 * @property boolean $deptID
 * @property-read \Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\Kit[] $kits
 * @method static \Illuminate\Database\Query\Builder|\AccessArea whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AccessArea whereAccessarea($value) 
 * @method static \Illuminate\Database\Query\Builder|\AccessArea whereDeptID($value) 
 */
class AccessArea extends Loaner\Model {

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'accessareas';

	protected $primaryKey = 'id';

	public $timestamps = false;
	
	public function department()
	{
		return $this->belongsTo('Department');
	}
	
	public function equipment()
	{
	   return $this->belongsToMany('Equipment','equipments_accessareas', 'accessid');
	}	

	public function users()
	{
		return $this->belongsToMany('User','users_accessareas', 'accessid', 'userNum');
	}
	
	public function kits()
	{
		return $this->belongsToMany('Kit','kits_accessareas', 'accessid');
	}
}
