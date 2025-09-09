<?php

namespace Webkul\Invoice\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Invoice\Contracts\InvoiceItem as InvoiceItemContract;

class InvoiceItem extends Model implements InvoiceItemContract
{
    protected $table = 'invoice_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'name',
        'quantity',
        'price',
        'coupon_code',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'product_id',
        'invoice_id',
    ];

    /**
     * Get the invoice record associated with the invoice item.
     */
    public function invoice()
    {
        return $this->belongsTo(InvoiceProxy::modelClass());
    }
}
