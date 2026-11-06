<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\TariffSetting;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index()
    {
        $tariff = TariffSetting::current();
        return view('admin.tariff.index', compact('tariff'));
    }

    public function update(Request $request)
    {
        $tariff = TariffSetting::current();

        $validated = $request->validate([
            'water_price_per_liter' => ['required', 'numeric', 'min:1'],
            'delivery_price_per_km' => ['required', 'numeric', 'min:0'],
            'base_fee' => ['required', 'numeric', 'min:0'],
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:100'],
            'depot_latitude' => ['required', 'numeric'],
            'depot_longitude' => ['required', 'numeric'],
        ]);

        $tariff->update($validated);

        ActivityLog::log(
            'TARIFF_UPDATED',
            "Admin memperbarui tarif: Air Rp {$tariff->water_price_per_liter}/L, Ongkir Rp {$tariff->delivery_price_per_km}/km, Base Fee Rp {$tariff->base_fee}."
        );

        return back()->with('success', 'Konfigurasi tarif dan operasional berhasil diperbarui.');
    }
}

