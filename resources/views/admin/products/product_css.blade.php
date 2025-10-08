<style>
  :root {
    /* Colors - Updated with better contrast and accessibility */
    --color-primary: #007bff;
    --color-primary-hover: #0056b3;
    --color-success: #28a745;
    --color-danger: #dc3545;
    --color-danger-hover: #c82333;
    --color-warning: #ffc107;
    --color-info: #17a2b8;

    /* Border colors */
    --color-border: #e9ecef;
    --color-border-light: #f8f9fa;
    --color-border-dark: #dee2e6;
    --color-border-dashed: #cbd3da;

    /* Background colors */
    --color-background: #f8f9fa;
    --color-background-hover: #f0f7ff;
    --color-background-dark: #6c757d;
    --color-white: #fff;

    /* Text colors */
    --color-text: #495057;
    --color-text-muted: #6c757d;
    --color-text-light: #adb5bd;

    /* Effects */
    --color-overlay: rgba(0, 0, 0, 0.7);
    --color-shadow: rgba(0, 0, 0, 0.1);
    --color-shadow-hover: rgba(0, 0, 0, 0.15);

    /* Spacing - Following 8px grid system */
    --spacing-xs: 4px;
    --spacing-sm: 8px;
    --spacing-md: 16px;
    --spacing-lg: 24px;
    --spacing-xl: 32px;
    --spacing-xxl: 48px;

    /* Border radius */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-full: 50%;

    /* Transitions - Using consistent timing functions */
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-medium: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 0.45s cubic-bezier(0.4, 0, 0.2, 1);

    /* Z-index scale */
    --z-dropdown: 1000;
    --z-modal: 1050;
    --z-overlay: 10;
    --z-remove-btn: 20;

    /* Component sizes */
    --thumbnail-size-sm: 20px;
    --thumbnail-size-md: 24px;
    --thumbnail-size-lg: 32px;
    --form-control-height: 38px;
    --form-control-height-sm: 32px;
    --button-height: 38px;
    --button-height-sm: 32px;
  }

  /* ==========================================================================
     Custom Utility Classes (Bootstrap overrides/additions)
     ========================================================================== */

  /* Custom flex utilities for specific needs */
  .flex-1 {
    flex: 1;
  }

  /* Custom gap utilities for older Bootstrap versions */
  .gap-2 {
    gap: var(--spacing-sm);
  }

  .gap-3 {
    gap: var(--spacing-md);
  }

  .gap-4 {
    gap: var(--spacing-lg);
  }

  /* Component-specific utilities */
  .transition-all {
    transition: all var(--transition-fast);
  }

  .transition-transform {
    transition: transform var(--transition-fast);
  }

  .hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px var(--color-shadow-hover);
  }

  /* ====================================                                                                                                                                                                Common Card Components
                                                                                                                                                                                                                                                                                                                     ========================================================================== */
  .card-base {
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    background-color: var(--color-white);
    transition: all var(--transition-fast);
  }

  .card-base:hover {
    box-shadow: 0 2px 8px var(--color-shadow);
  }

  .card-header-base {
    padding: var(--spacing-md) var(--spacing-lg);
    border-bottom: 1px solid var(--color-border-light);
    background-color: var(--color-background);
    border-radius: var(--radius-md) var(--radius-md) 0 0;
  }

  .card-body-base {
    padding: var(--spacing-lg);
  }

  /* ==========================================================================                                                                                                                                                        ========================================================================== */
  .remove-btn-base {
    position: absolute;
    border: none;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: var(--radius-full);
    color: var(--color-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity var(--transition-fast);
    box-shadow: 0 2px 4px var(--color-shadow);
    z-index: var(--z-remove-btn);
  }

  .remove-btn-base:hover {
    background-color: var(--color-danger);
    color: var(--color-white);
  }

  /* ==========================================================================
                                                                                                                                                                                                                                                                            ========================================================================== */
  .product-media-container {
    display: flex;
    gap: var(--spacing-xl);
    align-items: flex-start;
  }

  /* Main Image Section */
  .main-media-wrapper {
    flex: 1;
    max-width: 150px;
  }

  .main-image-holder {
    position: relative;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    overflow: hidden;
    background-color: var(--color-background);
    aspect-ratio: 1;
    cursor: pointer;
    transition: all var(--transition-fast);
  }

  .main-image-holder:hover {
    border-color: var(--color-primary);
  }

  .main-image-holder:hover .remove-main-image {
    opacity: 1;
  }

  .main-product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .remove-main-image {
    position: absolute;
    border: none;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: var(--radius-full);
    color: var(--color-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity var(--transition-fast);
    box-shadow: 0 2px 4px var(--color-shadow);
    z-index: var(--z-remove-btn);
    top: var(--spacing-sm);
    right: var(--spacing-sm);
    width: var(--thumbnail-size-md);
    height: var(--thumbnail-size-md);
    font-size: 12px;
  }

  .remove-main-image:hover {
    background-color: var(--color-danger);
    color: var(--color-white);
  }

  /* Image Overlay */
  .image-overlay {
    position: absolute;
    inset: 0;
    background: var(--color-overlay);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--transition-fast);
    z-index: var(--z-overlay);
  }

  .main-image-holder:hover .image-overlay {
    opacity: 1;
  }

  .main-image-holder.has-image .image-overlay {
    opacity: 0;
  }

  .main-image-holder.has-image:hover .image-overlay {
    opacity: 0.8;
  }

  .overlay-content {
    text-align: center;
    color: var(--color-white);
    font-size: 12px;
  }

  .overlay-content i {
    font-size: 20px;
    margin-bottom: 5px;
  }

  .overlay-content span {
    display: block;
    font-weight: 500;
  }

  .main-image-holder.has-image .overlay-content span {
    display: none;
  }

  .main-image-holder.has-image .overlay-content i:before {
    content: '\f021';
    /* Edit icon */
  }

  /* Thumbnails Grid */
  .media-thumbnails-grid {
    flex: 2;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--spacing-md);
    max-height: 200px;
    overflow-y: auto;
  }

  .media-thumbnails-grid.hidden {
    display: none;
  }

  .media-thumbnails-grid.visible {
    display: grid;
    opacity: 1;
    animation: fadeInGrid var(--transition-medium) ease-in-out;
  }

  @keyframes fadeInGrid {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .media-thumbnail-item {
    position: relative;
    cursor: pointer;
    aspect-ratio: 1;
    min-width: 0;
  }

  .media-thumbnail-item[data-draggable="true"] {
    cursor: grab;
  }

  .media-thumbnail-item[data-draggable="true"]:active {
    cursor: grabbing;
  }

  .media-thumbnail-item:hover .thumbnail-holder {
    border-color: var(--color-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px var(--color-shadow);
  }

  .media-thumbnail-item:hover .remove-thumbnail {
    opacity: 1;
  }

  .thumbnail-holder {
    position: relative;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-md);
    overflow: hidden;
    background-color: var(--color-background);
    aspect-ratio: 1;
    transition: all var(--transition-fast);
  }

  .thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .remove-thumbnail {
    position: absolute;
    border: none;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: var(--radius-full);
    color: var(--color-danger);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity var(--transition-fast);
    box-shadow: 0 2px 4px var(--color-shadow);
    z-index: var(--z-remove-btn);
    top: var(--spacing-xs);
    right: var(--spacing-xs);
    width: var(--thumbnail-size-sm);
    height: var(--thumbnail-size-sm);
    font-size: 10px;
  }

  .remove-thumbnail:hover {
    background-color: var(--color-danger);
    color: var(--color-white);
  }

  /* Add New Media Button */
  .add-new-media .thumbnail-holder {
    border-style: dashed;
    border-color: var(--color-border-dashed);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .add-new-media:hover .thumbnail-holder {
    border-color: var(--color-primary);
    background-color: var(--color-background-hover);
  }

  .add-new-media.disabled .thumbnail-holder {
    border-color: var(--color-border);
    background-color: var(--color-background);
    opacity: 0.7;
  }

  .add-new-media.disabled:hover .thumbnail-holder {
    border-color: var(--color-border);
    background-color: var(--color-background);
  }

  .add-media-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 10px;
  }

  .add-media-content i {
    font-size: 18px;
    margin-bottom: var(--spacing-xs);
  }

  .add-media-content span {
    font-size: 11px;
    font-weight: 500;
  }

  ==========================================================================*/
  /* ==========================================================================
   Card Components (Variation, Option, Variant)
   ========================================================================== */

  .variation-card,
  .option-card,
  .variant-card {
    border: 1px solid var(--color-border);
    border-radius: var(--radius-md);
    background-color: var(--color-white);
    transition: all var(--transition-fast);
    margin-bottom: 1rem;
    transform-origin: center;
    will-change: transform;
  }

  .variation-card:hover,
  .option-card:hover,
  .variant-card:hover {
    box-shadow: 0 2px 8px var(--color-shadow);
  }

  .variation-card-header,
  .option-card-header {
    padding: 0.5rem 1rem;
    border-bottom: 1px solid var(--color-border-light);
    background-color: var(--color-background);
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    display: flex;
    align-items: center;
  }

  .variation-card-body,
  .option-card-body,
  .variant-card-body {
    padding: var(--spacing-sm);
  }

  /* New Variant Layout Styles */
  .variants-section {
    animation: fadeInVariants 0.5s ease-in-out;
  }

  /* Highlight effects for dynamic behavior */
  .highlight-changed {
    background-color: #d4edda !important;
    border-color: #28a745 !important;
    transition: all 0.3s ease;
  }

  #bulkPriceSection.border {
    border-radius: 0.375rem;
    padding: 0.5rem;
    transition: all 0.3s ease;
  }

  @keyframes fadeInVariants {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .variant-row-container {
    margin-bottom: 1rem;
  }

  .variant-header-row {
    background-color: #fff !important;
    border: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
    padding: 16px 20px;
    transition: all 0.2s ease;
  }

  .variant-header-row:hover {
    background-color: #f8f9fa !important;
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
  }

  .variant-label {
    flex-grow: 1;
  }

  .variant-label .fw-bold {
    font-size: 14px;
    color: #007bff;
    font-weight: 600;
  }

  .variant-details {
    background-color: #fff;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 8px 8px;
    /* padding: 20px; */
    margin-top: -1px;
  }

  .variant-image-upload {
    background-color: #f8f9fa;
    border: 2px dashed #dee2e6 !important;
    border-radius: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .variant-image-upload:hover {
    border-color: #007bff !important;
    background-color: rgba(0, 123, 255, 0.05);
  }

  .variant-image-upload .text-muted {
    color: #6c757d;
  }

  .variant-image-upload i {
    font-size: 24px;
  }

  /* Form controls inside variants */
  .variant-details .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
    font-size: 13px;
  }

  .variant-details .form-control,
  .variant-details .form-select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
  }

  .variant-details .form-control:focus,
  .variant-details .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .variant-details .input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
    font-weight: 500;
    font-size: 14px;
  }

  /* Switch toggle style */
  .form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
  }

  .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  /* Variants header controls */
  .variants-header-controls h6 {
    font-size: 16px;
    color: #495057;
    font-weight: 600;
  }

  /* Bulk edit section styling */
  .bulk-edit-section {
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
  }

  .bulk-edit-section .form-label.fw-semibold {
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 13px;
  }

  .bulk-edit-section .form-control,
  .bulk-edit-section .form-select {
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 14px;
  }

  .bulk-edit-section .btn {
    font-size: 14px;
    font-weight: 500;
  }

  /* Modal styles */
  #bulkEditModal {
    z-index: 9999;
    backdrop-filter: blur(4px);
    background-color: rgba(0, 0, 0, 0.5);
  }

  #bulkEditModal .modal-content {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
  }

  #bulkEditModal .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 12px 12px 0 0;
  }

  #bulkEditModal .modal-title {
    font-weight: 600;
    color: #495057;
  }

  #bulkEditModal .btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
  }

  /* Badge styles */
  .badge.bg-primary {
    background-color: #007bff !important;
    font-size: 11px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 12px;
  }

  /* Button improvements */
  .btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 4px;
  }

  .drag-handle {
    cursor: grab;
    transition: all 0.2s ease;
  }

  .drag-handle:hover {
    background-color: #e9ecef;
    transform: translateY(-1px);
  }

  .drag-handle:active {
    cursor: grabbing;
  }

  /* Button improvements */
  .btn-group .btn {
    border-radius: 4px;
    margin-left: 4px;
  }

  .btn-group .btn:first-child {
    margin-left: 0;
  }

  .toggle-variant,
  .remove-variant {
    transition: all 0.2s ease;
  }

  .toggle-variant:hover,
  .remove-variant:hover {
    transform: translateY(-1px);
  }

  /* Form improvements inside variants */
  .variant-card-body .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
  }

  .variant-card-body .input-group-text {
    background-color: #f8f9fa;
    border-color: #ced4da;
    font-weight: 500;
  }

  .variant-card-body .form-control,
  .variant-card-body .form-select {
    border-color: #ced4da;
    transition: all 0.2s ease;
  }

  .variant-card-body .form-control:focus,
  .variant-card-body .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .variation-title {
    font-weight: 600;
    color: var(--color-text);
    font-size: 14px;
  }

  .drag-handle {
    cursor: grab;
    border: 1px solid #dee2e6 !important;
    background-color: var(--color-white) !important;
    transition: all var(--transition-fast);
  }

  .drag-handle:hover {
    background-color: var(--color-background) !important;
  }

  .drag-handle:active {
    cursor: grabbing;
  }

  .toggle-variation {
    transition: all var(--transition-fast);
  }

  .toggle-variation i {
    transition: transform var(--transition-fast);
  }

  .select-options-btn {
    width: 100%;
  }

  .variant-badge .badge {
    font-size: 11px;
    font-weight: 500;
  }

  #variationTemplate {
    min-width: 200px;
  }

  /* ================================================================= */
  /* Labels section with performance optimizations */
  .labels-section {
    border-top: 1px solid var(--color-border-light);
    /* padding-top: var(--spacing-sm); */
    /* margin-top: var(--spacing-lg); */
    contain: layout;
    will-change: auto;
  }

  /* Simplified approach - only disable specific problematic animations */
  .labels-section .transition-all,
  .labels-section .hover-lift {
    transition: none;
    transform: none;
  }

  .labels-section *:hover {
    transform: none;
  }

  /* Disabled - conflicts with labels-section no-animation rule */

  .label-input {
    flex: 1;
  }

  .labels-text .label-input {
    font-weight: 500;
  }

  .labels-color .label-row,
  .labels-image .label-row {
    min-height: 50px;
    align-items: center;
  }

  /* Color Input Styles */
  .color-input-group {
    width: 100%;
  }

  .color-picker {
    border: 1px solid #ddd;
    padding: 2px;
    cursor: pointer;
  }

  .color-picker::-webkit-color-swatch-wrapper {
    padding: 0;
  }

  .color-picker::-webkit-color-swatch {
    border: none;
    border-radius: 2px;
  }

  .color-name {
    min-width: 120px;
  }

  .color-hex {
    font-family: monospace;
    text-transform: uppercase;
  }

  /* Image Input Styles */
  .image-input-group {
    width: 100%;
  }

  .image-preview {
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
  }

  .image-preview:hover {
    border-color: var(--color-primary);
    background-color: var(--color-background-hover);
  }

  .image-preview img {
    transition: all var(--transition-fast);
  }

  .image-preview:hover img {
    transform: scale(1.05);
  }

  .image-name {
    min-width: 120px;
  }

  .select-image {
    white-space: nowrap;
  }

  /* ==========================================================================
     Responsive Design - Mobile-first approach
     ========================================================================== */

  /* Base mobile styles (< 576px) */
  .media-thumbnails-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-sm);
  }

  .variant-badges .badge {
    font-size: 10px;
    padding: 2px 6px;
  }

  .variant-card-body,
  .option-card-body {
    padding: var(--spacing-md);
  }

  .variant-card-header,
  .option-card-header {
    padding: var(--spacing-sm) var(--spacing-md);
  }

  /* Small devices (≥ 576px) */
  @media (min-width: 576px) {
    .media-thumbnails-grid {
      grid-template-columns: repeat(4, 1fr);
    }

    .variant-badges .badge {
      font-size: 11px;
      padding: 4px 8px;
    }
  }

  /* Medium devices (≥ 768px) */
  @media (min-width: 768px) {
    .product-media-container {
      flex-direction: row;
      gap: var(--spacing-xl);
    }

    .main-media-wrapper {
      max-width: 150px;
    }

    .variant-card-body,
    .option-card-body {
      padding: var(--spacing-lg);
    }

    .variant-card-header,
    .option-card-header {
      padding: var(--spacing-md) var(--spacing-lg);
    }
  }

  /* Mobile-specific overrides */
  @media (max-width: 767.98px) {
    .product-media-container {
      flex-direction: column;
    }

    .main-media-wrapper {
      max-width: 100%;
      margin-bottom: var(--spacing-xl);
    }

    .variation-card-header,
    .variant-card-header {
      flex-direction: column;
      align-items: flex-start !important;
      gap: var(--spacing-md);
    }

    .variation-card-header .ms-auto,
    .variant-card-header .ms-auto {
      margin-left: 0 !important;
      align-self: flex-end;
      width: 100%;
      display: flex;
      justify-content: flex-end;
      gap: var(--spacing-sm);
    }

    .variants-header .d-flex {
      flex-direction: column;
      gap: var(--spacing-md);
      align-items: stretch;
    }

    .variants-header .btn-group {
      display: flex;
      flex-wrap: wrap;
      gap: var(--spacing-sm);
    }

    .variants-header .btn {
      flex: 1;
      min-width: auto;
    }

    #variationTemplate {
      width: 100%;
      min-width: auto;
    }

    .option-card .row {
      --bs-gutter-x: 0.5rem;
    }

    .option-card .col-md-4,
    .option-card .col-md-3,
    .option-card .col-md-2,
    .variant-card .row .col-md-6,
    .variant-details .col-md-2,
    .variant-details .col-md-6 {
      margin-bottom: var(--spacing-md);
    }

    .option-card .text-end {
      text-align: center !important;
    }

    .variant-info {
      margin-bottom: var(--spacing-md);
    }

    .variant-badges {
      justify-content: flex-start;
    }

    #bulkEditPanel .row .col-md-4 {
      margin-bottom: var(--spacing-md);
    }

    #bulkEditPanel .d-flex {
      flex-direction: column;
      gap: var(--spacing-sm);
    }

    .variant-header-row {
      flex-direction: column;
      align-items: flex-start !important;
      gap: var(--spacing-md);
    }

    .variant-header-row .d-flex:last-child {
      width: 100%;
      justify-content: space-between;
    }

    .variant-image-upload {
      height: 80px !important;
    }

    .variants-section>.row:first-child .col-md-6 {
      margin-bottom: var(--spacing-md);
    }

    /* ==========================================================================
     Table Components - Compact styling
     ========================================================================== */

    /* Compact table styling */
    .compact-table tbody tr td {
      padding: var(--spacing-sm) var(--spacing-md);
      vertical-align: middle;
    }

    .compact-table .form-control {
      padding: var(--spacing-xs) var(--spacing-sm);
      font-size: 14px;
      height: auto;
      min-height: var(--form-control-height-sm);
    }

    .compact-table .form-group {
      margin-bottom: 0;
    }

    .compact-table .delete-row {
      padding: var(--spacing-xs) var(--spacing-sm);
      font-size: 12px;
    }

    /* Extra compact version */
    .extra-compact tbody tr td {
      padding: var(--spacing-xs) var(--spacing-sm);
      line-height: 1.2;
    }

    .extra-compact .form-control {
      padding: 2px 6px;
      font-size: 13px;
      min-height: 28px;
    }

    .extra-compact .form-group {
      margin-bottom: 0;
    }

    .extra-compact .delete-row {
      padding: 3px 6px;
      font-size: 11px;
      margin-top: var(--spacing-xs);
    }

    .custom-checkbox {
      margin-top: 35px;
    }

    /* ==========================================================================
     Animation & Interaction States
     ========================================================================== */

    /* Card body overflow for jQuery animations */
    .variation-card-body,
    .option-card-body,
    .attribute-row {
      overflow: hidden;
    }

    /* Enhanced animation states */
    .variation-card.animating {
      transform-origin: center;
      transition: transform var(--transition-slow);
    }

    .variation-card.bounce-effect {
      animation: bounceEffect 0.2s ease-out;
    }

    @keyframes bounceEffect {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.02);
      }
    }

    /* Toggle button animations */
    .toggle-variation i,
    .toggle-option i,
    #toggleAllVariations i,
    #toggleAllOptions i,
    #toggleAllAttributes i {
      transition: transform var(--transition-medium);
      transform-origin: center;
    }

    /* Disabled state during animation */
    .toggle-variation:disabled {
      opacity: 0.6;
      pointer-events: none;
    }

    /* Icon rotation states */
    .fa-chevron-up {
      transform: rotate(0deg);
    }

    .fa-chevron-down {
      transform: rotate(180deg);
    }

    /* Hover effects for toggle buttons */
    #toggleAllVariations,
    #toggleAllOptions,
    #toggleAllAttributes,
    .toggle-variation,
    .toggle-option {
      transition: all var(--transition-fast);
    }

    #toggleAllVariations:hover,
    #toggleAllOptions:hover,
    #toggleAllAttributes:hover,
    .toggle-variation:hover,
    .toggle-option:hover {
      background-color: var(--color-background);
      transform: translateY(-1px);
      box-shadow: 0 2px 4px var(--color-shadow);
    }

    /* ==========================================================================
     Utility Classes
     ========================================================================== */

    .hidden {
      display: none !important;
    }

    /* Button utilities */
    .btn-remove {
      background: none;
      border: none;
      color: var(--color-danger);
      padding: 0.375rem;
      transition: color var(--transition-fast);
    }

    .btn-remove:hover {
      color: var(--color-danger-hover);
    }

    .add-file-btn {
      background: none;
      border: none;
      color: var(--color-primary);
      padding: 0.5rem 0;
      transition: all var(--transition-fast);
    }

    .add-file-btn:hover {
      color: var(--color-primary-hover);
      text-decoration: underline;
    }

    /* Bootstrap collapse animations */
    .collapsing {
      position: relative;
      height: 0;
      overflow: hidden;
      transition: height 0.35s ease;
    }

    .collapse:not(.show) {
      display: none;
    }

    .collapse.show {
      display: block;
    }
</style>
