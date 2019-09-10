<?php

/**
 * EquipmentCategory
 *
 * @property boolean $equipCatID
 * @property string $equipCatName
 * @property-read \Illuminate\Database\Eloquent\Collection|\EquipmentSubCategory[] $subCategories
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\InactiveEquipment[] $inactiveEquipment
 * @method static \Illuminate\Database\Query\Builder|\EquipmentCategory whereEquipCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\EquipmentCategory whereEquipCatName($value) 
 */
class EquipmentCategory extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'equipCategory';

	protected $primaryKey = 'equipCatID';
	
	public $timestamps = false;

    public function subCategories()
    {
        return $this->hasMany('EquipmentSubCategory', $this->primaryKey);
    }
    
    public function equipment()
    {
    	return $this->hasMany('Equipment', $this->primaryKey);
    }
    
    public function inactiveEquipment()
    {
    	return $this->hasMany('InactiveEquipment');
    }

}
