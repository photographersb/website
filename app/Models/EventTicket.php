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
        'reserved_qty',
        'sales_start_datetime',
        'sales_end_datetime',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'reserved_qty' => 'integer',
        'sales_start_datetime' => 'datetime',
        'sales_end_datetime' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'available_quantity',
        'is_on_sale',
        'is_sold_out',
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
        $reserved = (int) ($this->reserved_qty ?? 0);
        return $this->quantity - $this->sold_count - $reserved;
    }

    public function getAvailableQuantityAttribute()
    {
        return $this->getAvailableQuantity();
    }

    public function isSoldOut()
    {
        $reserved = (int) ($this->reserved_qty ?? 0);
        return ($this->sold_count + $reserved) >= $this->quantity;
    }

    public function getIsSoldOutAttribute()
    {
        return $this->isSoldOut();
    }

    public function isOnSale()
    {
        return $this->is_active
            && now()->between($this->sales_start_datetime, $this->sales_end_datetime)
            && !$this->isSoldOut();
    }

    public function getIsOnSaleAttribute()
    {
        return $this->isOnSale();
    }
}
