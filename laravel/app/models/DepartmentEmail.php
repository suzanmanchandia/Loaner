<?php

/**
 * Email
 *
 * @property int $id
 * @property int $deptID
 * @property string $email
 * @property-read \Department $department
 * @method static \Illuminate\Database\Query\Builder|\DepartmentEmail whereDeptID($value)
 * @method static \Illuminate\Database\Query\Builder|\DepartmentEmail whereEmail($value)
 */
class DepartmentEmail extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'department_emails';

	public $timestamps = false;
	public $fillable = array('email');

	public static $rules = array(
		'deptID' => 'exists:dept',
	);

	protected $dates = array('deactivate_date');

	public static $customMessages = array(
		'deactivationNotes.required' => 'Disposal details are required'
	);

	public function department()
	{
		return $this->belongsTo('Department', 'deptID');
	}
}
