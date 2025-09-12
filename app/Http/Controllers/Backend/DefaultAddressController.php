<?php

namespace App\Http\Controllers\Backend;

use App\Models\DefaultAddress;
use App\Models\User;
use App\Models\Address;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class DefaultAddressController extends BaseController
{
    protected string $resource = 'default_address';
    
    protected array $additionalPermissions = ['customer_management_access'];

    public function index(Request $request)
    {
        $query = DefaultAddress::with(['customer', 'address']);
        
        // Filter by customer if provided
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by address type if provided
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $defaultAddresses = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.default-addresses.index', compact('defaultAddresses'));
    }

    public function store(Request $request, User $customer)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'type' => 'required|in:billing,shipping',
        ]);
        
        // Verify the address belongs to the customer
        $address = Address::where('id', $request->address_id)
                         ->where('customer_id', $customer->id)
                         ->firstOrFail();
        
        // Check if a default address of this type already exists for the customer
        $existingDefault = DefaultAddress::where('customer_id', $customer->id)
                                       ->where('type', $request->type)
                                       ->first();
        
        if ($existingDefault) {
            // Update existing default address
            $existingDefault->update(['address_id' => $address->id]);
            $message = 'Default address updated successfully.';
        } else {
            // Create new default address
            DefaultAddress::create([
                'customer_id' => $customer->id,
                'address_id' => $address->id,
                'type' => $request->type,
            ]);
            $message = 'Default address created successfully.';
        }
        
        return redirect()->route('admin.default-addresses.index')
            ->with('success', $message);
    }

    public function destroy(DefaultAddress $defaultAddress)
    {
        $defaultAddress->delete();
        return redirect()->route('admin.default-addresses.index')
            ->with('success', 'Default address removed successfully.');
    }

    /**
     * Get default addresses for a specific customer
     */
    public function byCustomer(User $customer)
    {
        $defaultAddresses = DefaultAddress::with(['customer', 'address'])
            ->where('customer_id', $customer->id)
            ->orderBy('type')
            ->get();
            
        return view('admin.default-addresses.by-customer', compact('defaultAddresses', 'customer'));
    }

    /**
     * Set billing address as default for a customer
     */
    public function setBillingDefault(Request $request, User $customer)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        // Verify the address belongs to the customer
        $address = Address::where('id', $request->address_id)
                         ->where('customer_id', $customer->id)
                         ->firstOrFail();
        
        DefaultAddress::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'type' => 'billing',
            ],
            [
                'address_id' => $address->id,
            ]
        );
        
        return redirect()->back()
            ->with('success', 'Default billing address set successfully.');
    }

    /**
     * Set shipping address as default for a customer
     */
    public function setShippingDefault(Request $request, User $customer)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);
        
        // Verify the address belongs to the customer
        $address = Address::where('id', $request->address_id)
                         ->where('customer_id', $customer->id)
                         ->firstOrFail();
        
        DefaultAddress::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'type' => 'shipping',
            ],
            [
                'address_id' => $address->id,
            ]
        );
        
        return redirect()->back()
            ->with('success', 'Default shipping address set successfully.');
    }

    /**
     * Clear all default addresses for a customer
     */
    public function clearCustomerDefaults(User $customer)
    {
        $count = DefaultAddress::where('customer_id', $customer->id)->count();
        DefaultAddress::where('customer_id', $customer->id)->delete();
        
        return redirect()->route('admin.default-addresses.index')
            ->with('success', "Cleared {$count} default addresses for customer.");
    }
}