<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * User
 *
 * @property integer $userNum
 * @property string $userid
 * @property string $usc_id
 * @property string $password
 * @property string $fname
 * @property string $lname
 * @property string $email
 * @property string $phone
 * @property string $city
 * @property string $address
 * @property string $state
 * @property string $zip
 * @property boolean $level
 * @property string $class
 * @property boolean $status
 * @property boolean $suspended
 * @property boolean $role
 * @property string $notes
 * @property \Carbon\Carbon $created_on
 * @property \Carbon\Carbon $updated_on
 * @property integer $last_login
 * @property integer $fine
 * @property string $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection|\Loan[] $loans
 * @property-read \Illuminate\Database\Eloquent\Collection|\Department[] $departments
 * @property-read \Illuminate\Database\Eloquent\Collection|\AccessArea[] $accessAreas
 * @property-read \Illuminate\Database\Eloquent\Collection|\Fine[] $fines
 * @property-read \Illuminate\Database\Eloquent\Collection|\FinePayment[] $finePayments
 * @method static \Illuminate\Database\Query\Builder|\User whereUserNum($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereUserid($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereUscId($value) 
 * @method static \Illuminate\Database\Query\Builder|\User wherePassword($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereFname($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereLname($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereEmail($value) 
 * @method static \Illuminate\Database\Query\Builder|\User wherePhone($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereCity($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereAddress($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereState($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereZip($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereLevel($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereClass($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereStatus($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereSuspended($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereRole($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereNotes($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereCreatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereUpdatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereLastLogin($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereFine($value) 
 * @method static \Illuminate\Database\Query\Builder|\User whereRememberToken($value) 
 * @method static \User whereActive() 
 */
class User extends Loaner\Model implements UserInterface, RemindableInterface {

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
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

    protected $primaryKey = 'userNum';

    public $incrementing = false;

    const ROLE_STUDENT = 1;
    const ROLE_WORK_STUDY  = 2;
    const ROLE_ADMINISTRATOR = 3;
    const ROLE_FACULTY = 4;
    const ROLE_SYSTEM_ADMIN = 5;
    const ROLE_CASHIER = 6;

    const STATUS_UNLOCKED = 1;
    const STATUS_LOCKED = 2;
    const STATUS_DEACTIVATED = 3;

    public static $rules = array(
        'userid' => 'required|unique:users',
        'password' => 'required',
        'fname' => 'required',
        'lname' => 'required',
        'email' => 'required|email|unique:users',
    );

    public static $customMessages = array(
        'userid.unique' => 'The username has already been taken.',
    );

    public function getForeignKey()
    {
        return $this->primaryKey;
    }

    protected function getDateFormat()
    {
        return 'U';
    }

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function loans()
	{
		return $this->hasMany('Loan');
	}

    public function departments()
    {
        return $this->belongsToMany('Department', 'departments_users');
    }
	
	public function accessAreas()
	{
		return $this->belongsToMany('AccessArea','users_accessareas', 'userNum', 'accessid');
	}
	
	public function fines()
	{
		return $this->hasMany('Fine');
	}
	
	public function finePayments()
	{
		return $this->hasMany('FinePayment');
	}

    /**
     * @return string
     */
    public function getRole()
    {
        return array_get(Config::get('dropdowns.user_roles'), $this->role);
    }

    public function getStatus()
    {
        return array_get( Config::get('dropdowns.user_statuses'), $this->status );
    }

    /**
     * Calculate and update fines
     */
    public function calculateFine()
    {
        $fines = $this->loans()->sum('fine');
        $payments = $this->finePayments()->sum('amount');

        $this->fine = $fines - $payments;

        // Revoke suspension on completed fines
        if ($this->fine <= 0 && $this->suspended)
        {
            $this->suspended = 0;
        }

        $this->forceSave();
    }

    public function scopeWhereActive($query)
    {
        $that = $this;
        return $query->where(function($inner) use ($that) {
                $inner->where($that->table.'.suspended', '=', 0);
                $inner->where($that->table.'.status', '<>', User::STATUS_LOCKED);
            });
    }

    public function scopeSuspended($query, $role = User::ROLE_STUDENT)
    {
        $that = $this;
        return $query->where(function($inner) use ($that, $role) {
                $inner->where($that->table.'.suspended', '=', 1);
                $inner->where($that->table.'.role', '=', $role);
            });
    }

    public function scopeUnsuspended($query, $role = User::ROLE_STUDENT)
    {
        $that = $this;
        return $query->where(function($inner) use ($that, $role) {
                $inner->where($that->table.'.suspended', '=', 0);
                $inner->where($that->table.'.role', '=', $role);
            });
    }
}
