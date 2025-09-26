<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    // Ensure this matches your real table name
    protected $table = 'flower__apartment';

    protected $fillable = ['locality_id','apartment_name'];

    /**
     * Each apartment belongs to a Locality.
     * foreign key on this model: locality_id
     * owner key on Locality: unique_code
     */
    public function locality()
    {
        return $this->belongsTo(Locality::class, 'locality_id', 'unique_code');
    }
}
