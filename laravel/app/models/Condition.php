<?php

/**
 * Condition
 *
 * @property boolean $condID
 * @property string $condName
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipments
 * @property-read \Illuminate\Database\Eloquent\Collection|\InactiveEquipment[] $inactiveEquipments
 * @method static \Illuminate\Database\Query\Builder|\Condition whereCondID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Condition whereCondName($value) 
 */
class Condition extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'conditions';

	protected $primaryKey = 'condID';

	public $timestamps = false;

	public function equipments()
	{
		return $this->hasMany('Equipment');
	}
	
	public function inactiveEquipments()
	{
		return $this->hasMany('InactiveEquipment');
	}
	
}
