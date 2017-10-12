<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contribution extends ModelBase
{
    use Traits\HasAccount;

    protected $fillable = [ 
        'account_id', 
        'language_id', 
        'translation_id',
        'sentence_id',
        'word',
        'payload', 
        'keywords', 
        'notes',
        'sense',
        'type' 
    ];
    protected $dates = [
        'created_at',
        'updated_at',
        'date_reviewed'
    ];
    
    public function reviewed_by() 
    {
        return $this->belongsTo(Account::class, 'reviewed_by_account_id');
    }
}
