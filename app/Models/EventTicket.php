<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'price',
        'quantity',
        'sold_count',
        'sales_start_datetime',
        'sales_end_datetime',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sales_start_datetime' => 'datetime',
        'sales_end_datetime' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnSale($query)
    {
        return $query->active()
            ->where('sales_start_datetime', '<=', now())
            ->where('sales_end_datetime', '>=', now());
    }

    // Methods
    public function getAvailableQuantity()
    {
        return $this->quantity - $this->sold_count;
    }

    public function isSoldOut()
    {
        return $this->sold_count >= $this->quantity;
    }

    public function isOnSale()
    {
        return $this->is_active
            && now()->between($this->sales_start_datetime, $this->sales_end_datetime)
            && !$this->isSoldOut();
    }
}
