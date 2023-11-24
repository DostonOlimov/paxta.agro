<?php

namespace App\Models\Traits;

use App\Models\Invoice;

trait InvoicableTrait
{
    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'invoicable');
    }
}
