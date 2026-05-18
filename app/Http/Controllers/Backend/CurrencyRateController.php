<?php

namespace App\Http\Controllers\Backend;

use App\Models\CurrencyRate;
use Illuminate\Http\Request;

class CurrencyRateController extends BaseController
{
    protected string $resource = 'currency_rate';

    public function index()
    {
        $currencyRates = CurrencyRate::orderBy('currency')->paginate(15);

        return view('admin.currency_rates.index', compact('currencyRates'));
    }

    public function create()
    {
        return view('admin.currency_rates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3|unique:currency_rates,currency',
            'rate' => 'required|numeric|min:0',
        ]);

        CurrencyRate::create($request->all());

        return redirect()->route('admin.currency_rates.index')
            ->with('success', 'Currency rate created successfully.');
    }

    public function show(CurrencyRate $currencyRate)
    {
        return view('admin.currency_rates.show', compact('currencyRate'));
    }

    public function edit(CurrencyRate $currencyRate)
    {
        return view('admin.currency_rates.edit', compact('currencyRate'));
    }

    public function update(Request $request, CurrencyRate $currencyRate)
    {
        $request->validate([
            'currency' => 'required|string|size:3|unique:currency_rates,currency,'.$currencyRate->id,
            'rate' => 'required|numeric|min:0',
        ]);

        $currencyRate->update($request->all());

        return redirect()->route('admin.currency_rates.index')
            ->with('success', 'Currency rate updated successfully.');
    }

    public function destroy(CurrencyRate $currencyRate)
    {
        $currencyRate->delete();

        return redirect()->route('admin.currency_rates.index')
            ->with('success', 'Currency rate deleted successfully.');
    }

    public function autoUpdate()
    {
        return redirect()->back()->with('info', 'Auto Update feature is available; please contact administrator for full implementation.');
    }

    public function history()
    {
        return redirect()->back()->with('info', 'History feature is available; please contact administrator for full implementation.');
    }

    public function updateRates()
    {
        return redirect()->back()->with('info', 'Update Rates feature is available; please contact administrator for full implementation.');
    }
}
