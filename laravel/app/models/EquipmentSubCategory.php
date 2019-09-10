<?php

/**
 * EquipmentSubCategory
 *
 * @property boolean $equipSubCatID
 * @property boolean $equipCatID
 * @property string $equipSubName
 * @property-read \EquipmentCategory $equipmentCategory
 * @property-read \Illuminate\Database\Eloquent\Collection|\Equipment[] $equipment
 * @method static \Illuminate\Database\Query\Builder|\EquipmentSubCategory whereEquipSubCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\EquipmentSubCategory whereEquipCatID($value) 
 * @method static \Illuminate\Database\Query\Builder|\EquipmentSubCategory whereEquipSubName($value) 
 */
class EquipmentSubCategory extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'equipSubCategory';

	protected $primaryKey = 'equipSubCatID';

    public $timestamps = false;

	
	public function equipmentCategory()
	{
		return $this->belongsTo('EquipmentCategory');
	}
	
	public function equipment()
	{
	    return $this->hasMany('Equipment');
	}
	
	
	
}
