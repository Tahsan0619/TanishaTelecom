<?php

use App\Models\Setting;

if (! function_exists('money')) {
    function money(float|int|string|null $amount): string
    {
        $amount = (float) $amount;
        $decimals = floor($amount) == $amount ? 0 : 2;

        return Setting::getValue('currency_symbol', '৳').number_format($amount, $decimals);
    }
}
