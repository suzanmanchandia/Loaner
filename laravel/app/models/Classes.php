<?php

/**
 * Classes
 *
 * @property integer $id
 * @property string $classname
 * @property boolean $deptID
 * @property-read \Department $department
 * @method static \Illuminate\Database\Query\Builder|\Classes whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Classes whereClassname($value) 
 * @method static \Illuminate\Database\Query\Builder|\Classes whereDeptID($value) 
 */
class Classes extends Loaner\Model {

		/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'classes';

	public $timestamps = false;
	
	public function department()
	{
		return $this->belongsTo('Department');
	}
}
