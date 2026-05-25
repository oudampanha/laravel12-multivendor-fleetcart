<?php

namespace App\Http\Controllers\Backend;

use App\Models\Order;
use App\Models\OrderDownload;
use Illuminate\Http\Request;

class OrderDownloadController extends BaseController
{
    protected string $resource = 'order_download';

    protected array $additionalPermissions = ['order_download_management_access'];

    public function index()
    {
        $orderDownloads = OrderDownload::with(['order'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.order-downloads.index', compact('orderDownloads'));
    }

    public function create()
    {
        $orders = Order::all();

        return view('admin.order-downloads.create', compact('orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'file_id' => 'required|integer',
        ]);

        OrderDownload::create($validated);

        return redirect()->route('admin.order-downloads.index')->with('success', 'Order Download created successfully.');
    }

    public function show(OrderDownload $orderDownload)
    {
        $orderDownload->load(['order']);

        return view('admin.order-downloads.show', compact('orderDownload'));
    }

    public function edit(OrderDownload $orderDownload)
    {
        $orders = Order::all();

        return view('admin.order-downloads.edit', compact('orderDownload', 'orders'));
    }

    public function update(Request $request, OrderDownload $orderDownload)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'file_id' => 'required|integer',
        ]);

        $orderDownload->update($validated);

        return redirect()->route('admin.order-downloads.index')->with('success', 'Order Download updated successfully.');
    }

    public function destroy(OrderDownload $orderDownload)
    {
        $orderDownload->delete();

        return redirect()->route('admin.order-downloads.index')->with('success', 'Order Download deleted successfully.');
    }
}
