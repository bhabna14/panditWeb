<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingVisitPlace extends Model
{
    use HasFactory;

    protected $table = 'flower__marketing_visit_place';

    protected $fillable = [
        'visitor_name',
        'location_type',
        'date_time',
        'contact_person_name',
        'contact_person_number',
        'no_of_apartment',
        'already_delivery',
        'apartment_name',
        'apartment_number',
        'locality_name',
        'landmark'
    ];

}
