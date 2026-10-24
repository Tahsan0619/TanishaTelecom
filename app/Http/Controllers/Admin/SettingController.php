<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ShurjoPay;
use App\Support\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(ShurjoPay $gateway): View
    {
        return view('admin.settings', ['settings' => Setting::allValues(), 'gateway' => $gateway]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:255'],
            'banner_title' => ['nullable', 'string', 'max:255'],
            'banner_subtitle' => ['nullable', 'string', 'max:255'],
            'banner_badge' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'shipping_inside_dhaka' => ['required', 'numeric', 'min:0'],
            'shipping_outside_dhaka' => ['required', 'numeric', 'min:0'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'max:5120'],
        ]);

        foreach (['logo' => 'logo', 'banner_image' => 'banner'] as $field => $folder) {
            unset($data[$field]);
            if ($request->hasFile($field)) {
                Media::delete(Setting::getValue($field));
                $data[$field] = Media::store($request->file($field), $folder);
            } elseif ($request->boolean('remove_'.$field)) {
                Media::delete(Setting::getValue($field));
                $data[$field] = null;
            }
        }

        Setting::putMany($data);

        return back()->with('success', 'Settings saved.');
    }
}
