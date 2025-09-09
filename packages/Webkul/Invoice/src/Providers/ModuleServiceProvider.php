<?php

namespace Webkul\Invoice\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Invoice\Models\Invoice::class,
        \Webkul\Invoice\Models\InvoiceItem::class,
    ];
}
