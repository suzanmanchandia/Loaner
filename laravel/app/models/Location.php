<?php

/**
 * Location
 *
 * @property boolean $locationID
 * @property string $locationName
 * @property boolean $deptID
 * @property-read \Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipments
 * @property-read \Illuminate\Database\Eloquent\Collection|\InactiveEquipment[] $inactiveEquipments
 * @method static \Illuminate\Database\Query\Builder|\Location whereLocationID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Location whereLocationName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Location whereDeptID($value) 
 */
class Location extends Loaner\Model  {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'locations';

	protected $primaryKey = 'locationID';

	public $timestamps = false;


	public function department()
	{
	    return $this->belongsTo('Department');
	}
	
	public function equipments()
	{
	    return $this->hasMany('Equipment');
	}
	
	public function inactiveEquipments()
	{
		return $this->hasMany('InactiveEquipment');
	}
}
