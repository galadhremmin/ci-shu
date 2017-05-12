<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationReview extends Model
{
    protected $fillable = [ 'translation_id' ];
    protected $dates = [ 'date_reviewed' ];
}
