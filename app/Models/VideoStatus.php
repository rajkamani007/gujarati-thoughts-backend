<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoStatus extends Model
{
    protected $fillable = [
        'title',
        'video',
        'thumbnail',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['video_url', 'thumbnail_url'];

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video ? asset('storage/' . $this->video) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }
}
