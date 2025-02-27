<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    
    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function searchAll()
    {
        return Company::all();
    }

    public function conditionSearch($query)
    {
        return $query->get();
    }
}


