<?php

/**
 * FinePayment
 *
 * @property string $userid
 * @property integer $userNum
 * @property integer $amount
 * @property string $accepted_by
 * @property boolean $deptID
 * @property \Carbon\Carbon $timestamp
 * @property-read \Department $department
 * @property-read \User $user
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereUserid($value) 
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereUserNum($value) 
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereAmount($value) 
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereAcceptedBy($value) 
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\FinePayment whereTimestamp($value) 
 */
class FinePayment extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'finePayments';
	public $timestamps = false;

    protected $dates = array('timestamp');
	
	public function department()
	{
		return $this->belongsTo('Department');
	}
	
	public function user()
	{
		return $this->belongsTo('User');
	}

}
