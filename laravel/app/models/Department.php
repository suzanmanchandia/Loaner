<?php

/**
 * Department
 *
 * @property boolean $deptID
 * @property string $deptName
 * @property string $lastIDcreated
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\DepartmentEmail[] $emails
 * @property-read \Illuminate\Database\Eloquent\Collection|\InactiveEquipment[] $inactiveEquipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\Classes[] $classes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Kit[] $kits
 * @property-read \Illuminate\Database\Eloquent\Collection|\Location[] $locations
 * @property-read \Illuminate\Database\Eloquent\Collection|\Loan[] $loans
 * @property-read \Illuminate\Database\Eloquent\Collection|\User[] $oldUsers
 * @property-read \Illuminate\Database\Eloquent\Collection|\User[] $users
 * @property-read \FineDefault $fineDefault
 * @property-read \Illuminate\Database\Eloquent\Collection|\FinePayment[] $finePayments
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fine[] $fines
 * @property-read \Illuminate\Database\Eloquent\Collection|\AccessArea[] $accesssAreas
 * @method static \Illuminate\Database\Query\Builder|\Department whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Department whereDeptName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Department whereLastIDcreated($value) 
 */
class Department extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'dept';

	protected $primaryKey = 'deptID';

	public $timestamps = false;

    public function getForeignKey()
    {
        return $this->primaryKey;
    }
	
	public function equipment()
	{
	    return $this->hasMany('Equipment');
	}
	
	public function inactiveEquipment()
	{
		return $this->hasMany('InactiveEquipment');
	}
	
	public function classes()
	{
		return $this->hasMany('Classes');
	}
	
	public function kits()
	{
	    return $this->hasMany('Kit');
	}
	
	public function locations()
	{
		return $this->hasMany('Location');
	}
	
	public function loans()
	{
		return $this->hasMany('Loan');
	}
	
	public function oldUsers()
	{
		return $this->hasMany('User');
	}

    public function users()
    {
        return $this->belongsToMany('User', 'departments_users');
    }
	
	public function fineDefault()
	{
		return $this->hasOne('FineDefault', $this->primaryKey);
	}

	public function finePayments()
	{
		return $this->hasMany('FinePayment');
	}
	
	public function fines()
	{
		return $this->hasMany('Fine');
	}
	
	public function accesssAreas()
	{
		return $this->hasMany('AccessArea');
	}

	public function emails()
	{
		return $this->hasMany('DepartmentEmail');
	}
}

