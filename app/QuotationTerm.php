<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationTerm extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Terms of a business in print order.
     */
    public static function forBusiness($business_id)
    {
        return self::where('business_id', $business_id)
            ->orderBy('sort_order')->orderBy('id')
            ->get();
    }
}
