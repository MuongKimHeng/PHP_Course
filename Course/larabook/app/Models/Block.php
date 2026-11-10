<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
{
    use HasFactory;
    protected $fillable = ['topic_id', 'title', 'content', 'image_path'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
