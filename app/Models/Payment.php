<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'file',
        'tx_id',
        'status',
        'card_type',
        'card_progress',
        'user_id',
        'card_holder_id',
        'card_id',
        'trans_id',
        'trans_address',
        'trans_amount',
        'trans_fee',
        'trans_status',
        'trans_loaded',
        'trans_from',
        'trans_to',
        'api_trans_id',
        'api_status',
        'api_response',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function card()
    {
        return $this->belongsTo(CardActivation::class);
    }
}
