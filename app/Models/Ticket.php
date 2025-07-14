<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'name',
        'thumbnail',
        'address',
        'path_video',
        'price',
        'is_popular',
        'about',
        'open_time_at',
        'closed_time_at',
        'category_id',
        'seller_id',
        'slug',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TicketPhoto::class);
    }

    protected static function booted()
    {
        static::deleted(function ($ticket) {
           if ($ticket->thumbnail) {
                Storage::disk('public')->delete($ticket->thumbnail);
            }

        });

        static::updating(function ($ticket) {
            if ($ticket->isDirty('thumbnail')) {
                $oldthumbnail = $ticket->getOriginal('thumbnail');
                if ($oldthumbnail) {
                    Storage::disk('public')->delete($oldthumbnail);
                }
            }
        });
    }
}
