<?php

/**
 * InactiveEquipment
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
 * @property-read \Kit $kit
 * @property-read \Department $department
 * @property-read \Condition $condition
 * @property-read \EquipmentCategory $category
 * @property-read \EquipmentSubCategory $subCategory
 * @property-read \Location $location
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereEquipmentid($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereKitid($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereEquipmentDesc($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereModel($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereCondID($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereNotes($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereLoanLengthEQ($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereCreatedBy($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereCreatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereUpdatedOn($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereStatus($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereManufacturer($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereManufSerialNum($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereManufactureDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereExpectedLifetime($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereExpirationDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereEquipCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereEquipSubCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereLocationID($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment wherePurchaseDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment wherePurchasePrice($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereIpAddress($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereMacAddress($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereHostName($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereConnectType($value) 
 * @method static \Illuminate\Database\Query\Builder|\InactiveEquipment whereWarrantyInfo($value) 
 */
class InactiveEquipment extends Loaner\Model implements \Loaner\EquipmentInterface {

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
	protected $table = 'inactiveEquipment';

	protected $primaryKey = 'equipmentid';

    public $incrementing = false;

    protected $dates = array('expirationDate', 'manufactureDate', 'purchaseDate');
    protected $fillable = array('status');

    public function kit()
    {
        return $this->belongsTo('Kit', 'kitid');
    }

    public function department()
    {
        return $this->belongsTo('Department', 'deptID');
    }

    public function condition()
    {
        return $this->belongsTo('Condition', 'condID');
    }

    public function category()
    {
        return $this->belongsTo('EquipmentCategory', 'equipCatID');
    }

    public function subCategory()
    {
        return $this->belongsTo('EquipmentSubCategory', 'equipSubCatID');
    }

    public function location()
    {
        return $this->belongsTo('Location', 'locationID');
    }

    /**
     * Status text
     * @return string
     */
    public function friendlyStatus()
    {
        return $this->status == static::STATUS_ACTIVE ? 'Available' : 'On Loan';
    }

	public function activate()
	{
		$new = new Equipment();

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

    public function checkStatus()
    {
        // TODO: Implement checkStatus() method.
    }
}
