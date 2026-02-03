<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactMessage extends Model
{
    use HasFactory;

    protected $table = 'contact_messages';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'type',
        'status',
        'user_id',
        'reply_count',
        'last_replied_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_replied_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'contact',
        'status' => 'pending',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeContact($query)
    {
        return $query->where('type', 'contact');
    }

    public function scopeSponsorship($query)
    {
        return $query->where('type', 'sponsorship');
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    public function markAsResolved()
    {
        $this->update(['status' => 'resolved']);
    }

    public function markAsNotified()
    {
        $this->increment('reply_count');
        $this->update(['last_replied_at' => now()]);
    }
}
