@csrf
<div class="row">
  <div class="col-md-3">
    <div class="form-group">
      <label>Code <span class="text-danger">*</span></label>
      <input type="text" name="code" value="{{ old('code', $warehouse->code ?? '') }}" class="form-control" required>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>Name <span class="text-danger">*</span></label>
      <input type="text" name="name" value="{{ old('name', $warehouse->name ?? '') }}" class="form-control" required>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Position</label>
      <input type="number" name="position" value="{{ old('position', $warehouse->position ?? 0) }}" class="form-control">
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>Address</label>
      <textarea name="address" class="form-control" rows="2">{{ old('address', $warehouse->address ?? '') }}</textarea>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group"><label>City</label><input type="text" name="city" value="{{ old('city', $warehouse->city ?? '') }}" class="form-control"></div>
  </div>
  <div class="col-md-3">
    <div class="form-group"><label>State</label><input type="text" name="state" value="{{ old('state', $warehouse->state ?? '') }}" class="form-control"></div>
  </div>
</div>
<div class="row">
  <div class="col-md-3"><div class="form-group"><label>Country</label><input type="text" name="country" value="{{ old('country', $warehouse->country ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>Zip</label><input type="text" name="zip" value="{{ old('zip', $warehouse->zip ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone', $warehouse->phone ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3"><div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $warehouse->email ?? '') }}" class="form-control"></div></div>
</div>
<div class="row">
  <div class="col-md-6"><div class="form-group"><label>Contact Person</label><input type="text" name="contact_person" value="{{ old('contact_person', $warehouse->contact_person ?? '') }}" class="form-control"></div></div>
  <div class="col-md-3">
    <div class="form-group"><label>&nbsp;</label>
      <div class="form-check">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="active" {{ old('is_active', $warehouse->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="active">Active</label>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group"><label>&nbsp;</label>
      <div class="form-check">
        <input type="hidden" name="is_default" value="0">
        <input type="checkbox" name="is_default" value="1" class="form-check-input" id="default" {{ old('is_default', $warehouse->is_default ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="default">Default Warehouse</label>
      </div>
    </div>
  </div>
</div>
<div class="form-group">
  <label>Notes</label>
  <textarea name="notes" class="form-control" rows="3">{{ old('notes', $warehouse->notes ?? '') }}</textarea>
</div>
