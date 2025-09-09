<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Lead\Models\LeadProxy;
use Webkul\Invoice\Contracts\Invoice as InvoiceContract;
use Webkul\User\Models\UserProxy;

class Invoice extends Model implements InvoiceContract
{
    use CustomAttribute;

    protected $table = 'invoices';

    protected $casts = [
        'billing_address'  => 'array',
        'shipping_address' => 'array',
        'expired_at'       => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'description',
        'billing_address',
        'shipping_address',
        'discount_percent',
        'discount_amount',
        'tax_amount',
        'adjustment_amount',
        'sub_total',
        'grand_total',
        'expired_at',
        'user_id',
        'person_id',
    ];

    /**
     * Get the invoice items record associated with the invoice.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItemProxy::modelClass());
    }

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the person that owns the invoice.
     */
    public function person()
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    /**
     * The leads that belong to the invoice.
     */
    public function leads()
    {
        return $this->belongsToMany(LeadProxy::modelClass(), 'lead_invoices');
    }
}
