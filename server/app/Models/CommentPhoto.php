<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentPhoto extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];


    public $timestamps = false;

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // public function toArray()
    // {
    //     $array = parent::toArray();

    //     $array['user'] = $this->user->only(['id', 'username']); 
    //     $array['created_at'] = Carbon::parse($this->created_at)->format('Y-m-d H:i:s');

    //     return $array;
    // }
}
