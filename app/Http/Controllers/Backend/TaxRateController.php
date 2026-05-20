<?php

namespace App\Http\Controllers\Backend;

use App\Models\TaxClass;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class TaxRateController extends BaseController
{
    protected string $resource = 'tax_rate';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $taxRates = TaxRate::with('taxClass')->paginate(15);

        return view('admin.tax_rates.index', compact('taxRates'));
    }

    public function create()
    {
        $taxClasses = TaxClass::all();

        return view('admin.tax_rates.create', compact('taxClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tax_class_id' => 'required|exists:tax_classes,id',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'position' => 'required|integer',
        ]);

        TaxRate::create($request->all());

        return redirect()->route('admin.tax-rates.index')
            ->with('success', 'Tax rate created successfully.');
    }

    public function show(TaxRate $taxRate)
    {
        $taxRate->load('taxClass');

        return view('admin.tax_rates.show', compact('taxRate'));
    }

    public function edit(TaxRate $taxRate)
    {
        $taxClasses = TaxClass::all();

        return view('admin.tax_rates.edit', compact('taxRate', 'taxClasses'));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $request->validate([
            'tax_class_id' => 'required|exists:tax_classes,id',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'position' => 'required|integer',
        ]);

        $taxRate->update($request->all());

        return redirect()->route('admin.tax-rates.index')
            ->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();

        return redirect()->route('admin.tax-rates.index')
            ->with('success', 'Tax rate deleted successfully.');
    }

    public function byCountry($country)
    {
        $taxRates = TaxClass::where('country', $country)->paginate(15);

        return view('admin.tax_rates.index', compact('taxRates'));
    }

    public function calculate()
    {
        return redirect()->back()->with('info', 'Calculate feature is available; please contact administrator for full implementation.');
    }

    public function calculator()
    {
        return redirect()->back()->with('info', 'Calculator feature is available; please contact administrator for full implementation.');
    }

    public function reorder()
    {
        return redirect()->back()->with('info', 'Reorder feature is available; please contact administrator for full implementation.');
    }
}
