# Local Library Paths Reference

This file contains the mapping from CDN URLs to local library paths for the AdminPro dashboard.

## CSS Libraries

### Bootstrap 4.6.2
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css`
- **Local**: `assets/lib/bootstrap/css/bootstrap.min.css`

### Font Awesome 6.0.0
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css`
- **Local**: `assets/lib/fontawesome/css/all.min.css`

### MetisMenu 3.0.7
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.css`
- **Local**: `assets/lib/metismenu/css/metisMenu.min.css`

### DataTables CSS
- **CDN**: `https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css`
- **Local**: `assets/lib/datatables/css/dataTables.bootstrap4.min.css`

- **CDN**: `https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css`
- **Local**: `assets/lib/datatables/css/buttons.bootstrap4.min.css`

- **CDN**: `https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css`
- **Local**: `assets/lib/datatables/css/responsive.bootstrap4.min.css`

- **CDN**: `https://cdn.datatables.net/select/1.7.0/css/select.bootstrap4.min.css`
- **Local**: `assets/lib/datatables/css/select.bootstrap4.min.css`

### Select2
- **CDN**: `https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css`
- **Local**: `assets/lib/select2/css/select2.min.css`

### Chart.js CSS (optional)
- **CDN**: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css`
- **Local**: Not downloaded (optional CSS file, not commonly used)

## JavaScript Libraries

### jQuery 3.6.0
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js`
- **Local**: `assets/lib/jquery/jquery.min.js`

### Bootstrap 4.6.2 JS
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js`
- **Local**: `assets/lib/bootstrap/js/bootstrap.bundle.min.js`

### MetisMenu 3.0.7 JS
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js`
- **Local**: `assets/lib/metismenu/js/metisMenu.min.js`

### Chart.js 4.4.0
- **CDN**: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js`
- **Local**: `assets/lib/chartjs/chart.umd.js`

- **CDN**: `https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js`
- **Local**: `assets/lib/chartjs/chart.min.js`

### DataTables JS
- **CDN**: `https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js`
- **Local**: `assets/lib/datatables/js/jquery.dataTables.min.js`

- **CDN**: `https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js`
- **Local**: `assets/lib/datatables/js/dataTables.bootstrap4.min.js`

### DataTables Plugins
- **CDN**: `https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js`
- **Local**: `assets/lib/datatables/js/dataTables.buttons.min.js`

- **CDN**: `https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js`
- **Local**: `assets/lib/datatables/js/buttons.bootstrap4.min.js`

- **CDN**: `https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js`
- **Local**: `assets/lib/datatables/js/buttons.html5.min.js`

- **CDN**: `https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js`
- **Local**: `assets/lib/datatables/js/buttons.print.min.js`

- **CDN**: `https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js`
- **Local**: `assets/lib/datatables/js/dataTables.responsive.min.js`

- **CDN**: `https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js`
- **Local**: `assets/lib/datatables/js/responsive.bootstrap4.min.js`

- **CDN**: `https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js`
- **Local**: `assets/lib/datatables/js/dataTables.select.min.js`

### Export Dependencies
- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js`
- **Local**: `assets/lib/datatables/js/jszip.min.js`

- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js`
- **Local**: `assets/lib/datatables/js/pdfmake.min.js`

- **CDN**: `https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js`
- **Local**: `assets/lib/datatables/js/vfs_fonts.js`

### Select2 JS
- **CDN**: `https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js`
- **Local**: `assets/lib/select2/js/select2.min.js`

## Usage Instructions

To update your HTML files to use local libraries instead of CDN:

1. Replace CDN URLs with the corresponding local paths shown above
2. Ensure the relative path is correct from your HTML file location
3. All paths above are relative to the root directory of the project

## Directory Structure Created

```
assets/lib/
├── bootstrap/
│   ├── css/
│   │   └── bootstrap.min.css
│   └── js/
│       └── bootstrap.bundle.min.js
├── fontawesome/
│   ├── css/
│   │   └── all.min.css
│   └── webfonts/
├── metismenu/
│   ├── css/
│   │   └── metisMenu.min.css
│   └── js/
│       └── metisMenu.min.js
├── jquery/
│   └── jquery.min.js
├── datatables/
│   ├── css/
│   │   ├── dataTables.bootstrap4.min.css
│   │   ├── buttons.bootstrap4.min.css
│   │   ├── responsive.bootstrap4.min.css
│   │   └── select.bootstrap4.min.css
│   └── js/
│       ├── jquery.dataTables.min.js
│       ├── dataTables.bootstrap4.min.js
│       ├── dataTables.buttons.min.js
│       ├── buttons.bootstrap4.min.js
│       ├── buttons.html5.min.js
│       ├── buttons.print.min.js
│       ├── dataTables.responsive.min.js
│       ├── responsive.bootstrap4.min.js
│       ├── dataTables.select.min.js
│       ├── jszip.min.js
│       ├── pdfmake.min.js
│       └── vfs_fonts.js
├── chartjs/
│   ├── chart.min.js
│   └── chart.umd.js
└── select2/
    ├── css/
    │   └── select2.min.css
    └── js/
        └── select2.min.js
```