<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private $terms = [
        ['Delivery', 'From Ready Stock.'],
        ['Mode of Payment', '100% Cash or pay order before/ at the time of delivery.'],
        ['Validity', '15 (fifteen) days from the date of this quotation.'],
        ['Price', 'Price will be considered as Excluding AIT & VAT.'],
        ['Warranty', "As per manufacturer's standard warranty."],
        ['Installation', 'Installation and commissioning by our engineers.'],
    ];

    /**
     * Give every business a starter set of commercial terms; businesses that
     * already have terms are left alone.
     */
    public function up()
    {
        $now = now();
        foreach (DB::table('business')->pluck('id') as $business_id) {
            if (DB::table('quotation_terms')->where('business_id', $business_id)->exists()) {
                continue;
            }

            DB::table('quotation_terms')->insert(array_map(function ($term, $index) use ($business_id, $now) {
                return [
                    'business_id' => $business_id,
                    'title' => $term[0],
                    'description' => $term[1],
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $this->terms, array_keys($this->terms)));
        }
    }

    public function down()
    {
        foreach ($this->terms as $term) {
            DB::table('quotation_terms')->where('title', $term[0])->where('description', $term[1])->delete();
        }
    }
};
