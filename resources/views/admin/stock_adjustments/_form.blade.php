@csrf
<div class="row">
  <div class="col-md-4"><div class="form-group">
    <label>Warehouse <span class="text-danger">*</span></label>
    <select name="warehouse_id" class="form-control" required>
      <option value="">Select warehouse</option>
      @foreach($warehouses as $w)
        <option value="{{ $w->id }}" @selected(old('warehouse_id', $stockAdjustment->warehouse_id ?? null) == $w->id)>{{ $w->name }}</option>
      @endforeach
    </select>
  </div></div>
  <div class="col-md-4"><div class="form-group">
    <label>Adjustment Date <span class="text-danger">*</span></label>
    <input type="date" name="adjustment_date" class="form-control" value="{{ old('adjustment_date', optional($stockAdjustment->adjustment_date ?? null)->format('Y-m-d') ?? now()->toDateString()) }}" required>
  </div></div>
  <div class="col-md-4"><div class="form-group">
    <label>Reason <span class="text-danger">*</span></label>
    <select name="reason" class="form-control" required>
      @foreach(\App\Models\StockAdjustment::REASONS as $r)
        <option value="{{ $r }}" @selected(old('reason', $stockAdjustment->reason ?? null) == $r)>{{ ucfirst($r) }}</option>
      @endforeach
    </select>
  </div></div>
</div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $stockAdjustment->notes ?? '') }}</textarea></div>

<h5 class="mt-4">Items</h5>
<div class="table-responsive">
  <table class="table table-sm" id="itemsTable">
    <thead>
      <tr><th>Product <span class="text-danger">*</span></th><th>Variant</th><th>Actual Qty <span class="text-danger">*</span></th><th>Unit Cost</th><th>Notes</th><th></th></tr>
    </thead>
    <tbody>
      @php $items = old('items', isset($stockAdjustment) ? $stockAdjustment->items->toArray() : [['product_id' => null]]); @endphp
      @foreach($items as $i => $item)
        <tr>
          <td>
            <select name="items[{{ $i }}][product_id]" class="form-control" required>
              <option value="">Select product</option>
              @foreach($products as $p)
                <option value="{{ $p->id }}" @selected(($item['product_id'] ?? null) == $p->id)>{{ $p->name ?? $p->slug }}</option>
              @endforeach
            </select>
          </td>
          <td><input type="number" name="items[{{ $i }}][product_variant_id]" value="{{ $item['product_variant_id'] ?? '' }}" class="form-control" placeholder="Variant ID (opt)"></td>
          <td><input type="number" name="items[{{ $i }}][actual_quantity]" value="{{ $item['actual_quantity'] ?? 0 }}" class="form-control" min="0" required></td>
          <td><input type="number" name="items[{{ $i }}][unit_cost]" value="{{ $item['unit_cost'] ?? 0 }}" class="form-control" step="0.0001" min="0"></td>
          <td><input type="text" name="items[{{ $i }}][notes]" value="{{ $item['notes'] ?? '' }}" class="form-control"></td>
          <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
<button type="button" class="btn btn-sm btn-outline-primary" id="addItem"><i class="fas fa-plus"></i> Add line</button>

@push('scripts')
<script>
document.getElementById('addItem')?.addEventListener('click', () => {
  const tbody = document.querySelector('#itemsTable tbody');
  const i = tbody.querySelectorAll('tr').length;
  const tr = tbody.querySelector('tr');
  if (!tr) return;
  const clone = tr.cloneNode(true);
  clone.querySelectorAll('select, input').forEach(el => {
    el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
    if (el.type !== 'hidden') el.value = el.tagName === 'SELECT' ? '' : (el.type === 'number' ? 0 : '');
  });
  tbody.appendChild(clone);
});
</script>
@endpush
