<!-- Step 4: Price Input -->
<div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
  <div class="col-md-2">
    <label for="bulk-edit-variants-price" class="form-label mb-0"><strong>Price</strong></label>
  </div>
  <div class="col-md-5">
    <div class="input-group">
      <span class="input-group-text">$</span>
      <input type="number" step="0.01" class="form-control" id="bulk-edit-variants-price" name="bulk-edit-variants-price" placeholder="0">
    </div>
  </div>
</div>

<!-- Step 5: Apply Button -->
<div class="row mb-0 align-items-center" id="applyBulkChangesSection">
  <div class="col-md-2"></div>
  <div class="col-md-5">
    <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
  </div>
</div>

<div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
  <div class="col-md-2">
    <label for="bulk-edit-variants-is-active" class="form-label mb-0"><strong>Status</strong></label>
  </div>
  <div class="col-md-5">
    <div class="form-check">
      <label class="form-check-label" for="bulk-edit-variants-is-active">
        <input type="checkbox" class="form-check-input" id="bulk_edit_variants_is_active" name="bulk_edit_variants_is_active" value=""> Enable the variant
      </label>
    </div>
  </div>
</div>
