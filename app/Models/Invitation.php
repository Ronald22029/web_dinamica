<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'is_demo',
        'template',
        'title',
        'client_name',
        'event_date',
        'description',
        'gallery_images',
        'preview_url',
        'data',
        'visits_count',
        'user_id',
    ];

    protected $casts = [
        'is_demo' => 'boolean',
        'gallery_images' => 'array',
        'data' => 'array',
        'event_date' => 'date',
        'visits_count' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(RSVPResponse::class);
    }
}
