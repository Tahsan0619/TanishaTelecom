<?php

namespace App\Models;

use App\Services\ShurjoPay;
use App\Support\Media;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    private const CACHE_KEY = 'site_settings';

    /** Defaults used until the admin saves a value. */
    public const DEFAULTS = [
        'site_name' => 'Tanisha Telecom',
        'site_tagline' => 'GADGETS & ACCESSORIES',
        'footer_text' => 'Your trusted destination for original gadgets.',
        'logo' => null,
        'banner_image' => null,
        'banner_title' => 'TANISHA TELECOM',
        'banner_subtitle' => 'Smartphones • Laptops • Smartwatches • Accessories',
        'banner_badge' => '100% Original Products',
        'phone' => '01754471654',
        'email' => 'info@tanishatelecom.com',
        'address' => 'Dhaka, Bangladesh',
        'facebook' => 'https://www.facebook.com/',
        'whatsapp' => '01754471654',
        'shipping_inside_dhaka' => '60',
        'shipping_outside_dhaka' => '120',
        'currency_symbol' => '৳',
    ];

    public static function allValues(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return array_merge(self::DEFAULTS, static::query()->pluck('value', 'key')->toArray());
        });
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return self::allValues()[$key] ?? $default;
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Cache::forget(self::CACHE_KEY);
    }

    public static function publicValues(): array
    {
        $values = self::allValues();
        $values['logo_url'] = Media::url($values['logo']);
        $values['banner_image_url'] = Media::url($values['banner_image']);
        $values['shipping_inside_dhaka'] = (float) $values['shipping_inside_dhaka'];
        $values['shipping_outside_dhaka'] = (float) $values['shipping_outside_dhaka'];
        $values['online_payment'] = app(ShurjoPay::class)->isConfigured();

        return $values;
    }
}
