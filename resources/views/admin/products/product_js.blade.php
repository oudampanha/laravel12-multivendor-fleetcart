  <script>
    let fileCounter = 1;

    function handleFileSelect(index) {
      const fileInput = document.getElementById(`fileInput${index}`);
      const fileItem = document.querySelector(`[data-file-index="${index}"]`);
      const fileNameInput = fileItem.querySelector('.file-name');

      if (fileInput.files && fileInput.files[0]) {
        const fileName = fileInput.files[0].name;
        fileNameInput.value = fileName;
      }
    }

    function removeFileItem(index) {
      const fileItem = document.querySelector(`[data-file-index="${index}"]`);
      if (fileItem) {
        fileItem.remove();
      }
    }

    function addNewFileItem() {
      const container = document.getElementById('fileItemsContainer');
      const newFileRow = document.createElement('div');
      newFileRow.className = 'mb-3';
      newFileRow.setAttribute('data-file-index', fileCounter);

      newFileRow.innerHTML = `
                <div class="card bg-white border">
                    <div class="card-body py-3">
                        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                            <!-- Drag Handle -->
                            <div class="d-flex align-items-center justify-content-center align-self-center" style="min-width: 30px;">
                                <i class="fas fa-bars text-muted"></i>
                            </div>

                            <!-- File Name Input -->
                            <div class="flex-grow-1 w-100 w-sm-auto">
                                <input type="text" class="form-control bg-transparent file-name" value="" readonly placeholder="Choose a file...">
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-sm-auto">
                                <input type="file" class="d-none file-input" id="fileInput${fileCounter}" onchange="handleFileSelect(${fileCounter})">
                                <button type="button" class="btn btn-light border w-100 w-sm-auto" onclick="document.getElementById('fileInput${fileCounter}').click()">
                                    <i class="fas fa-folder-open me-1"></i>Choose File
                                </button>
                                <button type="button" class="btn btn-outline-danger w-100 w-sm-auto" onclick="removeFileItem(${fileCounter})">
                                    <i class="fas fa-trash me-1"></i>Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

      container.appendChild(newFileRow);
      fileCounter++;
    }

    // Initialize components on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize flatpickr for special price date fields
      if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr-input', {
          dateFormat: "Y-m-d H:i",
          enableTime: true,
          time_24hr: true,
          allowInput: true,
        });
      }

    });

    $(function() {
      'use strict';

      // Add CSS for bulk edit highlighting
      const style = document.createElement('style');
      style.textContent = `
          .highlight-changed {
            background-color: #fff3cd !important;
            border-color: #ffc107 !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
            transition: all 0.3s ease;
          }
        `;
      document.head.appendChild(style);

      // Add custom easing function if not available
      if (!$.easing.easeInOutCubic) {
        $.easing.easeInOutCubic = function(x, t, b, c, d) {
          if ((t /= d / 2) < 1) return c / 2 * t * t * t + b;
          return c / 2 * ((t -= 2) * t * t + 2) + b;
        };
      }
      // Configuration
      const CONFIG = {
        MAX_GALLERY_IMAGES: 8,
        PLACEHOLDER_IMAGE: '{{ asset('assets/images/placeholder_image.png') }}',
        ENDPOINTS: {
          productSearch: '{{ route('admin.products.search') }}',
          media: {
            list: '{{ route('admin.media.list') }}',
            upload: '{{ route('admin.media.upload') }}',
            bulkUpload: '{{ route('admin.media.bulk-upload') }}',
            createFolder: '{{ route('admin.media.create-folder') }}',
            renameFolder: '{{ route('admin.media.rename-folder') }}',
            deleteFolder: '{{ route('admin.media.delete-folder') }}',
            renameFile: '{{ route('admin.media.rename-file') }}',
            deleteFile: '{{ route('admin.media.delete', ':id') }}',
            moveToFolder: '{{ route('admin.media.move-to-folder') }}',
            copyToFolder: '{{ route('admin.media.copy-to-folder') }}',
            bulkMoveToFolder: '{{ route('admin.media.bulk-move-to-folder') }}',
            bulkCopyToFolder: '{{ route('admin.media.bulk-copy-to-folder') }}',
            getFolders: '{{ route('admin.media.folders') }}'
          }
        }
      };

      // State management
      const state = {
        attributeIndex: 1,
        variationIndex: 1,
        optionIndex: 1,
        featuredImageId: null,
        galleryImages: []
      };

      // Cache DOM elements
      const $elements = {
        name: $('#name'),
        slug: $('#slug'),
        description: $('#description'),
        price: $('#price'),
        inventoryManagement: $('#inventory_management'),
        stockAvailabilityGroup: $('#stockAvailabilityGroup'),
        quantityGroup: $('#quantityGroup'),
        attributesContainer: $('#attributesContainer'),
        variationsContainer: $('#variationsContainer'),
        optionsContainer: $('#optionsContainer'),
        generatedVariants: $('#generatedVariants'),
        variantsPlaceholder: $('#variantsPlaceholder'),
        mainProductImage: $('#mainProductImage'),
        mainImageHolder: $('#mainImageHolder'),
        removeMainImage: $('#removeMainImage'),
        featuredImageInput: $('#featuredImageInput'),
        galleryImagesInput: $('#galleryImagesInput'),
        mediaThumbnailsGrid: $('#mediaThumbnailsGrid'),
        productForm: $('#productForm')
      };

      // Initialize Select2
      function initializeSelect2() {
        $('#categories, #brand_id, #tax_class_id, #vendor_id').select2({
          placeholder: 'Please Select',
          allowClear: true
        });

        $('#up_sells, #cross_sells, #related_products').select2({
          placeholder: 'Search and select products...',
          allowClear: true,
          multiple: true,
          ajax: {
            url: CONFIG.ENDPOINTS.productSearch,
            dataType: 'json',
            delay: 250,
            data: params => ({
              q: params.term,
              page: params.page
            }),
            processResults: (data, params) => {
              params.page = params.page || 1;
              return {
                results: data.items,
                pagination: {
                  more: (params.page * 30) < data.total_count
                }
              };
            },
            cache: true
          },
          templateResult: product => {
            if (product.loading) return product.text;
            return $(`<span>${product.name} <small class="text-muted">($${product.price})</small></span>`);
          },
          templateSelection: product => product.name || product.text
        });
      }

      // Initialize Summernote
      function initializeSummernote() {
        $elements.description.summernote({
          height: 250,
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ]
        });
      }

      // Slug generation
      function generateSlug(text) {
        return text.toLowerCase()
          .replace(/[^\w ]+/g, '')
          .replace(/ +/g, '-');
      }

      // Event handlers
      function bindEventHandlers() {
        // Auto-generate slug
        $elements.name.on('input', function() {
          $elements.slug.val(generateSlug($(this).val()));
        });

        // Inventory management toggle
        $elements.inventoryManagement.on('change', function() {
          const trackInventory = $(this).val() === '1';
          $elements.stockAvailabilityGroup.add($elements.quantityGroup).toggle(trackInventory);
        });

        // Form validation
        $elements.productForm.on('submit', function(e) {
          const errors = [];

          if (!$elements.name.val().trim()) {
            errors.push('Product name is required');
            $elements.name.addClass('is-invalid');
          }

          if (!$elements.price.val() || parseFloat($elements.price.val()) <= 0) {
            errors.push('Valid price is required');
            $elements.price.addClass('is-invalid');
          }

          if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n' + errors.join('\n'));
            return false;
          }
        });

        // Remove validation errors on input
        $('.form-control').on('input change', function() {
          $(this).removeClass('is-invalid');
        });
      }

      // Attributes management
      const AttributeManager = {
        add() {
          const html = `
            <div class="attribute-row d-flex align-items-center mb-2" data-attribute-index="${state.attributeIndex}">
              <button type="button" class="btn btn-sm btn-link text-dark p-1 mr-2 drag-handle">
                <i class="fas fa-grip-vertical"></i>
              </button>
              <div class="row flex-grow-1">
                <div class="col-md-5">
                  <select class="form-control" name="attributes[${state.attributeIndex}][attribute_id]">
                    <option value="">Please Select</option>
                    @foreach ($attributes as $attribute)
                      <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') ?? $attribute->slug }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-7">
                  <input type="text" class="form-control" name="attributes[${state.attributeIndex}][values]" placeholder="">
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-attribute">
                <i class="fas fa-trash"></i>
              </button>
            </div>`;
          $elements.attributesContainer.append(html);
          state.attributeIndex++;
        },

        remove($button) {
          const $row = $button.closest('.attribute-row');
          $row.fadeOut(300, function() {
            $(this).remove();
          });
        }
      };

      // Variations management
      const VariationManager = {
        add(templateData = null) {
          const data = templateData || {
            name: 'New Variation',
            type: ''
          };
          const html = this.generateHTML(state.variationIndex, data);
          $elements.variationsContainer.append(html);
          state.variationIndex++;
        },

        generateHTML(index, data) {
          return `
            <div class="card border mb-3" data-variation-index="${index}">
              <div class="card-header bg-light border-0 py-2" id="headingVariation${index}">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-link text-dark p-1 mr-2 drag-handle">
                      <i class="fas fa-grip-vertical"></i>
                    </button>
                    <span class="variation-title font-weight-normal">${data.name}</span>
                  </div>
                  <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-link text-danger p-1 mr-2 remove-variation">
                      <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-link text-dark p-1"
                      data-toggle="collapse" data-target="#collapseVariation${index}"
                      aria-expanded="true" aria-controls="collapseVariation${index}">
                      <i class="fas fa-chevron-up"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div id="collapseVariation${index}" class="collapse show" aria-labelledby="headingVariation${index}">
                <div class="card-body pt-3 pb-2">
                  ${this.generateBodyHTML(index, data)}
                </div>
              </div>
            </div>`;
        },

        generateBodyHTML(index, data) {
          return `
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label class="form-label">Name</label>
                      <input type="text" class="form-control variation-name" name="variations[${index}][name]" value="${data.name}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group mb-3">
                      <label class="form-label">Type</label>
                      <select class="form-control variation-type" name="variations[${index}][type]">
                        <option value="">Please Select</option>
                        <option value="text" ${data.type === 'text' ? 'selected' : ''}>Text</option>
                        <option value="color" ${data.type === 'color' ? 'selected' : ''}>Color</option>
                        <option value="image" ${data.type === 'image' ? 'selected' : ''}>Image</option>
                      </select>
                    </div>
                  </div>
                </div>
            ${this.generateLabelsSection(index)}`;
        },

        generateLabelsSection(index) {
          return `
                <div class="labels-section" style="display: none;">
                  <div class="form-group mb-3">
                    <label class="form-label">Label <span class="text-danger">*</span></label>
                    ${this.generateLabelContainers(index)}
                    <button type="button" class="btn btn-sm btn-secondary add-label-row">Add Row</button>
                  </div>
                </div>`;
        },

        generateLabelContainers(index) {
          const types = ['text', 'color', 'image'];
          return types.map(type => this.generateLabelContainer(index, type)).join('');
        },

        generateLabelContainer(index, type) {
          const containerClass = `labels-container labels-${type}`;
          let content = '';

          switch (type) {
            case 'text':
              content = this.generateTextLabel(index);
              break;
            case 'color':
              content = this.generateColorLabel(index);
              break;
            case 'image':
              content = this.generateImageLabel(index);
              break;
          }

          return `<div class="${containerClass}" style="display: none;">${content}</div>`;
        },

        generateTextLabel(index) {
          return `
                <div class="label-row d-flex align-items-center mb-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                    <i class="fas fa-grip-vertical"></i>
                  </button>
                  <input type="text" class="form-control label-input flex-grow-1" name="variations[${index}][labels][]" placeholder="Enter text value (e.g., Small)">
                  <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-label">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                `;
        },

        generateColorLabel(index) {
          return `
                <div class="label-row d-flex align-items-center mb-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary drag-handle me-2">
                    <i class="fas fa-grip-vertical"></i>
                  </button>
                  <div class="color-input-group d-flex align-items-center flex-1">
                    <input type="color" class="form-control form-control-color color-picker me-2" value="#000000" style="width: 50px; height: 38px;">
                    <input type="text" class="form-control color-name me-2" placeholder="Color name" style="flex: 1;">
                    <input type="text" class="form-control color-hex label-input" name="variations[${index}][labels][]" placeholder="#000000" style="width: 100px;">
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-label">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>`;
        },

        generateImageLabel(index) {
          return `
                <div class="label-row d-flex align-items-center mb-2">
                  <button type="button" class="btn btn-sm btn-outline-secondary drag-handle me-2">
                    <i class="fas fa-grip-vertical"></i>
                  </button>
                  <div class="image-input-group d-flex align-items-center flex-1">
                    <div class="image-preview me-2" style="width: 50px; height: 38px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                      <i class="fas fa-image text-muted"></i>
                    </div>
                    <input type="text" class="form-control image-name me-2" placeholder="Image name" style="flex: 1;">
                    <input type="hidden" class="image-id label-input" name="variations[${index}][labels][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-primary select-image me-1">
                      <i class="fas fa-image"></i>
                    </button>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger ms-2 remove-label">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>`;
        },

        remove($card) {
          if (confirm('Are you sure you want to remove this variation?')) {
            $card.fadeOut(300, function() {
              $(this).remove();
              updateProductVariants();
            });
          }
        },

        toggle($button) {
          const targetId = $button.data('target');
          const $target = $('#' + targetId);
          const $icon = $button.find('i');
          const $card = $button.closest('.card[data-variation-index]');

          // Prevent multiple clicks during animation
          if ($target.is(':animated') || $button.prop('disabled')) {
            return;
          }

          // Disable button during animation
          $button.prop('disabled', true);

          // Add animation classes for enhanced effects
          $card.addClass('animating');

          // Enhanced slideToggle with custom easing and icon rotation
          $target.slideToggle({
            duration: 450,
            easing: 'easeInOutCubic',
            start: function() {
              // Rotate icon smoothly during slide
              const isVisible = $(this).is(':visible');
              $icon.css('transform', isVisible ? 'rotate(180deg)' : 'rotate(0deg)');
            },
            progress: function(animation, progress) {
              // Add subtle scale effect during animation
              const scale = 1 - (progress * 0.02);
              $card.css('transform', `scale(${scale})`);
            },
            complete: function() {
              const isVisible = $(this).is(':visible');

              // Update icon classes
              $icon.toggleClass('fa-chevron-up', isVisible)
                .toggleClass('fa-chevron-down', !isVisible);

              // Reset transform and remove animation class
              $card.css('transform', 'scale(1)').removeClass('animating');

              // Re-enable button
              $button.prop('disabled', false);

              // Add slight bounce effect on completion
              $card.addClass('bounce-effect');
              setTimeout(() => {
                $card.removeClass('bounce-effect');
              }, 200);
            }
          });
        }
      };

      // Options management
      const OptionManager = {
        add() {
          const html = `
                  <div class="card border mb-3" data-option-index="${state.optionIndex}">
                    <div class="card-header bg-light border-0 py-2" id="headingOption${state.optionIndex}">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-dark p-1 mr-2 drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                          </button>
                          <span class="option-title font-weight-normal">New Option</span>
                        </div>
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-danger p-1 mr-2 remove-option">
                            <i class="fas fa-trash"></i>
                          </button>
                          <button type="button" class="btn btn-link text-dark p-1"
                            data-toggle="collapse" data-target="#collapseOption${state.optionIndex}"
                            aria-expanded="true" aria-controls="collapseOption${state.optionIndex}">
                            <i class="fas fa-chevron-up"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div id="collapseOption${state.optionIndex}" class="collapse show" aria-labelledby="headingOption${state.optionIndex}">
                      <div class="card-body pt-3 pb-2">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label d-block mb-2">Name</label>
                            <input type="text" class="form-control option-name" name="options[${state.optionIndex}][name]"
                              placeholder="">
                          </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label d-block mb-2">Type</label>
                            <select class="form-control option-type" name="options[${state.optionIndex}][type]">
                              <option value="">Please Select</option>
                              <optgroup label="Text">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                              </optgroup>
                              <optgroup label="Select">
                                <option value="dropdown">Dropdown</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="check_custom">Custom Check</option>
                                <option value="radio">Radio Button</option>
                                <option value="radio_custom">Custom Radio Button</option>
                                <option value="multiple_select">Multiple Select</option>
                              </optgroup>
                              <optgroup label="Date">
                                <option value="date">Date</option>
                                <option value="date_time">Date & Time</option>
                                <option value="time">Time</option>
                              </optgroup>
                            </select>
                          </div>
                          <div class="col-md-2 mb-3">
                            <label class="form-label d-block mb-2">&nbsp;</label>
                            <div class="form-check">
                              <input type="checkbox" class="form-check-input" id="option${state.optionIndex}Required" name="options[${state.optionIndex}][required]" value="1">
                              <label class="form-check-label" for="option${state.optionIndex}Required">
                                Required
                              </label>
                            </div>
                          </div>
                        </div>

                      <!-- Option Values Section for Text types (initially hidden) -->
                      <div class="option-values-text" style="display: none;">
                        <div class="row">
                          <div class="col-md-8 mb-3">
                            <label class="form-label d-block mb-2">Price</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                              </div>
                              <input type="number" step="0.01" class="form-control" name="options[${state.optionIndex}][price]" placeholder="0.00">
                            </div>
                          </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label d-block mb-2">Price Type</label>
                            <select class="form-control" name="options[${state.optionIndex}][price_type]">
                              <option value="fixed">Fixed</option>
                              <option value="percent">Percent</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- Option Values Section for Select types (initially hidden) -->
                      <div class="option-values-select" style="display: none;">
                        <div class="mb-3">
                          <div class="row mb-2">
                            <div class="col-md-5">
                              <label class="form-label d-block mb-2">Label <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label d-block mb-2">Price</label>
                            </div>
                            <div class="col-md-3">
                              <label class="form-label d-block mb-2">Price Type</label>
                            </div>
                          </div>
                          <div class="option-values-container">
                            <!-- Option value rows will be added here -->
                            <div class="option-value-row d-flex align-items-center mb-2">
                              <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                                <i class="fas fa-grip-vertical"></i>
                              </button>
                              <div class="row flex-grow-1">
                                <div class="col-md-5">
                                  <input type="text" class="form-control" name="options[${state.optionIndex}][values][0][label]" placeholder="">
                                </div>
                                <div class="col-md-4">
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="0.01" class="form-control" name="options[${state.optionIndex}][values][0][price]" placeholder="0.00">
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <select class="form-control" name="options[${state.optionIndex}][values][0][price_type]">
                                    <option value="fixed">Fixed</option>
                                    <option value="percent">Percent</option>
                                  </select>
                                </div>
                              </div>
                              <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-option-value">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </div>
                          <button type="button" class="btn btn-sm btn-secondary add-option-value">Add Row</button>
                        </div>
                      </div>

                      <!-- Option Values Section for Date/Time types (initially hidden) -->
                      <div class="option-values-datetime" style="display: none;">
                        <div class="row">
                          <div class="col-md-8 mb-3">
                            <label class="form-label d-block mb-2">Price</label>
                            <div class="input-group">
                              <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                              </div>
                              <input type="number" step="0.01" class="form-control" name="options[${state.optionIndex}][price]" placeholder="0.00">
                            </div>
                          </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label d-block mb-2">Price Type</label>
                            <select class="form-control" name="options[${state.optionIndex}][price_type]">
                              <option value="fixed">Fixed</option>
                              <option value="percent">Percent</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      </div>
                    </div>
                  </div>`;
          $elements.optionsContainer.append(html);
          state.optionIndex++;
        },

        remove($card) {
          if (confirm('Are you sure you want to remove this option?')) {
            $card.fadeOut(300, function() {
              $(this).remove();
            });
          }
        }
      };

      // Variant generation
      function updateProductVariants() {
        const variations = collectVariations();
        if (variations.length === 0) {
          $elements.generatedVariants.empty();

          // Check if placeholder actually exists in DOM (not cached reference)
          if ($('#variantsPlaceholder').length > 0) {
            $('#variantsPlaceholder').show();
          } else {
            // Recreate the placeholder element
            const placeholderHtml = `
              <div id="variantsPlaceholder" class="alert alert-info d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <span>Please add some variations to generate variants.</span>
              </div>
            `;

            // Insert the placeholder before the generated variants container
            $elements.generatedVariants.before(placeholderHtml);

            // Update the cached element reference
            $elements.variantsPlaceholder = $('#variantsPlaceholder');
          }
          return;
        }

        $elements.variantsPlaceholder.hide();
        const variants = generateVariantCombinations(variations);

        if (variants.length === 0) {
          $elements.generatedVariants.html(
            '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No variants generated. Please add labels to your variations.</div>'
          );
          return;
        }

        renderVariants(variants);
      }

      function collectVariations() {
        const variations = [];

        $('.card[data-variation-index]').each(function() {
          const $card = $(this);
          const name = $card.find('.variation-name').val();
          const type = $card.find('.variation-type').val();
          const labels = [];

          if (name && type) {
            $card.find('.label-input:visible').each(function() {
              const value = $(this).val().trim();
              if (value) labels.push(value);
            });

            if (labels.length > 0) {
              variations.push({
                name,
                type,
                labels
              });
            }
          }
        });

        return variations;
      }

      function generateVariantCombinations(variations) {
        if (variations.length === 0) return [];

        let combinations = [{}];

        variations.forEach(variation => {
          const newCombinations = [];
          combinations.forEach(combination => {
            variation.labels.forEach(label => {
              newCombinations.push({
                ...combination,
                [variation.name]: label
              });
            });
          });
          combinations = newCombinations;
        });

        return combinations;
      }

      function generateVariantId(str) {
        let hash = 0;
        if (str.length === 0) return hash.toString(36);

        for (let i = 0; i < str.length; i++) {
          const char = str.charCodeAt(i);
          hash = ((hash << 5) - hash) + char;
          hash = hash & hash;
        }

        return Math.abs(hash).toString(36);
      }

      function renderVariants(variants) {
        const variantIds = variants.map((variant, index) => {
          const variantStr = Object.values(variant).join('').toLowerCase().replace(/\s+/g, '');
          return generateVariantId(variantStr);
        });

        const html = generateVariantsHTML(variants, variantIds);
        $elements.generatedVariants.html(html);

        // Remove placeholder completely when variants are successfully generated
        if (html && html.trim() !== '') {
          // Completely remove the placeholder element from the DOM
          $elements.variantsPlaceholder.remove();
          $('#variantsPlaceholder').remove();

        }

        // Initialize date pickers if available
        if (typeof flatpickr !== 'undefined') {
          $elements.generatedVariants.find('.flatpickr-input').each(function() {
            flatpickr(this, {
              dateFormat: "Y-m-d",
              allowInput: true
            });
          });
        }

        // Double-check placeholder visibility after rendering
        setTimeout(() => {
          if ($elements.generatedVariants.children().length > 0) {
            $elements.variantsPlaceholder.hide().css('display', 'none !important');
          }
        }, 100);
      }

      function generateVariantsHTML(variants, variantIds) {
        let html = '';

        // Generate the exact 5-section layout from reference image
        if (variants.length > 0) {
          html = `
                <!-- Variants Section with Integrated Bulk Edit Controls -->
                <div class="variants-section bg-white rounded">
                  <!-- Section Header -->
                  <div class="card mb-4">
                     <div class="card-body px-5 py-3" id="variantsBulkEditSection">

                      <!-- Step 1: Default Variant Selection -->
                      <div class="row mb-3 align-items-center">
                        <div class="col-md-2">
                          <label class="form-label mb-0"><strong>Default Variant</strong></label>
                        </div>
                        <div class="col-md-5">
                          <select class="form-control" id="defaultVariantSelect">
                            ${variants.map((variant, index) =>
                              `<option value="${Object.values(variant).join('-')}">${Object.values(variant).join(' - ')}</option>`
                            ).join('')}
                          </select>
                        </div>
                      </div>

                      <!-- Step 2: Variation Values List -->
                      <div class="row mb-3 align-items-center">
                        <div class="col-md-2">
                          <label class="form-label mb-0"><strong>Bulk Edit</strong></label>
                        </div>
                        <div class="col-md-5">
                          <select class="form-control" id="variation-values-list" name="variation-values-list">
                            <option value="">Please Select</option>
                            <option value="all">All Variants</option>
                            ${variants.map((variant, index) =>
                              `<option value="${index}">${Object.values(variant).join(' - ')}</option>`
                            ).join('')}
                          </select>
                        </div>
                      </div>

                      <!-- Step 3: Field Type Selection -->
                      <div class="row mb-3 align-items-center" id="bulkEditVariantsFieldTypeSection" style="display: none;">
                        <div class="col-md-2">
                          <label for="bulk_edit_variants_field_type" class="form-label mb-0"><strong>Field Type</strong></label>
                        </div>
                        <div class="col-md-5">
                          <select class="form-control" id="bulk_edit_variants_field_type" name="bulk_edit_variants_field_type">
                            <option value="">Please Select</option>
                            <option value="is_active">Status</option>
                            <option value="media">Media</option>
                            <option value="sku">SKU</option>
                            <option value="price">Price</option>
                            <option value="special_price">Special Price</option>
                            <option value="manage_stock">Inventory Management</option>
                            <option value="in_stock">Stock Available</option>
                          </select>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
               <!-- Individual Variant Cards Section -->
            `;
          // Generate individual variant cards matching the reference image
          variants.forEach((variant, index) => {
            const variantId = variantIds[index];
            const variantLabel = Object.values(variant).join(' - ').toUpperCase();
            const isDefaultVariant = index === 0 ? 'Default' : '';
            html += `
                <div class="variants-group">
                  <div class="card mb-4" data-variant-id="${variantId}" data-variant-label="${variantLabel}" data-variant-index="${index}">
                    <!-- Variant Header Row -->
                    <div class="card-header d-flex justify-content-between align-items-center py-1 px-3 border-bottom">
                      <!-- Left Side: Variant Name and Badge -->
                      <div class="d-flex align-items-center gap-2">
                        <h5 class="card-title mb-0 fw-semibold text-dark">${variantLabel}</h5>
                        <span class="badge bg-primary text-white px-2 py-1 rounded">${isDefaultVariant}</span>
                      </div>

                      <!-- Right Side: Toggle Switch and Chevron -->
                      <div class="d-flex align-items-center gap-3">
                        <!-- Toggle Switch -->
                        <div class="custom-control custom-switch my-1 mx-2">
                          <input class="custom-control-input" type="checkbox" id="variantActiveSwitch${index}" checked
                            style="width: 3rem; height: 1.5rem;">
                          <label class="custom-control-label visually-hidden" for="variantActiveSwitch${index}"></label>
                        </div>

                        <!-- Collapse Toggle Button -->
                        <button type="button" class="btn btn-link p-0 text-muted border-0 bg-transparent toggle-variant-details"
                          aria-expanded="false" data-target="variantDetails${index}">
                          <i class="fas fa-chevron-down fs-5"></i>
                        </button>
                      </div>
                    </div>

                    <!-- Variant Details (Collapsed by default) -->
                    <div class="variant-details" id="variantDetails${index}" style="display: none;">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="product-media-grid">
                              <div class="media-grid-item media-picker disabled">
                                <div class="image-holder">
                                  <img src="../../assets/images/placeholder_image.png" class="placeholder-image"
                                    alt="Placeholder Image">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-8">
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group"><label for="variants-sku">SKU</label>
                                  <input type="text" name="variants[${index}][sku]" id="variants-sku" class="form-control">
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-price">Price <span class="text-red">*</span></label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="variants[${index}][price]" min="0" step="0.1"
                                      id="variants-price" class="form-control" placeholder="0.00" required>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-special-price">Special Price</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" name="variants[${index}][special_price]" min="0" step="0.1"
                                      id="variants-special-price" class="form-control" placeholder="0.00">
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-special-price-type">Special Price Type</label>
                                  <select name="variants[${index}][special_price_type]" id="variants-special-price-type"
                                    class="form-control custom-select-black">
                                    <option value="fixed">Fixed</option>
                                    <option value="percent">Percent</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-special-price-start">Special Price Start</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                                    </div>
                                    <input type="text" data-input="true"  name="variants[${index}][special_price_start]"
                                      id="variants-special-price-start" class="form-control flatpickr-input"
                                      readonly="readonly">
                                  </div>
                                </div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-special-price-end">Special PriceEnd</label>
                                  <div class="input-group">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                                    </div>
                                    <input type="text" data-input="true" name="variants[${index}][special_price_end]"
                                      id="variants-special-price-end" class="form-control flatpickr-input"
                                      readonly="readonly">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-manage-stock">Inventory Management</label>
                                  <select name="variants[${index}][manage_stock]" id="variants-manage-stock"
                                    class="form-control custom-select-black">
                                    <option value="0">Don't Track Inventory</option>
                                    <option value="1">Track Inventory</option>
                                  </select></div>
                              </div>
                              <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="variants-in-stock">Stock Availability</label>
                                    <select name="variants[${index}][in_stock]" id="variants-in-stock"
                                    class="form-control custom-select-black">
                                    <option value="0">Out of Stock</option>
                                    <option value="1">In Stock</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                      <!-- Variation Values (Hidden fields) -->
                        ${Object.entries(variant).map(([key, value]) =>
                          `<input type="hidden" name="variants[${index}][variations][${key}]" value="${value}">`
                        ).join('')}
                      </div>
                    </div>
                  </div>
                </div>
                `;
          });

          html += `</div>`;
          return html;
        }

        return '';
      }

      // Media management
      const MediaManager = {
        create(options) {
          const modalId = 'mediaManagerModal';
          this.removeExistingModal(modalId);
          this.createModal(modalId);
          this.initializeManager(modalId, options);
          this.showModal(modalId);
        },

        removeExistingModal(id) {
          $('#' + id).remove();
        },

        createModal(id) {
          const html = `
                  <div class="modal fade" id="${id}" tabindex="-1" style="z-index: 9999;">
                    <div class="modal-dialog modal-xl">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">Media Manager</h5>
                          <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div id="mediaManagerContainer" style="min-height: 500px;"></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                          <button type="button" class="btn btn-primary" id="selectMediaFiles">Select</button>
                        </div>
                      </div>
                    </div>
                  </div>`;
          $('body').append(html);
        },

        initializeManager(modalId, options) {
          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            return;
          }

          const manager = new window.MediaManager({
            container: document.getElementById('mediaManagerContainer'),
            multiple: options.multiple || false,
            defaultView: 'list',
            endpoints: CONFIG.ENDPOINTS.media,
            onSelect: options.onSelect || function() {}
          });

          $('#selectMediaFiles').off('click').on('click', function() {
            const selectedFiles = manager.selectedFiles || [];
            if (options.onSelect) {
              options.onSelect(selectedFiles);
            }
            $('#' + modalId).modal('hide');
          });

          return manager;
        },

        showModal(id) {
          $('#' + id).modal('show');
        }
      };

      // Gallery management
      const GalleryManager = {
        addThumbnail(file) {
          const html = `
                  <div class="media-thumbnail-item" data-file-id="${file.id}">
                    <div class="thumbnail-holder">
                      <img src="${file.url}" alt="Product thumbnail" class="thumbnail-image">
                      <button type="button" class="btn remove-thumbnail" title="Remove image">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>`;
          $('.add-new-media').before(html);
        },

        removeThumbnail(fileId) {
          state.galleryImages = state.galleryImages.filter(img => img.id !== fileId);
          $(`.media-thumbnail-item[data-file-id="${fileId}"]`).fadeOut(300, function() {
            $(this).remove();
          });
          this.updateInput();
          this.updateAddButton();
        },

        updateInput() {
          const imageIds = state.galleryImages.map(img => img.id).join(',');
          $elements.galleryImagesInput.val(imageIds);
        },

        updateAddButton() {
          const $addButton = $('.add-new-media');
          const remaining = CONFIG.MAX_GALLERY_IMAGES - state.galleryImages.length;

          if (remaining <= 0) {
            $addButton.addClass('disabled').css('pointer-events', 'none');
          } else {
            $addButton.removeClass('disabled').css('pointer-events', 'auto')
              .find('.add-media-content').html(`
                      <i class="fas fa-plus text-muted mb-2"></i>
                      <span class="text-muted small">Add Images</span>
                      <span class="text-muted small d-block">${remaining} remaining</span>
                    `);
          }
        },

        setMainImage(file) {
          $elements.mainProductImage.attr('src', file.url);
          $elements.mainImageHolder.addClass('has-image');
          $elements.removeMainImage.show();
          $elements.featuredImageInput.val(file.id);
          state.featuredImageId = file.id;
          $elements.mediaThumbnailsGrid.fadeIn(300);
        },

        clearMainImage() {
          $elements.mainProductImage.attr('src', CONFIG.PLACEHOLDER_IMAGE);
          $elements.mainImageHolder.removeClass('has-image');
          $elements.removeMainImage.hide();
          $elements.featuredImageInput.val('');
          state.featuredImageId = null;
          $elements.mediaThumbnailsGrid.fadeOut(300);

          // Clear gallery
          state.galleryImages = [];
          $('.media-thumbnail-item:not(.add-new-media)').remove();
          this.updateInput();
          this.updateAddButton();
        }
      };

      // Event delegation
      $(document)
        // Attributes
        .on('click', '#addAttribute', () => AttributeManager.add())
        .on('click', '.remove-attribute', function() {
          AttributeManager.remove($(this));
        })
        // Variations
        .on('click', '#addVariation', () => VariationManager.add())
        .on('click', '.remove-variation', function() {
          VariationManager.remove($(this).closest('.card[data-variation-index]'));
        })
        .on('click', '.toggle-variation', function() {
          VariationManager.toggle($(this));
        })
        .on('input', '.variation-name', function() {
          const title = $(this).val() || 'New Variation';
          $(this).closest('.card[data-variation-index]').find('.variation-title').text(title);
        })
        .on('change', '.variation-type', function() {
          const $card = $(this).closest('.card[data-variation-index]');
          const $labelsSection = $card.find('.labels-section');
          const type = $(this).val();

          $card.find('.labels-container').hide();

          if (type) {
            $labelsSection.slideDown(300);
            $card.find(`.labels-${type}`).show();
            updateProductVariants();
          } else {
            $labelsSection.slideUp(300);
            updateProductVariants();
          }
        })
        .on('click', '.add-label-row', function() {
          const $card = $(this).closest('.card[data-variation-index]');
          const index = $card.data('variation-index');
          const type = $card.find('.variation-type').val();

          if (!type) return;

          let html = '';
          switch (type) {
            case 'text':
              html = VariationManager.generateTextLabel(index);
              break;
            case 'color':
              html = VariationManager.generateColorLabel(index);
              break;
            case 'image':
              html = VariationManager.generateImageLabel(index);
              break;
          }

          $card.find(`.labels-${type}`).append(html);
          updateProductVariants();
        })
        .on('click', '.remove-label', function() {
          const $row = $(this).closest('.label-row');
          const $container = $row.closest('.labels-container');

          if ($container.find('.label-row').length > 1) {
            $row.fadeOut(300, function() {
              $(this).remove();
              updateProductVariants();
            });
          } else {
            alert('At least one label is required when a type is selected.');
          }
        })
        .on('input', '.label-input', updateProductVariants)

        // Options
        .on('click', '#addOption', () => OptionManager.add())
        .on('click', '.remove-option', function() {
          OptionManager.remove($(this).closest('.card[data-option-index]'));
        })
        .on('click', '.toggle-option', function() {
          const targetId = $(this).data('target');
          const $target = $('#' + targetId);
          const $icon = $(this).find('i');

          $target.slideToggle(400, 'swing', function() {
            const isVisible = $(this).is(':visible');
            $icon.toggleClass('fa-chevron-up', isVisible)
              .toggleClass('fa-chevron-down', !isVisible);
          });
        })
        .on('input', '.option-name', function() {
          const title = $(this).val() || 'New Option';
          $(this).closest('.card[data-option-index]').find('.option-title').text(title);
        })
        .on('change', '.option-type', function() {
          const $card = $(this).closest('.card[data-option-index]');
          const type = $(this).val();

          // Hide all option value sections
          $card.find('.option-values-text').hide();
          $card.find('.option-values-select').hide();
          $card.find('.option-values-datetime').hide();

          // Text group types
          const textTypes = ['text', 'textarea'];
          // Select group types
          const selectTypes = ['dropdown', 'checkbox', 'check_custom', 'radio', 'radio_custom', 'multiple_select'];
          // Date/Time group types
          const datetimeTypes = ['date', 'date_time', 'time'];

          if (textTypes.includes(type)) {
            $card.find('.option-values-text').fadeIn(10);
          } else if (selectTypes.includes(type)) {
            $card.find('.option-values-select').fadeIn(10);
          } else if (datetimeTypes.includes(type)) {
            $card.find('.option-values-datetime').fadeIn(10);
          }
        })
        .on('click', '.add-option-value', function() {
          const $card = $(this).closest('.card[data-option-index]');
          const $container = $card.find('.option-values-container');
          const index = $card.data('option-index');
          const rowCount = $container.find('.option-value-row').length;

          const html = `
            <div class="option-value-row d-flex align-items-center mb-2">
              <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                <i class="fas fa-grip-vertical"></i>
              </button>
              <div class="row flex-grow-1">
                <div class="col-md-5">
                  <input type="text" class="form-control" name="options[${index}][values][${rowCount}][label]" placeholder="">
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" class="form-control" name="options[${index}][values][${rowCount}][price]" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-3">
                  <select class="form-control" name="options[${index}][values][${rowCount}][price_type]">
                    <option value="fixed">Fixed</option>
                    <option value="percent">Percent</option>
                  </select>
                </div>
              </div>
              <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-option-value">
                <i class="fas fa-trash"></i>
              </button>
            </div>`;

          $container.append(html);
        })
        .on('click', '.remove-option-value', function() {
          const $row = $(this).closest('.option-value-row');
          const $container = $row.closest('.option-values-container');

          if ($container.find('.option-value-row').length > 1) {
            $row.fadeOut(300, function() {
              $(this).remove();
            });
          } else {
            alert('At least one option value is required.');
          }
        })

        // Downloads
        .on('click', '#addDownload', function() {
          const $container = $('#downloadsContainer');
          const index = $container.find('.download-row').length;

          const html = `
            <div class="download-row d-flex align-items-center mb-2" data-download-index="${index}">
              <button type="button" class="btn btn-sm btn-link text-dark p-1 mr-2 drag-handle">
                <i class="fas fa-grip-vertical"></i>
              </button>
              <input type="text" class="form-control flex-grow-1 mr-2" name="downloads[${index}][file_name]"
                placeholder="" readonly>
              <input type="hidden" name="downloads[${index}][file_id]" value="">
              <button type="button" class="btn btn-outline-secondary mr-2 choose-file-btn">
                Choose
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger remove-download">
                <i class="fas fa-trash"></i>
              </button>
            </div>`;

          $container.append(html);
        })
        .on('click', '.remove-download', function() {
          const $row = $(this).closest('.download-row');
          const $container = $row.closest('#downloadsContainer');

          if ($container.find('.download-row').length > 1) {
            $row.fadeOut(300, function() {
              $(this).remove();
            });
          } else {
            alert('At least one download file is required.');
          }
        })
        .on('click', '.choose-file-btn', function() {
          const $row = $(this).closest('.download-row');
          const $input = $row.find('input[type="text"]');
          const $hiddenInput = $row.find('input[type="hidden"]');

          // Check if MediaManager is available
          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            alert('Media Manager is not available. Please refresh the page.');
            return;
          }

          // Open media manager
          const manager = new window.MediaManager({
            modal: true,
            multiple: false
          });
          manager.open(function(file) {
            $input.val(file.name);
            $hiddenInput.val(file.id);
          });
        })

        // Templates
        .on('click', '#insertTemplate', function() {
          const template = $('#variationTemplate').val();
          if (!template) {
            alert('Please select a template first.');
            return;
          }

          const templates = {
            size: {
              name: 'Size',
              type: 'text'
            },
            color: {
              name: 'Color',
              type: 'color'
            },
            material: {
              name: 'Material',
              type: 'text'
            }
          };

          if (templates[template]) {
            VariationManager.add(templates[template]);
            $('#variationTemplate').val('');
          }
        })

        // Collapse/Expand all with smooth animations
        .on('click', '#toggleAllVariations', function() {
          const $button = $(this);
          const $icon = $button.find('i');
          const $variationCollapses = $('[id^="collapseVariation"]');

          // Disable button briefly to prevent rapid clicking
          $button.prop('disabled', true);

          if ($icon.hasClass('fa-chevron-up')) {
            // Collapse all
            $variationCollapses.collapse('hide');
            $('[data-target^="#collapseVariation"] i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
          } else {
            // Expand all
            $variationCollapses.collapse('show');
            $('[data-target^="#collapseVariation"] i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
          }

          // Re-enable button after animation
          setTimeout(() => {
            $button.prop('disabled', false);
          }, 600);
        })
        .on('click', '#toggleAllAttributes', function() {
          const $button = $(this);
          const $icon = $button.find('i');
          const $attributeRows = $('.attribute-row');

          // Disable button briefly to prevent rapid clicking
          $button.prop('disabled', true);

          if ($icon.hasClass('fa-chevron-up')) {
            // Collapse all with smooth animation
            $attributeRows.each(function(index) {
              const $row = $(this);
              setTimeout(() => {
                $row.slideUp(400, 'swing');
              }, index * 100); // Stagger animation
            });
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
          } else {
            // Expand all with smooth animation
            $attributeRows.each(function(index) {
              const $row = $(this);
              setTimeout(() => {
                $row.slideDown(400, 'swing');
              }, index * 100); // Stagger animation
            });
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
          }

          // Re-enable button after animation
          setTimeout(() => {
            $button.prop('disabled', false);
          }, 600);
        })
        .on('click', '#toggleAllOptions', function() {
          const $button = $(this);
          const $icon = $button.find('i');
          const $optionCollapses = $('[id^="collapseOption"]');

          // Disable button briefly to prevent rapid clicking
          $button.prop('disabled', true);

          if ($icon.hasClass('fa-chevron-up')) {
            // Collapse all
            $optionCollapses.collapse('hide');
            $('[data-target^="#collapseOption"] i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
          } else {
            // Expand all
            $optionCollapses.collapse('show');
            $('[data-target^="#collapseOption"] i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
          }

          // Re-enable button after animation
          setTimeout(() => {
            $button.prop('disabled', false);
          }, 400);
        })

        // Media
        .on('click', '#mainImageHolder', function(e) {
          e.preventDefault();

          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            alert('Media Manager is not available. Please refresh the page.');
            return;
          }

          const manager = new window.MediaManager({
            modal: true,
            multiple: false
          });
          manager.open(function(file) {
            GalleryManager.setMainImage(file);
          });
        })
        .on('click', '#removeMainImage', function(e) {
          e.stopPropagation();
          if (confirm('Are you sure you want to remove the featured image?')) {
            GalleryManager.clearMainImage();
          }
        })
        .on('click', '[data-media-picker-multiple]', function(e) {
          e.preventDefault();

          if (state.galleryImages.length >= CONFIG.MAX_GALLERY_IMAGES) {
            alert(`You can only add up to ${CONFIG.MAX_GALLERY_IMAGES} gallery images.`);
            return;
          }

          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            alert('Media Manager is not available. Please refresh the page.');
            return;
          }

          const manager = new window.MediaManager({
            modal: true,
            multiple: true
          });
          manager.open(function(files) {
            const available = CONFIG.MAX_GALLERY_IMAGES - state.galleryImages.length;
            let added = 0;

            files.forEach(file => {
              if (added >= available) return;
              if (!state.galleryImages.find(img => img.id === file.id)) {
                state.galleryImages.push(file);
                GalleryManager.addThumbnail(file);
                added++;
              }
            });

            if (files.length > added && added > 0) {
              alert(
                `Only ${added} image(s) were added. Maximum ${CONFIG.MAX_GALLERY_IMAGES} gallery images allowed.`
              );
            }

            GalleryManager.updateInput();
            GalleryManager.updateAddButton();
          });
        })
        .on('click', '.remove-thumbnail', function(e) {
          e.stopPropagation();
          const $item = $(this).closest('.media-thumbnail-item');
          const fileId = $item.data('file-id');

          if (confirm('Are you sure you want to remove this image?')) {
            GalleryManager.removeThumbnail(fileId);
          }
        })
        .on('click', '.media-thumbnail-item:not(.add-new-media)', function(e) {
          if ($(e.target).hasClass('remove-thumbnail') || $(e.target).closest('.remove-thumbnail').length) {
            return;
          }

          const fileId = $(this).data('file-id');
          const file = state.galleryImages.find(img => img.id === fileId);

          if (file) {
            const currentMainId = state.featuredImageId;
            const currentMainSrc = $elements.mainProductImage.attr('src');

            // Set as main image
            GalleryManager.setMainImage(file);

            // Remove from gallery
            state.galleryImages = state.galleryImages.filter(img => img.id !== fileId);
            $(this).remove();

            GalleryManager.updateInput();
            GalleryManager.updateAddButton();
          }
        });

      // Color utilities
      function getColorName(hex) {
        const colors = {
          '#FF0000': 'Red',
          '#00FF00': 'Green',
          '#0000FF': 'Blue',
          '#FFFF00': 'Yellow',
          '#FF00FF': 'Magenta',
          '#00FFFF': 'Cyan',
          '#000000': 'Black',
          '#FFFFFF': 'White',
          '#808080': 'Gray',
          '#FFA500': 'Orange',
          '#800080': 'Purple',
          '#FFC0CB': 'Pink',
          '#A52A2A': 'Brown',
          '#008000': 'Dark Green',
          '#000080': 'Navy'
        };
        return colors[hex.toUpperCase()] || 'Custom';
      }

      function isValidHex(hex) {
        return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
      }
      // Color event handlers
      $(document)
        .on('change', '.color-picker', function() {
          const $row = $(this).closest('.label-row');
          const color = $(this).val();
          const $hex = $row.find('.color-hex');
          const $name = $row.find('.color-name');

          $hex.val(color);
          if (!$name.val()) {
            $name.val(getColorName(color));
          }
          updateProductVariants();
        })
        .on('input', '.color-hex', function() {
          const $row = $(this).closest('.label-row');
          const hex = $(this).val();

          if (isValidHex(hex)) {
            $row.find('.color-picker').val(hex);
          }
          updateProductVariants();
        })
        .on('input', '.color-name', function() {
          updateProductVariants();
        });

      // Main variants accordion toggle (controls all variant details visibility)
      $(document)
        .on('click', '#toggleAllVariantsAccordion', function() {
          const $button = $(this);
          const $icon = $button.find('i');
          const $allVariantDetails = $('.variant-details');
          const $allVariantToggles = $('.toggle-variant-details');
          const isExpanded = $button.attr('aria-expanded') === 'true';

          if (isExpanded) {
            // Collapse all variant details
            $allVariantDetails.slideUp(300);
            $allVariantToggles.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $allVariantToggles.attr('aria-expanded', 'false');
            $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            $button.attr('aria-expanded', 'false');
          } else {
            // Expand all variant details
            $allVariantDetails.slideDown(300);
            $allVariantToggles.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $allVariantToggles.attr('aria-expanded', 'true');
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            $button.attr('aria-expanded', 'true');
          }
        })

      // Image selection handlers
      $(document)
        .on('click', '.select-image, .image-preview', function(e) {
          e.preventDefault();
          const $row = $(this).closest('.label-row');
          const $preview = $row.find('.image-preview');
          const $name = $row.find('.image-name');
          const $id = $row.find('.image-id');

          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            alert('Media Manager is not available. Please refresh the page.');
            return;
          }

          const manager = new window.MediaManager({
            modal: true,
            multiple: false
          });
          manager.open(function(file) {
            $preview.html(
              `<img src="${file.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">`
            );
            $name.val(file.name);
            $id.val(file.id);
          });
          manager.open(function(file) {
            $preview.html(
              `<img src="${file.url}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">`
            );
            $name.val(file.name);
            $id.val(file.id);
          });
        })
        .on('input', '.image-name', function() {
          updateProductVariants();
        })

        // Variant controls
        .on('click', '.toggle-variant-details', function() {
          const targetId = $(this).data('target');
          const $target = $('#' + targetId);
          const $icon = $(this).find('i');

          $target.slideToggle(300, function() {
            const isVisible = $(this).is(':visible');
            $icon.toggleClass('fa-chevron-up', isVisible)
              .toggleClass('fa-chevron-down', !isVisible);
          });
        })
        .on('click', '.remove-variant', function() {
          if (confirm('Are you sure you want to remove this variant?')) {
            $(this).closest('.variant-row-container').fadeOut(300, function() {
              $(this).remove();
              // Update variant count and regenerate if needed
              updateProductVariants();
            });
          }
        })

        // Variant controls
        .on('click', '#collapseAllVariants', function() {
          $('.variant-card-body').slideUp(300);
          $('.toggle-variant i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        })
        .on('click', '#expandAllVariants', function() {
          $('.variant-card-body').slideDown(300);
          $('.toggle-variant i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        })
        .on('click', '.toggle-variant', function() {
          const targetId = $(this).data('target');
          const $target = $('#' + targetId);
          const $icon = $(this).find('i');

          $target.slideToggle(300, function() {
            const isVisible = $(this).is(':visible');
            $icon.toggleClass('fa-chevron-up', isVisible)
              .toggleClass('fa-chevron-down', !isVisible);
          });
        })
        .on('click', '.remove-variant', function() {
          if (confirm('Are you sure you want to remove this variant?')) {
            $(this).closest('.variant-card').fadeOut(300, function() {
              $(this).remove();
            });
          }
        })

        // New variation-values-list Functionality - Dynamic behavior for reference image layout
        .on('change', '#variation-values-list', function() {
          const selectedVariants = $(this).val();
          const $bulkPriceSection = $('#bulkPriceSection');
          const $bulkFieldTypeSection = $('#bulk_edit_variants_field_type').closest('.row');

          // When selection changes in step 2, show/hide field type section and trigger visual changes
          if (selectedVariants !== '') {
            // Show field type section when variant is selected
            $bulkFieldTypeSection.show();
            // All Variants selected - highlight the price section
            $bulkPriceSection.addClass('border border-primary bg-light');
            $('#bulk_edit_variants_price').focus();
          } else {
            // Hide field type section when no variant is selected
            $bulkFieldTypeSection.hide();
            // Reset field type selection
            $('#bulk_edit_variants_field_type').val('');
            // Specific variant selected - change styling
            $bulkPriceSection.removeClass('border border-primary bg-light').addClass('border border-secondary');
          }
        })
        .on('change', '#bulk_edit_variants_field_type', function() {
          const fieldType = $(this).val();
          const $variantsBulkEditSection = $('#variantsBulkEditSection');

          // Remove any existing dynamic sections and layout images
          $('.field-type-layout-image').remove();
          $('#bulkEditVariantsPriceSection').remove();
          $('#applyBulkChangesSection').remove();
          // Remove any additional rows that might have been added (like special_price multiple fields)
          $variantsBulkEditSection.find('.row:gt(2)').remove();

          // Update step 4 based on field type selection in step 3
          if (fieldType !== '') {
            let $html = '';
            switch (fieldType) {
              case 'is_active':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-is-active" class="form-label mb-0"><strong>Status</strong></label>
                      </div>
                      <div class="col-md-5">
                        <div class="form-check">
                          <label class="form-check-label" for="bulk-edit-variants-is-active">
                            <input type="checkbox" class="form-check-input" id="bulk_edit_variants_is_active" name="bulk_edit_variants_is_active" value="1"> Enable the variant
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;
              case 'media':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-media" class="form-label mb-0"><strong>Media</strong></label>
                      </div>
                      <div class="col-md-5">
                        <div class="input-group">
                          <input type="text" class="form-control" id="bulk_edit_variants_media" name="bulk_edit_variants_media" placeholder="Select Media" readonly>
                          <button type="button" class="btn btn-outline-secondary" id="selectMediaBtn">Select</button>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;

              case 'sku':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-sku" class="form-label mb-0"><strong>SKU</strong></label>
                      </div>
                      <div class="col-md-5">
                        <input type="text" class="form-control" id="bulk_edit_variants_sku" name="bulk_edit_variants_sku" placeholder="Enter SKU">
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;
              case 'price':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-price" class="form-label mb-0"><strong>Price</strong></label>
                      </div>
                      <div class="col-md-5">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                          </div>
                          <input type="number" class="form-control" id="bulk_edit_variants_price" name="bulk_edit_variants_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;

                break;

              case 'special_price':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-special-price" class="form-label mb-0"><strong>Special Price</strong></label>
                      </div>
                      <div class="col-md-5">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                          </div>
                          <input type="number" class="form-control" id="bulk_edit_variants_special_price" name="bulk_edit_variants_special_price" placeholder="0.00" step="0.01" min="0">
                        </div>
                      </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-special-price-type" class="form-label mb-0"><strong>Special Price Type</strong></label>
                      </div>
                      <div class="col-md-5">
                        <select class="form-control" id="bulk_edit_variants_special_price_type" name="bulk_edit_variants_special_price_type">
                          <option value="fixed">Fixed</option>
                          <option value="percent">Percent</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-special-price-start" class="form-label mb-0"><strong>Special Price Start</strong></label>
                      </div>
                      <div class="col-md-5">
                        <input type="date" class="form-control" id="bulk_edit_variants_special_price_start" name="bulk_edit_variants_special_price_start">
                      </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-special-price-end" class="form-label mb-0"><strong>Special Price End</strong></label>
                      </div>
                      <div class="col-md-5">
                        <input type="date" class="form-control" id="bulk_edit_variants_special_price_end" name="bulk_edit_variants_special_price_end">
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;

              case 'manage_stock':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-manage-stock" class="form-label mb-0"><strong>Inventory Management</strong></label>
                      </div>
                      <div class="col-md-5">
                        <select class="form-control" id="bulk_edit_variants_manage_stock" name="bulk_edit_variants_manage_stock">
                          <option value="0">Don't Track Inventory</option>
                          <option value="1">Track Inventory</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;

              case 'in_stock':
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-in-stock" class="form-label mb-0"><strong>Stock Availability</strong></label>
                      </div>
                      <div class="col-md-5">
                        <select class="form-control" id="bulk_edit_variants_in_stock" name="bulk_edit_variants_in_stock">
                          <option value="0">Out of Stock</option>
                          <option value="1">In Stock</option>
                        </select>
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;

              default:
                $html = `
                    <div class="row mb-3 align-items-center" id="bulkEditVariantsPriceSection">
                      <div class="col-md-2">
                        <label for="bulk-edit-variants-value" class="form-label mb-0"><strong>Value</strong></label>
                      </div>
                      <div class="col-md-5">
                        <input type="text" class="form-control" id="bulk_edit_variants_value" name="bulk_edit_variants_value" placeholder="Enter value">
                      </div>
                    </div>

                    <div class="row mb-0 align-items-center" id="applyBulkChangesSection">
                      <div class="col-md-2"></div>
                      <div class="col-md-5">
                        <button type="button" class="btn btn-primary" id="applyBulkChanges">Apply</button>
                      </div>
                    </div>
                  `;
                break;
            }
            // Append the layout HTML
            $variantsBulkEditSection.append($html);

          }
        })
        .on('click', '#applyBulkChanges', function() {
          console.log('🔥 APPLY BULK CHANGES CLICKED!');

          const selectedVariants = $('#variation-values-list').val();
          const fieldType = $('#bulk_edit_variants_field_type').val();

          console.log('=== BULK EDIT DEBUG INFO ===');
          console.log('Selected variants:', selectedVariants);
          console.log('Field type:', fieldType);

          // Get field value from the correct input based on field type
          let fieldValue = null;

          switch (fieldType) {
            case 'is_active':
              fieldValue = $('#bulk_edit_variants_is_active').is(':checked') ? 1 : 0;
              console.log('Status value (checkbox):', fieldValue);
              break;
            case 'media':
              fieldValue = $('#bulk_edit_variants_media').val();
              console.log('Media value:', fieldValue);
              break;
            case 'sku':
              fieldValue = $('#bulk_edit_variants_sku').val();
              console.log('SKU value:', fieldValue);
              break;
            case 'price':
              fieldValue = $('#bulk_edit_variants_price').val();
              console.log('Price value:', fieldValue);
              break;
            case 'special_price':
              // For special_price, collect all related fields
              const specialPriceData = {
                special_price: $('#bulk_edit_variants_special_price').val(),
                special_price_type: $('#bulk_edit_variants_special_price_type').val(),
                special_price_start: $('#bulk_edit_variants_special_price_start').val(),
                special_price_end: $('#bulk_edit_variants_special_price_end').val()
              };
              fieldValue = specialPriceData;
              console.log('Special price data:', specialPriceData);
              break;
            case 'manage_stock':
              fieldValue = $('#bulk_edit_variants_manage_stock').val();
              console.log('Manage stock value:', fieldValue);
              break;
            case 'in_stock':
              fieldValue = $('#bulk_edit_variants_in_stock').val();
              console.log('In stock value:', fieldValue);
              break;
            default:
              fieldValue = $('#bulk_edit_variants_value').val();
              console.log('Default value:', fieldValue);
              break;
          }

          // Validate based on field type
          if (!fieldType) {
            console.error('❌ Missing field type');
            alert('Please select a field type.');
            return;
          }

          if (fieldType === 'special_price') {
            // For special_price, check if at least the main price is provided
            if (!fieldValue.special_price || fieldValue.special_price === '') {
              console.error('❌ Missing special price value');
              alert('Please enter a special price value.');
              return;
            }
          } else if (fieldValue === null || fieldValue === '') {
            console.error('❌ Missing field value');
            alert('Please enter a value.');
            return;
          }

          console.log('Final field value to apply:', fieldValue);

          // Debug: Show all variant details on page
          console.log('=== VARIANT DETAILS DEBUG ===');
          $('.variant-details').each(function(index) {
            console.log(`Variant detail ${index}:`, this);
            $(this).find('input, select').each(function() {
              const name = $(this).attr('name');
              const id = $(this).attr('id');
              console.log(`  Input: name="${name}", id="${id}"`);
            });
          });

          let updatedCount = 0;

          // Apply bulk changes to selected variants
          if (selectedVariants === 'all') {
            console.log('📝 Applying to ALL variants...');

            // Debug: Try multiple approaches to find price inputs
            console.log('=== TRYING DIFFERENT SELECTORS ===');

            // Method 1: Look for inputs with name containing the field type
            const selector1 = `input[name*="[${fieldType}]"], select[name*="[${fieldType}]"]`;
            const fields1 = $(selector1);
            console.log(`Method 1 (${selector1}): Found ${fields1.length} fields`);

            // Method 2: Look specifically in variant-details
            const selector2 =
              `.variant-details input[name*="[${fieldType}]"], .variant-details select[name*="[${fieldType}]"]`;
            const fields2 = $(selector2);
            console.log(`Method 2 (${selector2}): Found ${fields2.length} fields`);

            // Method 3: Look for specific variant pattern
            const selector3 =
              `input[name^="variants["][name*="[${fieldType}]"], select[name^="variants["][name*="[${fieldType}]"]`;
            const fields3 = $(selector3);
            console.log(`Method 3 (${selector3}): Found ${fields3.length} fields`);

            // Use the best method that finds fields
            let $targetFields = null;
            if (fields2.length > 0) {
              $targetFields = fields2;
              console.log('✅ Using Method 2');
            } else if (fields3.length > 0) {
              $targetFields = fields3;
              console.log('✅ Using Method 3');
            } else if (fields1.length > 0) {
              $targetFields = fields1;
              console.log('✅ Using Method 1');
            }

            if (fieldType === 'special_price') {
              // Handle special_price with multiple fields
              console.log('📝 Applying special price data to all variants...');

              Object.keys(fieldValue).forEach(subFieldType => {
                const subFieldValue = fieldValue[subFieldType];
                if (subFieldValue !== '' && subFieldValue !== null) {
                  const subFieldSelector =
                    `.variant-details input[name*="[${subFieldType}]"], .variant-details select[name*="[${subFieldType}]"]`;
                  const $subFields = $(subFieldSelector);

                  console.log(`📝 Looking for ${subFieldType} fields: Found ${$subFields.length} fields`);

                  $subFields.each(function() {
                    const fieldName = $(this).attr('name');
                    if (fieldName && fieldName.startsWith('variants[') && fieldName.includes(
                        `][${subFieldType}]`)) {
                      console.log(`📝 Updating field: ${fieldName} with value: ${subFieldValue}`);
                      $(this).val(subFieldValue).addClass('highlight-changed');
                      updatedCount++;
                    }
                  });
                }
              });

            } else if ($targetFields && $targetFields.length > 0) {
              // Handle single field types
              $targetFields.each(function() {
                const fieldName = $(this).attr('name');

                // Only update variant fields, not bulk edit inputs
                if (fieldName && fieldName.startsWith('variants[') && fieldName.includes(`][${fieldType}]`)) {
                  console.log(`📝 Updating field: ${fieldName} with value: ${fieldValue}`);

                  if (fieldType === 'is_active' && $(this).attr('type') === 'checkbox') {
                    $(this).prop('checked', fieldValue == 1);
                  } else {
                    $(this).val(fieldValue);
                  }

                  $(this).addClass('highlight-changed');
                  updatedCount++;
                }
              });
            } else {
              console.error('❌ No fields found with any method');
              alert('❌ Could not find any fields to update. Check console for details.');
            }

            if (updatedCount > 0) {
              console.log(`✅ Updated ${updatedCount} fields`);
              alert(
                `Successfully updated all variants with ${fieldType}: ${fieldType === 'special_price' ? 'special price data' : fieldValue} (${updatedCount} fields updated)`
              );
            }

          } else {
            // Handle specific variant selection
            console.log(`📝 Applying to specific variant: ${selectedVariants}`);

            if (fieldType === 'special_price') {
              // Handle special_price with multiple fields for specific variant
              console.log('📝 Applying special price data to specific variant...');

              Object.keys(fieldValue).forEach(subFieldType => {
                const subFieldValue = fieldValue[subFieldType];
                if (subFieldValue !== '' && subFieldValue !== null) {
                  const specificSubSelector =
                    `input[name="variants[${selectedVariants}][${subFieldType}]"], select[name="variants[${selectedVariants}][${subFieldType}]"]`;
                  const $specificSubField = $(specificSubSelector);

                  console.log(
                    `📝 Looking for ${subFieldType} in variant ${selectedVariants}: Found ${$specificSubField.length} field(s)`
                  );

                  if ($specificSubField.length > 0) {
                    console.log(`📝 Updating field: ${specificSubSelector} with value: ${subFieldValue}`);
                    $specificSubField.val(subFieldValue).addClass('highlight-changed');
                    updatedCount++;
                  }
                }
              });

              if (updatedCount > 0) {
                console.log(`✅ Updated specific variant ${selectedVariants} special price fields`);
                alert(`Successfully updated variant ${parseInt(selectedVariants) + 1} special price fields`);
              } else {
                console.error(`❌ Could not find special price fields for variant ${selectedVariants}`);
                alert(`❌ Could not find special price fields for variant ${parseInt(selectedVariants) + 1}`);
              }

            } else {
              // Handle single field types for specific variant
              const specificSelector =
                `input[name="variants[${selectedVariants}][${fieldType}]"], select[name="variants[${selectedVariants}][${fieldType}]"]`;
              console.log(`Using selector: ${specificSelector}`);

              const $specificField = $(specificSelector);
              console.log(`Found ${$specificField.length} specific field(s)`);

              if ($specificField.length > 0) {
                if (fieldType === 'is_active' && $specificField.attr('type') === 'checkbox') {
                  $specificField.prop('checked', fieldValue == 1);
                } else {
                  $specificField.val(fieldValue);
                }
                $specificField.addClass('highlight-changed');
                updatedCount = 1;

                console.log(`✅ Updated specific variant ${selectedVariants}`);
                alert(
                  `Successfully updated variant ${parseInt(selectedVariants) + 1} with ${fieldType}: ${fieldValue}`
                );
              } else {
                console.error(`❌ Could not find field for variant ${selectedVariants}`);
                alert(`❌ Could not find field for variant ${parseInt(selectedVariants) + 1}`);
              }
            }
          }

          // Reset form
          $('#variation-values-list').val('');
          $('#bulk_edit_variants_field_type').val('');

          // Clear dynamic sections
          $('.field-type-layout-image').remove();
          $('#bulkEditVariantsPriceSection').remove();
          $('#applyBulkChangesSection').remove();
          $('#variantsBulkEditSection').find('.row:gt(2)').remove();

          // Remove highlights after 3 seconds
          setTimeout(() => {
            $('.highlight-changed').removeClass('highlight-changed');
          }, 3000);
        })
        .on('click', '#cancelBulkEdit', function() {
          $('#bulkEditPanel').slideUp(300);
          $('#bulk_edit_variants_field_type').val('');
          $('#bulk_edit_variants_price').val('');
        })
        // Variant image upload
        .on('click', '.variant-image-upload', function() {
          const $uploadArea = $(this);

          if (typeof window.MediaManager === 'undefined') {
            console.error('MediaManager.js is not loaded');
            alert('Media Manager is not available. Please refresh the page.');
            return;
          }

          const manager = new window.MediaManager({
            modal: true,
            multiple: false
          });
          manager.open(function(file) {
            $uploadArea.html(
              `<img src="${file.url}" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover;">`
            );
            // Store the image ID in a hidden field
            const index = $uploadArea.closest('.variant-row-container').index();
            $uploadArea.after(
              `<input type="hidden" name="variants[${index}][image_id]" value="${file.id}">`
            );
          });
        })
        // Default variant selection handler
        .on('change', '#defaultVariantSelect', function() {
          const selectedVariant = $(this).val(); // This is "value1-value2-value3"
          console.log('Selected variant:', selectedVariant);

          // Remove 'Default' badge from all variants
          $('.card[data-variant-id] .badge').text('').removeClass('bg-primary').addClass('bg-secondary');

          // Find the matching variant and add 'Default' badge
          $('.card[data-variant-id]').each(function() {
            const $card = $(this);
            const variantLabel = $card.data('variant-label'); // This is "VALUE1 - VALUE2 - VALUE3"
            const $badge = $card.find('.badge');

            // Convert the selected variant (dashes) to match the variant label (spaces and dashes)
            const selectedVariantFormatted = selectedVariant.split('-').join(' - ').toUpperCase();
            console.log('Comparing:', selectedVariantFormatted, 'with:', variantLabel);

            // Check if this variant matches the selected value
            if (variantLabel === selectedVariantFormatted) {
              console.log('Match found! Setting badge to Default');
              $badge.text('Default').removeClass('bg-secondary').addClass('bg-primary');
            }
          });
        })
        // Bulk edit handlers for variants and variations
        .on('change', '#bulk-edit-variants-field-type', function() {
          const type = $(this).val();
          $('.bulk-edit-field').hide();

          const fieldMap = {
            'sku': '#bulk-edit-sku-field',
            'price': '#bulk-edit-price-field',
            'special_price': '#bulk-edit-price-field',
            'is_active': '#bulk-edit-status-field'
          };

          if (fieldMap[type]) {
            $(fieldMap[type]).show();
          }
        })
        .on('click', '#apply-bulk-edit', function() {
          const selectedVariants = $('#variation-values-list').val();
          const fieldType = $('#bulk-edit-variants-field-type').val();

          if (!selectedVariants || !fieldType) {
            alert('Please select both variants and field type to apply bulk edit.');
            return;
          }

          let value = '';
          const fieldIds = {
            'sku': '#bulk-edit-variants-sku',
            'price': '#bulk-edit-variants-price',
            'special_price': '#bulk-edit-variants-price',
            'is_active': '#bulk-edit-variants-status'
          };

          if (fieldIds[fieldType]) {
            value = $(fieldIds[fieldType]).val();
          }

          if (!value && value !== '0') {
            alert('Please enter a value to apply.');
            return;
          }

          // Apply to variants
          const selector = selectedVariants === 'all' ? `[name*="variants."][name*=".${fieldType}"]` :
            `[name="variants.${selectedVariants}.${fieldType}"]`;
          $(selector).each(function() {
            if ($(this).is('select')) {
              $(this).val(value);
            } else if ($(this).is(':checkbox')) {
              $(this).prop('checked', value === '1');
            } else {
              $(this).val(value);
            }
          });

          // Reset form
          $('.bulk-edit-field').hide();
          $('#bulk-edit-variants-field-type').val('');
          $('#bulk-edit-variants-sku, #bulk-edit-variants-price').val('');
          $('#bulk-edit-variants-status').val('1');

          alert('Bulk edit applied successfully!');
        });

      // Initialize sortable if available
      function initializeSortable() {
        if (typeof $.fn.sortable !== 'undefined') {
          $elements.mediaThumbnailsGrid.sortable({
            items: '.media-thumbnail-item:not(.add-new-media)',
            cursor: 'grabbing',
            tolerance: 'pointer',
            placeholder: 'thumbnail-placeholder',
            start: function(e, ui) {
              ui.placeholder.height(ui.item.height()).width(ui.item.width());
            },
            update: function() {
              const newOrder = [];
              $('.media-thumbnail-item:not(.add-new-media)').each(function() {
                const fileId = $(this).data('file-id');
                const file = state.galleryImages.find(img => img.id === fileId);
                if (file) newOrder.push(file);
              });
              state.galleryImages = newOrder;
              GalleryManager.updateInput();
            }
          });
        }
      }
      // Initialize everything
      function initialize() {
        initializeSelect2();
        initializeSummernote();
        bindEventHandlers();
        initializeSortable();

        // Check initial state
        const mainImageSrc = $elements.mainProductImage.attr('src');
        if (mainImageSrc && !mainImageSrc.includes('placeholder_image.png')) {
          $elements.mediaThumbnailsGrid.fadeIn(300);
        }

        GalleryManager.updateAddButton();
      }
      // Start the application
      initialize();
    });
  </script>
