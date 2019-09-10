<?php

/**
 * FineDefaultRecord
 *
 * @property integer $defaultFineRecordID
 * @property string $notes
 * @method static \Illuminate\Database\Query\Builder|\FineDefaultRecord whereDefaultFineRecordID($value) 
 * @method static \Illuminate\Database\Query\Builder|\FineDefaultRecord whereNotes($value) 
 */
class FineDefaultRecord extends Loaner\Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'fineDefaultRecord';

	protected $primaryKey = 'defaultFineRecordID';

	public $timestamps = false;



}
