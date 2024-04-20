<?php

namespace Pratiksh\Payable\Models;

use Illuminate\Database\Eloquent\Model;

class Fiscal extends Model
{
    protected $fillable = ['year', 'start_date', 'end_date'];

    public function __construct()
    {
        parent::__construct();
        $this->table = config(
            'payable.table_prefix',
            'payable_'
        ).'fiscals';
    }

    // Relationships
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
