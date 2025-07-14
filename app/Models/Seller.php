<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
    'name',
    'telephone',
    'location',
    'slug',
    'photo',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    protected static function booted()
    {
        static::deleted(function ($saller) {
            if ($saller->photo) {
                Storage::disk('public')->delete($saller->photo);
            }
        });

        static::updating(function ($saller) {
            if ($saller->isDirty('photo')) {
                $oldphoto = $saller->getOriginal('photo');
                if ($oldphoto) {
                    Storage::disk('public')->delete($oldphoto);
                }
            }
        });
    }
}
