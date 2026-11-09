<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = ['topicname'];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }
}
