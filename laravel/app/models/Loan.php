<?php

use Carbon\Carbon;

/**
 * Loan
 *
 * @property integer $lid
 * @property string $equipmentid
 * @property string $kitid
 * @property string $userid
 * @property integer $userNum
 * @property boolean $deptID
 * @property string $issuedBy
 * @property \Carbon\Carbon $issue_date
 * @property integer $loan_length
 * @property \Carbon\Carbon $due_date
 * @property \Carbon\Carbon $return_date
 * @property boolean $renew_count
 * @property integer $fine
 * @property \Carbon\Carbon $fine_paid_date
 * @property boolean $status
 * @property string $notes
 * @property-read \Kit $kit
 * @property-read \Equipment $equipment
 * @property-read \Department $department
 * @property-read \User $user
 * @method static \Illuminate\Database\Query\Builder|\Loan whereLid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereEquipmentid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereKitid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereUserid($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereUserNum($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereDeptID($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereIssuedBy($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereIssueDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereLoanLength($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereDueDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereReturnDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereRenewCount($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereFine($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereFinePaidDate($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereStatus($value) 
 * @method static \Illuminate\Database\Query\Builder|\Loan whereNotes($value) 
 * @method static \Loan withFines() 
 * @method static \Loan unavailable() 
 * @method static \Loan active() 
 * @method static \Loan typeLoan()
 * @method static \Loan returned()
 * @method static \Loan reservations() 
 * @method static \Loan canceled() 
 * @method static \Loan longTerm() 
 * @method static \Loan overdue() 
 */
class Loan extends Loaner\Model  {

    const STATUS_SHORT_TERM = 2;
    const STATUS_OWING = 3;
    const STATUS_RETURNED = 4;
    const STATUS_RESERVED = 5;
    const STATUS_CANCELED = 6;
    const STATUS_LONG_TERM = 7;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'loans';

	public $timestamps = false;

    protected $dates = array('issue_date', 'due_date','return_date', 'fine_paid_date');

    protected $primaryKey = 'lid';

	public function kit()
    {
    	return $this->belongsTo('Kit', 'kitid');
    }

	public function equipment()
    {
    	return $this->belongsTo('Equipment', 'equipmentid');
    }
    
    public function department()
    {
    	return $this->belongsTo('Department', 'deptID');
    }
    
    public function user()
    {
    	return $this->belongsTo('User', 'userNum');
    }

    public function scopeWithFines($query)
    {
        return $query->where($this->table.'.fine', '>', 0);
    }

    public function scopeUnavailable($query)
    {
        return $query->active();
    }

    public function scopeTypeLoan($query)
    {
        return $query->whereIn($this->table.'.status', array( static::STATUS_SHORT_TERM, static::STATUS_RETURNED, static::STATUS_OWING, static::STATUS_LONG_TERM ) );
    }

    public function scopeActive($query)
    {
        return $query->whereIn($this->table.'.status', array( static::STATUS_SHORT_TERM, static::STATUS_OWING, static::STATUS_LONG_TERM ) );
    }

    public function scopeReturned($query)
    {
        return $query->whereIn($this->table.'.status', array( static::STATUS_RETURNED ) );
    }

    public function scopeReservations($query)
    {
        return $query->whereIn($this->table.'.status', array( static::STATUS_RESERVED ) );
    }

    public function scopeCanceled($query)
    {
        return $query->whereIn($this->table.'.status', array( static::STATUS_CANCELED ) );
    }

    public function scopeLongTerm($query)
    {
        return $query->whereIn($this->table.'.status', array(static::STATUS_LONG_TERM));
    }

    public function scopeOverdue($query)
    {
        return $query->where(DB::raw(sprintf('(%d - CAST(%s.due_date AS SIGNED))', time(), $this->table)), '>', '0')
            ->whereIn($this->table.'.status', array( static::STATUS_SHORT_TERM, static::STATUS_OWING, static::STATUS_LONG_TERM ));
    }

    public function scopeOverdueOpen($query)
    {
        return $query->where(DB::raw(sprintf('(%d - CAST(%s.due_date AS SIGNED))', time(), $this->table)), '>', '0')
            ->whereIn($this->table.'.status', array( static::STATUS_SHORT_TERM, static::STATUS_OWING, static::STATUS_LONG_TERM ))
            ->where(function($q){
                $q->orWhere('due_date', '<', DB::raw('return_date'));
                $q->orWhere('due_date', '<', DB::raw('UNIX_TIMESTAMP()'));
            });
    }

    /**
     * @return int
     */
    public function getDaysLate()
    {
        $today = Carbon::now();

        if ($this->return_date && $this->due_date)
        {
            if ($this->return_date->diffInDays($this->due_date))
            {
                return $this->return_date->diffInDays($this->due_date);
            }
        }

        if ($this->due_date)
        {
            return $today->diffInDays($today, false);
        }

        return 0;

    }

    public function getPeriod()
    {
		if ($this->loan_length)
		{
			return $this->loan_length;
		}

        if ($this->issue_date && $this->due_date)
        {
            return $this->issue_date->diffInDays($this->due_date);
        }
    }

    public function hasKit()
    {
        return $this->kitid && $this->kit instanceof Kit;
    }

    public function hasEquipment()
    {
        return $this->equipmentid && $this->equipment instanceof Equipment;
    }

    public function getDefaultPeriod()
    {
        $period = 0;

        if ($this->hasKit())
        {
            $period = $this->kit->loan_length;
        }
        elseif ($this->hasEquipment())
        {
            $period = $this->equipment->loan_lengthEQ;
        }

        return $period;
    }

    /**
     * Check if loan is past due
     * @return bool
     */
    public function isPastDue()
    {
        if ($this->due_date && !$this->due_date->lt(Carbon::now()))
        {
            return false;
        }
        return true;
    }

    /**
     * Any upcoming reservations
     * @return bool
     */
    public function hasUpcomingReservation()
    {
        $start = Carbon::now();
        $end   = Carbon::now()->setTime(13, 0, 0)->addDays($this->loan_length)->subHour();

        $upcomingReservations = Loan::query()
            ->where('issue_date', '>=', $start->format('U'))
            ->where('issue_date', '<=', $end->format('U'))
            ->reservations();

        if ($this->kitid)
        {
            $upcomingReservations->where('kitid', '=', $this->kitid);
        }
        else
        {
            $upcomingReservations->where('equipmentid', '=', $this->equipmentid);
        }

        return $upcomingReservations->count() > 0;
    }

    /**
     * Perform renewal
     */
    public function renew()
    {
        $end   = Carbon::now()->setTime(13, 0, 0)->addDays($this->loan_length)->subMinute();

        $this->fine = 0;
        $this->due_date = $end->format('U');
        $this->renew_count = $this->renew_count+1;

        $this->save();
        $this->user->calculateFine();
    }
    
    
}
