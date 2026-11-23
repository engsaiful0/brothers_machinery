<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('business_locations')->select('id', 'default_payment_accounts')->orderBy('id')->chunkById(100, function ($locations) {
            foreach ($locations as $location) {
                $payment_accounts = json_decode($location->default_payment_accounts, true);
                if (! is_array($payment_accounts)) {
                    $payment_accounts = [];
                }

                foreach (['credit', 'due'] as $method) {
                    if (! array_key_exists($method, $payment_accounts)) {
                        $payment_accounts[$method] = [
                            'is_enabled' => '1',
                            'account' => null,
                        ];
                    }
                }

                DB::table('business_locations')->where('id', $location->id)->update([
                    'default_payment_accounts' => json_encode($payment_accounts),
                ]);
            }
        });
    }

    public function down()
    {
        // Keep location payment settings intact when rolling back.
    }
};
