<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'contact_id',
        'od_sphere',
        'od_cylinder',
        'od_axis',
        'od_addition',
        'od_prism',
        'od_base',
        'od_pd',
        'os_sphere',
        'os_cylinder',
        'os_axis',
        'os_addition',
        'os_prism',
        'os_base',
        'os_pd',
        'notes',
        'created_by'
    ];

    /**
     * Get the contact that owns the prescription.
     */
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class);
    }
}
