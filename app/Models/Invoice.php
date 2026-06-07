<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'project_id', 'invoice_number', 'amount', 'vat_rate', 'vat_amount', 'total_with_vat', 'issue_date', 'due_date', 'status'];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum('total');
        $this->amount = $subtotal;
        $this->vat_amount = round($subtotal * ($this->vat_rate / 100), 2);
        $this->total_with_vat = $subtotal + $this->vat_amount;
        $this->saveQuietly();
    }
}
