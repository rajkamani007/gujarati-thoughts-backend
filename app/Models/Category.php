<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'bg_image',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['bg_image_url'];

    public function getBgImageUrlAttribute(): ?string
    {
        if (!$this->bg_image) {
            return null;
        }

        return asset('storage/' . $this->bg_image);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }
}
