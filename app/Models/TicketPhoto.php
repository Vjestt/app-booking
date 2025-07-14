<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
    'photo',
    'ticket_id',
    ];

    protected static function booted()
{
    static::deleted(function ($photo) {
        if ($photo->photo) {
            Storage::disk('public')->delete($photo->photo);
        }
    });

    static::updating(function ($photo) {
        if ($photo->isDirty('photo')) {
            $oldPath = $photo->getOriginal('photo');
            if ($oldPath ) {
                Storage::disk('public')->delete($oldPath);
            }
        }
    });
}

}
