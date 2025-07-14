<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'icon',
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
        static::deleted(function ($category) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            foreach ($category->tickets as $ticket) {
                foreach ($ticket->photos as $photo) {
                    if ($photo->photo) {
                        Storage::disk('public')->delete($photo->photo);
                    }
                }
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('icon')) {
                $oldIcon = $category->getOriginal('icon');
                if ($oldIcon) {
                    Storage::disk('public')->delete($oldIcon);
                }
            }
        });
    }
}
