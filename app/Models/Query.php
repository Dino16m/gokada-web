<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function Addresses(){
        return $this->belongsToMany(Address::class);
    }
}
