<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Otp extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public $table = 'otp';

    public $timestamps = false;

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
