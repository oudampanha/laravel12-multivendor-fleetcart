@csrf
<div class="row">
  <div class="col-md-3"><div class="form-group"><label>Code <span class="text-danger">*</span></label><input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}" class="form-control" required></div></div>
  <div class="col-md-6"><div class="form-group"><label>Name <span class="text-danger">*</span></label><input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" class="form-control" required></div></div>
  <div class="col-md-3"><div class="form-group"><label>&nbsp;</label>
    <div class="form-check">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" id="sup-active" class="form-check-input" {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="sup-active">Active</label>
    </div>
  </div></div>
</div>
<div class="row">
  <div class="col-md-4"><div class="form-group"><label>Contact Person</label><input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="form-control"></div></div>
  <div class="col-md-4"><div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="form-control"></div></div>
  <div class="col-md-4"><div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="form-control"></div></div>
</div>
<div class="form-group"><label>Address</label><textarea name="address" class="form-control" rows="2">{{ old('address', $supplier->address ?? '') }}</textarea></div>
<div class="row">
  <div class="col-md-3"><div class="form-group"><label>City</label><input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>State</label><input type="text" name="state" value="{{ old('state', $supplier->state ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>Country</label><input type="text" name="country" value="{{ old('country', $supplier->country ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>Zip</label><input type="text" name="zip" value="{{ old('zip', $supplier->zip ?? '') }}" class="form-control"></div></div>
</div>
<div class="row">
  <div class="col-md-6"><div class="form-group"><label>Tax Number</label><input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number ?? '') }}" class="form-control"></div></div>
  <div class="col-md-6"><div class="form-group"><label>Payment Terms</label><input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms ?? '') }}" class="form-control" placeholder="e.g. Net 30"></div></div>
</div>
<div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes', $supplier->notes ?? '') }}</textarea></div>
