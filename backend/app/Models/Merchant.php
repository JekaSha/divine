<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'secret',
        'stream',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'stream' => 'array', // Automatically cast to/from JSON
    ];

    /**
     * Relationship with the Invoice model.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
