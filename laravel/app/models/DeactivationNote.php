<?php

/**
 * DeactivationNote
 *
 * @property string $kitid
 * @property string $equipmentid
 * @property string $name
 * @property string $userid
 * @property string $disposalType
 * @property string $deactivationNotes
 * @property boolean $deptID
 * @property \Carbon\Carbon $deactivate_date
 * @property-read \Kit $kit
 * @property-read \InactiveEquipment $equipment
 * @property-read \Department $department
 * @property-read \User $user
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereKitid($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereEquipmentid($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereUserid($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereDisposalType($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereDeactivationNotes($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\DeactivationNote whereDeactivateDate($value) 
 */
class DeactivationNote extends Loaner\Model{

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'deactivationNotes';

	public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    public $fillable = array('deactivationNotes', 'disposalType');

    public static $rules = array(
        'kitid' => 'exists:kits',
        'deptID' => 'exists:dept',
        'equipmentid' => 'exists:equipments',
        'name' => 'required',
        'disposalType' => 'required',
        'deactivationNotes' => 'required',
    );

    protected $dates = array('deactivate_date');

    public static $customMessages = array(
        'deactivationNotes.required' => 'Disposal details are required'
    );

    public static function boot()
    {
        parent::boot();

        // Set date to current time when saving
        DeactivationNote::creating(function($note) {
                if (!$note->deactivate_date) {
                    $note->deactivate_date = time();
                }
            });
    }

    public function kit()
    {
        return $this->belongsTo('Kit', 'kitid');
    }

    public function equipment()
    {
        return $this->belongsTo('InactiveEquipment', 'equipmentid');
    }

    public function department()
    {
        return $this->belongsTo('Department', 'deptID');
    }

    public function user()
    {
        return $this->belongsTo('User', 'userid', 'userid');
    }
}
