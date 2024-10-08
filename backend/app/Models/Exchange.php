<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }


}
