<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'quote_text',
        'lang',
        'image',
        'hashtags',
        'meta_title',
        'meta_description',
        'status',
        'views',
    ];

    protected $casts = [
        'status' => 'boolean',
        'views' => 'integer',
    ];

    protected $appends = ['image_url'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        return asset('storage/' . $this->image);
    }
}
