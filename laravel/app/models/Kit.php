<?php

/**
 * Kit
 *
 * @property string $kitid
 * @property boolean $deptID
 * @property string $kit_desc
 * @property integer $loan_length
 * @property \Carbon\Carbon $created_on
 * @property \Carbon\Carbon $updated_on
 * @property string $notes
 * @property boolean $status
 * @property-read \Department $department
 * @property-read \Illuminate\Database\Eloquent\Collection|\Loan[] $loans
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\InactiveEquipment[] $inactiveEuipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\AccessArea[] $accessAreas
 * @method static \Illuminate\Database\Query\Builder|\Kit whereKitid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereKitDesc($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereLoanLength($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereCreatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereUpdatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereNotes($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kit whereStatus($value) 
 * @method static \Kit whereActive() 
 * @method static \Kit whereInActive() 
 */
class Kit extends Loaner\Model implements \Loaner\EquipmentInterface {

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
	protected $table = 'kits';

	protected $primaryKey = 'kitid';

    public $incrementing = false;
    protected $fillable = array('status');

    public static $rules = array(
        'kitid' => 'required|unique:kits',
        'deptID' => 'required|exists:dept',
        'loan_length' => 'required|integer|min:1',
        'kit_desc' => 'required',
    );

    public function getForeignKey()
    {
        return 'kitid';
    }

	public function department()
	{
		return $this->belongsTo('Department', 'deptID');
	}
	
	public function loans()
	{
		return $this->hasMany('Loan');
	}

	public function equipment()
	{
		return $this->hasMany('Equipment');
	}

	public function inactiveEuipment()
	{
		return $this->hasMany('InactiveEquipment');
	}
	
	public function accessAreas()
	{
		return $this->belongsToMany('AccessArea','kits_accessareas', 'kitid', 'accessid');
	}

    public function scopeWhereActive($query)
    {
        return $query->where('status', '<>', self::STATUS_DEACTIVATED);
    }

    public function scopeWhereInActive($query)
    {
        return $query->where('status', '=', self::STATUS_DEACTIVATED);
    }

    /**
     * Status text
     * @return string
     */
    public function friendlyStatus()
    {
        return $this->status == 1 ? 'Available' : 'On Loan';
    }

    public function inactive()
    {
        return $this->status == static::STATUS_DEACTIVATED;
    }

    /**
     * Suggest an maximum id based on the previous
     * @param $dept null|int
     * @return int
     */
    public function suggestId($dept = null)
    {
        $query = DB::table($this->table)->select( DB::raw('MAX(CAST(kitid AS UNSIGNED)) as m') );

        if ($dept)
        {
            $query->where('deptID', '=', $dept);
        }

        $max = (string) $query->first()->m;

        return bcadd($max, "1");
    }

    public function deactivate()
    {
        foreach ($this->equipment as $item)
        {
            $item->deactivate();
        }

        $this->status = Kit::STATUS_DEACTIVATED;
        $this->updateUniques();
    }

    public function activate()
    {
        foreach ($this->inactiveEuipment as $item)
        {
            $item->activate();
        }

        $this->timestamps = false;
        $this->status = Kit::STATUS_ACTIVE;
        $this->updateUniques();
    }

	/**
	 * @return array
	 */
	public function toJsonLoan()
	{
		$results = array();

		foreach ($this->equipment as $item)
		{
			$results[] = $item->toJsonLoan();
		}

		return array(
			'length' => $this->loan_length,
			'results' => $results,
		);
	}

    /**
     * Check status
     */
    public function checkStatus()
    {
        if ($this->loans()->active()->count())
        {
            $this->status = static::STATUS_LOANED;
        }
        else
        {
            $this->status = static::STATUS_ACTIVE;
        }

        foreach ($this->equipment as $item)
        {
            $item->checkStatus();
        }

        $this->forceSave();
    }
}
