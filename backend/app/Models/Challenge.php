<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_hash',
        'session_hash',
        'request',
        'prompt_id',
        'prompt',
        'response',
        'response_time',
        'stream',
    ];

    protected $casts = [
        'stream' => 'array',
    ];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}

