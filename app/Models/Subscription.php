<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
protected $fillable = [ 
        'subscription_title',
        'account_name',
        'since_date',
        'next_due_date',
    ];
}
