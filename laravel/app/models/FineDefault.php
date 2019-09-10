<?php

/**
 * FineDefault
 *
 * @property boolean $deptID
 * @property integer $defaultFine
 * @property integer $id
 * @property-read \Department $department
 * @method static \Illuminate\Database\Query\Builder|\FineDefault whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\FineDefault whereDefaultFine($value) 
 * @method static \Illuminate\Database\Query\Builder|\FineDefault whereId($value) 
 */
class FineDefault extends Loaner\Model{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'fineDefault';
	
	public $timestamps = false;

    public function department()
    {
        return $this->belongsTo('Department');
    }

}
