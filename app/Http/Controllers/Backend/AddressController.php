<?php

namespace App\Http\Controllers\Backend;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;

class AddressController extends BaseController
{
    protected string $resource = 'address';

    protected array $additionalPermissions = ['address_management_access'];

    public function index()
    {
        $addresses = Address::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.addresses.index', compact('addresses'));
    }

    public function create()
    {
        $customers = User::all();

        return view('admin.addresses.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        Address::create($validated);

        return redirect()->route('admin.addresses.index')->with('success', 'Address created successfully.');
    }

    public function show(Address $address)
    {
        $address->load('customer');

        return view('admin.addresses.show', compact('address'));
    }

    public function edit(Address $address)
    {
        $customers = User::all();

        return view('admin.addresses.edit', compact('address', 'customers'));
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $address->update($validated);

        return redirect()->route('admin.addresses.index')->with('success', 'Address updated successfully.');
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return redirect()->route('admin.addresses.index')->with('success', 'Address deleted successfully.');
    }

    public function byCustomer($customer)
    {
        $addresses = Address::where('customer_id', $customer)->paginate(15);

        return view('admin.addresses.index', compact('addresses'));
    }

    public function orders()
    {
        return redirect()->back()->with('info', 'Orders feature is available; please contact administrator for full implementation.');
    }
}
