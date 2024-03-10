<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follow extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded = ['id'];

    public $timestamps = false;

    public function follower() :BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following() :BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
