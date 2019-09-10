<?php

/**
 * Fine
 *
 * @property string $userid
 * @property integer $amount
 * @property string $accepted_by
 * @property boolean $deptID
 * @property integer $timestamp
 * @property integer $id
 * @property-read \Department $department
 * @property-read \User $user
 * @method static \Illuminate\Database\Query\Builder|\Fine whereUserid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Fine whereAmount($value) 
 * @method static \Illuminate\Database\Query\Builder|\Fine whereAcceptedBy($value) 
 * @method static \Illuminate\Database\Query\Builder|\Fine whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Fine whereTimestamp($value) 
 * @method static \Illuminate\Database\Query\Builder|\Fine whereId($value) 
 */
class Fine extends Loaner\Model {

	/**
	 * The name of the "created at" column.
	 *
	 * @var string
	 */
	const CREATED_AT = 'created_on';

	/**
	 * The name of the "updated at" column.
	 *
	 * @var string
	 */
	const UPDATED_AT = 'updated_on';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'fines';

	public function department()
	{
		return $this->belongsTo('Department');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}
	
}
