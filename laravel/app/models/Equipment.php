<?php

/**
 * Equipment
 *
 * @property string $equipmentid
 * @property string $kitid
 * @property boolean $deptID
 * @property string $equipment_desc
 * @property string $model
 * @property boolean $condID
 * @property string $notes
 * @property integer $loan_lengthEQ
 * @property string $createdBy
 * @property \Carbon\Carbon $created_on
 * @property \Carbon\Carbon $updated_on
 * @property boolean $status
 * @property string $manufacturer
 * @property string $manufSerialNum
 * @property \Carbon\Carbon $manufactureDate
 * @property boolean $expectedLifetime
 * @property \Carbon\Carbon $expirationDate
 * @property boolean $equipCatID
 * @property boolean $equipSubCatID
 * @property boolean $locationID
 * @property \Carbon\Carbon $purchaseDate
 * @property float $purchasePrice
 * @property string $ipAddress
 * @property string $macAddress
 * @property string $hostName
 * @property string $connectType
 * @property string $warrantyInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\Loan[] $loans
 * @property-read \Illuminate\Database\Eloquent\Collection|\AccessArea[] $accessAreas
 * @property-read \Kit $kit
 * @property-read \Department $department
 * @property-read \Condition $condition
 * @property-read \EquipmentCategory $category
 * @property-read \EquipmentSubCategory $subCategory
 * @property-read \Location $location
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereEquipmentid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereKitid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereEquipmentDesc($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereModel($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereCondID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereNotes($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereLoanLengthEQ($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereCreatedBy($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereCreatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereUpdatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereStatus($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereManufacturer($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereManufSerialNum($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereManufactureDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereExpectedLifetime($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereExpirationDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereEquipCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereEquipSubCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereLocationID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment wherePurchaseDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment wherePurchasePrice($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereIpAddress($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereMacAddress($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereHostName($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereConnectType($value) 
 * @method static \Illuminate\Database\Query\Builder|\Equipment whereWarrantyInfo($value) 
 */
class Equipment extends InactiveEquipment implements \Loaner\EquipmentInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'equipments';

    public static $rules = array(
        'equipmentid' => 'required|unique:equipments',
        //'equipment_desc' => 'required',
        'kitid' => 'exists:kits',
        'loan_lengthEQ' => 'required|integer|min:0',
        'deptID' => 'required|exists:dept',
        'equipCatID' => 'required|exists:equipCategory',
        //'equipSubCatID' => 'required|exists:equipSubCategory',
    );

    public function getForeignKey()
    {
        return 'equipmentid';
    }

    public function loans()
    {
        return $this->hasMany('Loan');
    }
	
    public function accessAreas()
	{
	   return $this->belongsToMany('AccessArea','equipments_accessareas', 'equipmentid', 'accessid');
	}

    /**
     * Suggest an maximum id based on the previous
     * @return int
     */
    public function suggestId($dept = null)
    {
        $query = DB::table($this->table)->select( DB::raw('MAX(CAST(equipmentid AS UNSIGNED)) as m') );

        if ($dept)
        {
            $query->where('deptID', '=', $dept);
        }

        $max = (string) $query->first()->m;

        return bcadd($max, "1");
    }

    public function getOwner()
    {
        $loan = Loan::query();

        $loan->active();

        $that = $this;

        $loan = $loan->where(function($query) use ($that) {
                $query->orWhere('equipmentid', '=', $that->equipmentid);
                if ($that->kitid)
                {
                    $query->orWhere('kitid', '=', $that->kitid);
                }
            })->first();

        if (!$loan)
        {
            return '';
        }

        return sprintf('%s %s (%s)', $loan->user->fname, $loan->user->lname, $loan->userid);
    }

    /**
     * Json result for loan display
     * @return array
     */
    public function toJsonLoan()
    {
        switch ($this->condition->condName)
        {
            case 'Damaged':
                $class = 'warning';
                break;
            case 'Missing':
                $class = 'danger';
                break;
            default:
                $class = 'info';
                break;
        }

        return array(
            'id' => $this->equipmentid,
            'model' => $this->model,
            'condition' => $this->condition->condName,
            'class' => $class,
			'loan_length' => $this->loan_lengthEQ
        );
    }

    /**
     * DEactivate
     * @return bool|null|void
     * @throws Exception
     */
    public function deactivate()
    {
        Eloquent::unguard();

        $new = new InactiveEquipment();
        $new->timestamps = false;

		DB::beginTransaction();

		try
		{
			$new->fill($this->getArrayableAttributes());
			$new->{static::UPDATED_AT} = time();
			$new->save();

			$this->delete();

			DB::commit();

			return $this;
		}
		catch (Exception $ex)
		{
			DB::rollBack();
			throw $ex;
		}

    }

    /**
     * Check status
     */
    public function checkStatus()
    {
        if ($this->loans()->active()->count()) {
            $this->status = static::STATUS_LOANED;
        }
        elseif ($this->kitid && $this->kit && $this->kit->loans()->active()->count()) {
            $this->status = static::STATUS_LOANED;
        }
        else {
            $this->status = static::STATUS_ACTIVE;
        }

        $this->forceSave();
    }


}
