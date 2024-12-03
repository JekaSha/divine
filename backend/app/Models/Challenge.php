<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = ['request', 'prompt', 'response', 'stream'];

    public function userable()
    {
        return $this->morphTo();
    }
}
