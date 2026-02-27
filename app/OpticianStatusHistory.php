<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpticianStatusHistory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
}
