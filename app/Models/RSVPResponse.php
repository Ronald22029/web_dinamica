<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RSVPResponse extends Model
{
    use HasFactory;

    protected $table = 'rsvp_responses';

    protected $fillable = [
        'invitation_id',
        'name',
        'attending',
        'guests',
        'message',
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
