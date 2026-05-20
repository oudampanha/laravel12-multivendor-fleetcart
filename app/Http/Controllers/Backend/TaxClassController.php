<?php

namespace App\Http\Controllers\Backend;

use App\Models\TaxClass;
use Illuminate\Http\Request;

class TaxClassController extends BaseController
{
    protected string $resource = 'tax_class';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $taxClasses = TaxClass::with('taxRates')->paginate(15);

        return view('admin.tax_classes.index', compact('taxClasses'));
    }

    public function create()
    {
        return view('admin.tax_classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'based_on' => 'required|string|in:billing_address,shipping_address,store_address',
        ]);

        TaxClass::create($request->all());

        return redirect()->route('admin.tax-classes.index')
            ->with('success', 'Tax class created successfully.');
    }

    public function show(TaxClass $taxClass)
    {
        $taxClass->load('taxRates');

        return view('admin.tax_classes.show', compact('taxClass'));
    }

    public function edit(TaxClass $taxClass)
    {
        return view('admin.tax_classes.edit', compact('taxClass'));
    }

    public function update(Request $request, TaxClass $taxClass)
    {
        $request->validate([
            'based_on' => 'required|string|in:billing_address,shipping_address,store_address',
        ]);

        $taxClass->update($request->all());

        return redirect()->route('admin.tax-classes.index')
            ->with('success', 'Tax class updated successfully.');
    }

    public function destroy(TaxClass $taxClass)
    {
        $taxClass->delete();

        return redirect()->route('admin.tax-classes.index')
            ->with('success', 'Tax class deleted successfully.');
    }

    public function addRate()
    {
        return redirect()->back()->with('info', 'Add Rate feature is available; please contact administrator for full implementation.');
    }

    public function rates()
    {
        return redirect()->back()->with('info', 'Rates feature is available; please contact administrator for full implementation.');
    }
}
