<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportPhoto extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function photo() :BelongsTo
    {
        return $this->belongsTo(Photo::class);
    }

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
