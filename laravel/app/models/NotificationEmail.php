<?php

/**
 * NotificationEmail
 *
 * @property string $emailid
 * @property integer $id
 * @method static \Illuminate\Database\Query\Builder|\NotificationEmail whereEmailid($value) 
 * @method static \Illuminate\Database\Query\Builder|\NotificationEmail whereId($value) 
 */
class NotificationEmail extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'notificationEmails';
	public $timestamps = false;

}
