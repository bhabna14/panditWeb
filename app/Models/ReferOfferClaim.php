<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferOfferClaim extends Model
{
    use HasFactory;

    protected $table = "flower__refer_offer_claim";

    protected $fillable = [
        'offer_id',
        'user_id',
        'selected_pairs',
        'date_time',
        'status',
    ];

    protected $casts = [
        'selected_pairs' => 'array',
        'date_time'      => 'datetime',
    ];

    // User is keyed by users.userid (string PK)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userid');
    }

    // Offer is standard id
    public function offer()
    {
        return $this->belongsTo(ReferOffer::class, 'offer_id', 'offer_id');
    }
}