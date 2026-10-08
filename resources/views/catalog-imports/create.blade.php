@extends('layouts.app')

@section('title', 'Catalog Product Builder')

@section('content')
    <div class="container-fluid py-4 catalog-builder">
        <div class="container-fluid px-2 px-lg-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">Catalog import</div>
                    <h1 class="h2 fw-bold mb-1">PDF Product Builder</h1>
                    <p class="text-secondary mb-0">
                        Choose one main image, add the remaining pages to the gallery, preview any PDF page, and create your products.
                    </p>
                </div>
                <a class="btn btn-outline-dark" href="{{ route('catalog-imports.index') }}">
                    <i class="bi bi-arrow-left me-1"></i> Back to Imports
                </a>
            </div>

            @if (session('info'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <section id="uploadPanel" class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-lg-5">
                            <div class="d-flex align-items-center gap-3">
                                <div class="upload-icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="bi bi-file-earmark-pdf fs-3"></i>
                                </div>
                                <div>
                                    <h2 class="h5 fw-bold mb-1">Choose your catalogue PDF</h2>
                                    <p class="text-secondary small mb-0">Maximum 50 MB. The PDF is also rendered locally in your browser.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <input id="pdfInput" class="form-control" type="file" accept="application/pdf">
                        </div>
                        <div class="col-lg-3 d-grid">
                            <button id="startButton" class="btn btn-dark btn-lg" type="button">
                                <i class="bi bi-folder2-open me-1"></i> Open Catalogue
                            </button>
                        </div>
                        <div class="col-12">
                            <div id="uploadStatus" class="small text-secondary"></div>
                        </div>
                    </div>
                </div>
            </section>

            <div id="builder" class="d-none">
                <div class="row g-4 align-items-start">
                    <section class="col-xl-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 p-4 pb-2">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                                    <div>
                                        <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">PDF Gallery</div>
                                        <h2 id="catalogName" class="h4 fw-bold mb-1">Catalogue pages</h2>
                                        <p class="text-secondary small mb-0">
                                            Select a product on the right, then choose its images. Use Preview before selecting.
                                        </p>
                                    </div>
                                    <div id="pageStatus" class="badge text-bg-light border text-dark px-3 py-2"></div>
                                </div>
                            </div>
                            <div class="card-body p-4 pt-3">
                                <div class="alert alert-light border small d-flex align-items-start gap-2 mb-4">
                                    <i class="bi bi-info-circle mt-1"></i>
                                    <div>
                                        <strong>WordPress-style image selection:</strong>
                                        open <strong>Choose images</strong> on a product, tick the pages you want, then click <strong>Set main</strong> on exactly one image. The main image is saved first and the remaining images become the gallery.
                                    </div>
                                </div>

                                <div id="pageContainer" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
                            </div>
                        </div>
                    </section>

                    <aside class="col-xl-4">
                        <div class="card border-0 shadow-sm product-sidebar">
                            <div class="card-header bg-white border-0 p-4 pb-3">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div>
                                        <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">Products</div>
                                        <h2 class="h5 fw-bold mb-1">Product details</h2>
                                        <p class="text-secondary small mb-0">Edit fields without losing your place.</p>
                                    </div>
                                    <button id="addDesign" class="btn btn-outline-dark btn-sm" type="button">
                                        <i class="bi bi-plus-lg me-1"></i> Design
                                    </button>
                                </div>
                            </div>

                            <div id="productList" class="card-body p-3 pt-0"></div>

                            <div class="card-footer bg-white border-0 p-3 pt-0">
                                <div class="d-grid gap-2">
                                    <button id="createProducts" class="btn btn-dark btn-lg" type="button">
                                        <i class="bi bi-check2-circle me-1"></i> Create Products
                                    </button>
                                    <div id="builderStatus" class="small text-secondary"></div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pagePickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold">Choose product images</h5>
                        <div id="pickerSubtitle" class="small text-secondary"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="alert alert-info border-0 small d-flex gap-2">
                        <i class="bi bi-lightbulb"></i>
                        <div>Tick images to add them to the gallery. Then use <strong>Set main</strong> on the image you want as the product's main image.</div>
                    </div>
                    <div id="pickerGrid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div id="pickerCount" class="small text-secondary"></div>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg bg-dark">
                <div class="modal-header border-secondary text-white">
                    <div>
                        <h5 id="previewTitle" class="modal-title fw-bold">Page preview</h5>
                        <div id="previewQuality" class="small text-white-50">Original PDF render</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-2 p-md-3 text-center">
                    <div id="previewLoading" class="text-white-50 py-5">Rendering preview…</div>
                    <div class="preview-canvas-wrap">
                        <canvas id="previewCanvas"></canvas>
                    </div>
                </div>
                <div class="modal-footer border-secondary justify-content-between">
                    <button type="button" class="btn btn-outline-light" id="previewPrevious">
                        <i class="bi bi-chevron-left me-1"></i> Previous
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-light" id="zoomOut">−</button>
                        <span id="zoomLabel" class="text-white small fw-bold" style="min-width: 50px; text-align: center;">Fit</span>
                        <button type="button" class="btn btn-outline-light" id="zoomIn">+</button>
                    </div>
                    <button type="button" class="btn btn-outline-light" id="previewNext">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .catalog-builder {
            min-height: 70vh;
        }

        .upload-icon {
            width: 58px;
            height: 58px;
            color: #fff;
            background: #111827;
        }

        .product-sidebar {
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 2rem);
            overflow: auto;
        }

        .pdf-page-card,
        .picker-page-card {
            position: relative;
            height: 100%;
            border: 2px solid #e5e7eb;
            border-radius: .75rem;
            overflow: hidden;
            background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
        }

        .pdf-page-card:hover,
        .picker-page-card:hover {
            border-color: #9ca3af;
            transform: translateY(-1px);
        }

        .pdf-page-card.selected {
            border-color: #111827;
            box-shadow: 0 0 0 2px #111827 inset;
        }

        .pdf-thumb,
        .picker-thumb {
            position: relative;
            background: #f3f4f6;
        }

        .pdf-thumb img,
        .picker-thumb img {
            display: block;
            width: 100%;
            aspect-ratio: .72;
            object-fit: contain;
            background: #f8f8f8;
        }

        .page-number-badge {
            position: absolute;
            top: .5rem;
            left: .5rem;
            z-index: 2;
            background: rgba(255, 255, 255, .95);
            color: #111827;
        }

        .selection-badge {
            position: absolute;
            top: .5rem;
            right: .5rem;
            z-index: 2;
        }

        .pdf-page-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .5rem;
            padding: .65rem;
        }

        .product-card {
            border: 1px solid #dee2e6;
            border-radius: .75rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .product-card.active {
            border-color: #111827;
            box-shadow: 0 0 0 1px #111827 inset;
        }

        .product-card-header {
            padding: .85rem 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .product-card-body {
            padding: 1rem;
        }

        .media-picker-summary {
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            background: #f8f9fa;
            padding: .75rem;
        }

        .main-image-slot {
            position: relative;
            min-height: 230px;
            border: 2px dashed #ced4da;
            border-radius: .75rem;
            overflow: hidden;
            background: #f8f9fa;
        }

        .main-image-slot.has-image {
            border-style: solid;
            border-color: #111827;
            background: #fff;
        }

        .main-image-slot img {
            display: block;
            width: 100%;
            height: 230px;
            object-fit: contain;
            background: #fff;
        }

        .main-image-empty {
            min-height: 230px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            text-align: center;
            padding: 1rem;
        }

        .gallery-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: .5rem;
        }

        .gallery-thumb {
            position: relative;
            min-width: 0;
            border: 2px solid #dee2e6;
            border-radius: .5rem;
            overflow: hidden;
            background: #fff;
            cursor: grab;
        }

        .gallery-thumb.is-main {
            border-color: #111827;
        }

        .gallery-thumb img {
            display: block;
            width: 100%;
            aspect-ratio: .72;
            object-fit: contain;
            background: #f8f8f8;
        }

        .gallery-thumb .gallery-number {
            position: absolute;
            top: .25rem;
            left: .25rem;
            min-width: 20px;
            padding: .15rem .35rem;
            border-radius: 999px;
            background: rgba(17, 24, 39, .9);
            color: #fff;
            font-size: 10px;
            text-align: center;
        }

        .gallery-thumb .gallery-remove {
            position: absolute;
            top: .2rem;
            right: .2rem;
            width: 23px;
            height: 23px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: rgba(127, 29, 29, .92);
            color: #fff;
            line-height: 1;
        }

        .gallery-thumb.dragging {
            opacity: .45;
        }

        .picker-page-card.is-selected {
            border-color: #111827;
            box-shadow: 0 0 0 2px #111827 inset;
        }

        .picker-page-card.is-main {
            border-color: #b7791f;
            box-shadow: 0 0 0 2px #b7791f inset;
        }

        .picker-main-badge {
            position: absolute;
            left: .5rem;
            bottom: .5rem;
            z-index: 2;
        }

        .preview-canvas-wrap {
            max-height: 72vh;
            overflow: auto;
            background: #1f2937;
            border-radius: .5rem;
        }

        #previewCanvas {
            display: block;
            margin: 0 auto;
            max-width: none;
            height: auto;
        }

        .field-help {
            font-size: .75rem;
            color: #6c757d;
        }

        @media (max-width: 1199.98px) {
            .product-sidebar {
                position: static;
                max-height: none;
                overflow: visible;
            }
        }

        @media (max-width: 575.98px) {
            .gallery-strip {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        (() => {
            const csrf = @json(csrf_token());
            const storeUrl = @json(route('catalog-imports.store'));
            const categories = @json($categories);
            const brands = @json($brands);

            const input = document.getElementById('pdfInput');
            const startButton = document.getElementById('startButton');
            const uploadStatus = document.getElementById('uploadStatus');
            const uploadPanel = document.getElementById('uploadPanel');
            const builder = document.getElementById('builder');
            const catalogName = document.getElementById('catalogName');
            const pageStatus = document.getElementById('pageStatus');
            const pageContainer = document.getElementById('pageContainer');
            const productList = document.getElementById('productList');
            const builderStatus = document.getElementById('builderStatus');
            const addDesign = document.getElementById('addDesign');
            const createProducts = document.getElementById('createProducts');
            const pickerGrid = document.getElementById('pickerGrid');
            const pickerCount = document.getElementById('pickerCount');
            const pickerSubtitle = document.getElementById('pickerSubtitle');
            const previewCanvas = document.getElementById('previewCanvas');
            const previewLoading = document.getElementById('previewLoading');
            const previewTitle = document.getElementById('previewTitle');
            const previewQuality = document.getElementById('previewQuality');
            const zoomLabel = document.getElementById('zoomLabel');

            const pickerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pagePickerModal'));
            const previewModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pagePreviewModal'));

            let pdfDocument = null;
            let finalizeUrl = null;
            let products = [];
            let activeProduct = 0;
            let pickerProductIndex = 0;
            let previewPageNumber = 1;
            let previewZoom = 1;
            const pageThumbs = new Map();

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function money(value) {
                return new Intl.NumberFormat('en-PK', { maximumFractionDigits: 0 }).format(Number(value) || 0);
            }

            function optionMarkup(items, selected, emptyLabel) {
                return [
                    `<option value="">${escapeHtml(emptyLabel)}</option>`,
                    ...items.map(item => `<option value="${item.id}" ${String(item.id) === String(selected) ? 'selected' : ''}>${escapeHtml(item.name)}</option>`),
                ].join('');
            }

            function normalizeImages(images) {
                return Array.from(new Set((images || []).map(Number).filter(Number.isFinite)));
            }

            function addProduct(data = {}) {
                const images = normalizeImages(data.pages || data.images || []);

                products.push({
                    name: data.name || `Design ${String(products.length + 1).padStart(2, '0')}`,
                    description: data.description || '',
                    retail_price: Number(data.retail_price || 0),
                    discount_percent: Number(data.discount_percent ?? 3),
                    gender: data.gender || 'women',
                    piece_count: data.piece_count ?? '',
                    fabric: data.fabric || '',
                    season: data.season || '',
                    category_id: data.category_id || '',
                    brand_id: data.brand_id || '',
                    design_code: data.design_code || '',
                    stock_quantity: Number(data.stock_quantity ?? 3),
                    featured: Boolean(data.featured),
                    images,
                    main_page: Number(data.main_page || images[0] || 0),
                });

                activeProduct = products.length - 1;
            }

            function activeImages(product) {
                return product?.images || [];
            }

            function ensureMainFirst(product) {
                if (!product || !product.images.length) {
                    if (product) {
                        product.main_page = 0;
                    }
                    return;
                }

                if (!product.images.includes(product.main_page)) {
                    product.main_page = product.images[0];
                }

                product.images = [
                    product.main_page,
                    ...product.images.filter(page => page !== product.main_page),
                ];
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => {
                    ensureMainFirst(product);
                    const salePrice = Number(product.retail_price) * (1 - Number(product.discount_percent) / 100);
                    const images = activeImages(product);
                    const mainImage = images.length ? pageThumbs.get(product.main_page) : null;

                    return `
                        <article class="product-card ${index === activeProduct ? 'active' : ''}" data-product-card="${index}">
                            <div class="product-card-header d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="fw-bold">Design ${String(index + 1).padStart(2, '0')}</div>
                                    <div class="small text-secondary">${images.length} image${images.length === 1 ? '' : 's'} selected</div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm ${index === activeProduct ? 'btn-dark' : 'btn-outline-dark'}" data-action="select-product" data-index="${index}">
                                        ${index === activeProduct ? 'Editing' : 'Edit'}
                                    </button>
                                    ${products.length > 1 ? `<button type="button" class="btn btn-sm btn-outline-danger" data-action="remove-product" data-index="${index}"><i class="bi bi-trash"></i></button>` : ''}
                                </div>
                            </div>

                            <div class="product-card-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Main image</label>
                                    <div class="main-image-slot ${mainImage ? 'has-image' : ''}">
                                        ${mainImage
                                            ? `<img src="${mainImage}" alt="Main image for ${escapeHtml(product.name)}">
                                               <span class="badge text-bg-dark position-absolute top-0 start-0 m-2">Main image</span>`
                                            : `<div class="main-image-empty"><i class="bi bi-image fs-1 mb-2"></i><strong>No main image</strong><span class="small">Choose images below.</span></div>`}
                                    </div>
                                </div>

                                <div class="media-picker-summary mb-3">
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <div class="fw-semibold small">Product gallery</div>
                                            <div class="field-help">First image is the main image.</div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-dark" data-action="open-picker" data-index="${index}">
                                            <i class="bi bi-images me-1"></i> Choose images
                                        </button>
                                    </div>
                                    <div class="gallery-strip" data-gallery="${index}">
                                        ${images.map((page, galleryIndex) => `
                                            <div class="gallery-thumb ${page === product.main_page ? 'is-main' : ''}" draggable="true" data-page="${page}" data-product="${index}" data-gallery-index="${galleryIndex}">
                                                <img src="${pageThumbs.get(page) || ''}" alt="Page ${page}">
                                                <span class="gallery-number">${galleryIndex + 1}</span>
                                                <button type="button" class="gallery-remove" title="Remove image" data-action="remove-image" data-index="${index}" data-page="${page}">&times;</button>
                                            </div>
                                        `).join('') || `<div class="small text-secondary py-3 text-center">No gallery images selected yet.</div>`}
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Product title</label>
                                        <input class="form-control form-control-sm" data-field="name" data-index="${index}" value="${escapeHtml(product.name)}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Retail price (PKR)</label>
                                        <input class="form-control form-control-sm" type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Discount (%)</label>
                                        <input class="form-control form-control-sm" type="number" min="0" max="100" step="0.01" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}">
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-light border py-2 px-3 mb-0 small">
                                            Customer price: <strong data-sale-price="${index}">PKR ${money(salePrice)}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Gender</label>
                                        <select class="form-select form-select-sm" data-field="gender" data-index="${index}">
                                            <option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option>
                                            <option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Pieces</label>
                                        <input class="form-control form-control-sm" type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${escapeHtml(product.piece_count)}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Fabric</label>
                                        <input class="form-control form-control-sm" data-field="fabric" data-index="${index}" value="${escapeHtml(product.fabric)}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Season</label>
                                        <input class="form-control form-control-sm" data-field="season" data-index="${index}" value="${escapeHtml(product.season)}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Category</label>
                                        <select class="form-select form-select-sm" data-field="category_id" data-index="${index}">${optionMarkup(categories, product.category_id, 'No category')}</select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Brand</label>
                                        <select class="form-select form-select-sm" data-field="brand_id" data-index="${index}">${optionMarkup(brands, product.brand_id, 'No brand')}</select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Design code</label>
                                        <input class="form-control form-control-sm" data-field="design_code" data-index="${index}" value="${escapeHtml(product.design_code)}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Description</label>
                                        <textarea class="form-control form-control-sm" rows="3" data-field="description" data-index="${index}">${escapeHtml(product.description)}</textarea>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold">Opening stock</label>
                                        <input class="form-control form-control-sm" type="number" min="0" data-field="stock_quantity" data-index="${index}" value="${product.stock_quantity}">
                                    </div>
                                    <div class="col-6 d-flex align-items-end">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" data-field="featured" data-index="${index}" ${product.featured ? 'checked' : ''}>
                                            <label class="form-check-label small">Feature on homepage</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    `;
                }).join('');
            }

            function updateField(element) {
                const index = Number(element.dataset.index);
                const product = products[index];

                if (!product) {
                    return;
                }

                product[element.dataset.field] = element.type === 'checkbox'
                    ? element.checked
                    : element.value;

                if (['retail_price', 'discount_percent'].includes(element.dataset.field)) {
                    const salePrice = Number(product.retail_price) * (1 - Number(product.discount_percent) / 100);
                    const priceTarget = productList.querySelector(`[data-sale-price="${index}"]`);

                    if (priceTarget) {
                        priceTarget.textContent = `PKR ${money(salePrice)}`;
                    }
                }
            }

            function updateProductSelection() {
                productList.querySelectorAll('[data-product-card]').forEach(card => {
                    const index = Number(card.dataset.productCard);
                    card.classList.toggle('active', index === activeProduct);
                });

                pageContainer.querySelectorAll('[data-page]').forEach(card => {
                    const page = Number(card.dataset.page);
                    const selected = products[activeProduct]?.images.includes(page);
                    card.classList.toggle('selected', selected);
                    const badge = card.querySelector('.selection-badge');
                    if (badge) {
                        badge.classList.toggle('d-none', !selected);
                    }
                });
            }

            function renderPicker() {
                const product = products[pickerProductIndex];

                if (!product) {
                    return;
                }

                pickerSubtitle.textContent = `${product.name} • Select gallery images and choose the main image.`;
                pickerGrid.innerHTML = Array.from(pageThumbs.entries()).map(([page, image]) => {
                    const selected = product.images.includes(page);
                    const isMain = product.main_page === page;

                    return `
                        <div class="col">
                            <div class="picker-page-card ${selected ? 'is-selected' : ''} ${isMain ? 'is-main' : ''}" data-picker-page="${page}">
                                <div class="picker-thumb">
                                    <img src="${image}" alt="Page ${page}">
                                    <span class="badge page-number-badge">Page ${page}</span>
                                    ${isMain ? '<span class="badge text-bg-warning picker-main-badge">Main image</span>' : ''}
                                </div>
                                <div class="p-2">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-sm ${selected ? 'btn-dark' : 'btn-outline-dark'}" data-picker-action="toggle" data-page="${page}">
                                            <i class="bi ${selected ? 'bi-check2-square' : 'bi-square'} me-1"></i>
                                            ${selected ? 'Selected' : 'Add to gallery'}
                                        </button>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1" data-picker-action="preview" data-page="${page}">
                                                <i class="bi bi-eye me-1"></i> Preview
                                            </button>
                                            <button type="button" class="btn btn-sm ${isMain ? 'btn-warning' : 'btn-outline-warning'}" ${selected ? '' : 'disabled'} data-picker-action="main" data-page="${page}" title="Set as main image">
                                                <i class="bi bi-star${isMain ? '-fill' : ''}"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                pickerCount.textContent = `${product.images.length} image${product.images.length === 1 ? '' : 's'} selected • Main: ${product.main_page ? `Page ${product.main_page}` : 'Not selected'}`;
            }

            function openPicker(index) {
                activeProduct = index;
                pickerProductIndex = index;
                updateProductSelection();
                renderPicker();
                pickerModal.show();
            }

            function togglePageForProduct(index, page) {
                const product = products[index];
                if (!product) {
                    return;
                }

                const position = product.images.indexOf(page);

                if (position >= 0) {
                    product.images.splice(position, 1);

                    if (product.main_page === page) {
                        product.main_page = product.images[0] || 0;
                    }
                } else {
                    product.images.push(page);

                    if (!product.main_page) {
                        product.main_page = page;
                    }
                }

                ensureMainFirst(product);
                renderProducts();
                updateProductSelection();
                renderPicker();
            }

            function setMainImage(index, page) {
                const product = products[index];

                if (!product || !product.images.includes(page)) {
                    return;
                }

                product.main_page = page;
                ensureMainFirst(product);
                renderProducts();
                updateProductSelection();
                renderPicker();
            }

            function removeImage(index, page) {
                const product = products[index];
                if (!product) {
                    return;
                }

                product.images = product.images.filter(item => item !== page);

                if (product.main_page === page) {
                    product.main_page = product.images[0] || 0;
                }

                ensureMainFirst(product);
                renderProducts();
                updateProductSelection();
            }

            function moveGalleryItem(productIndex, fromPage, toPage) {
                const product = products[productIndex];
                if (!product || fromPage === toPage) {
                    return;
                }

                const fromIndex = product.images.indexOf(fromPage);
                const toIndex = product.images.indexOf(toPage);

                if (fromIndex < 0 || toIndex < 0) {
                    return;
                }

                const [moved] = product.images.splice(fromIndex, 1);
                product.images.splice(toIndex, 0, moved);
                ensureMainFirst(product);
                renderProducts();
            }

            async function renderPageThumbnail(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const original = page.getViewport({ scale: 1 });
                const scale = Math.min(.62, 520 / original.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');

                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);

                await page.render({
                    canvasContext: canvas.getContext('2d', { alpha: false }),
                    viewport,
                }).promise;

                return canvas.toDataURL('image/jpeg', .78);
            }

            async function renderPages() {
                pageContainer.innerHTML = '';
                pageThumbs.clear();
                const total = pdfDocument.numPages;

                for (let pageNumber = 1; pageNumber <= total; pageNumber++) {
                    pageStatus.textContent = `Rendering page ${pageNumber} of ${total}…`;
                    const image = await renderPageThumbnail(pageNumber);
                    pageThumbs.set(pageNumber, image);

                    const column = document.createElement('div');
                    column.className = 'col';
                    column.innerHTML = `
                        <div class="pdf-page-card" data-page="${pageNumber}">
                            <div class="pdf-thumb">
                                <img src="${image}" alt="PDF page ${pageNumber}">
                                <span class="badge page-number-badge">Page ${pageNumber}</span>
                                <span class="badge text-bg-dark selection-badge d-none">Selected</span>
                            </div>
                            <div class="pdf-page-actions">
                                <button type="button" class="btn btn-sm btn-outline-dark" data-page-action="choose" data-page="${pageNumber}">
                                    <i class="bi bi-plus-lg me-1"></i> Select
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-page-action="preview" data-page="${pageNumber}">
                                    <i class="bi bi-eye me-1"></i> Preview
                                </button>
                            </div>
                        </div>
                    `;

                    pageContainer.appendChild(column);
                }

                pageStatus.textContent = `${total} pages ready`;
                updateProductSelection();
            }

            async function showPreview(pageNumber) {
                previewPageNumber = pageNumber;
                previewZoom = 1;
                zoomLabel.textContent = 'Fit';
                previewTitle.textContent = `Page ${pageNumber} preview`;
                previewQuality.textContent = 'Rendering from the original PDF';
                previewCanvas.width = 1;
                previewCanvas.height = 1;
                previewLoading.classList.remove('d-none');
                previewModal.show();

                const page = await pdfDocument.getPage(pageNumber);
                const original = page.getViewport({ scale: 1 });
                const wrapWidth = document.querySelector('.preview-canvas-wrap')?.clientWidth || 1000;
                const fitScale = Math.min(1.5, Math.max(.8, (wrapWidth - 20) / original.width));
                previewZoom = fitScale;

                await renderPreviewCanvas(page, previewZoom);
                zoomLabel.textContent = `${Math.round(previewZoom * 100)}%`;
                previewLoading.classList.add('d-none');
            }

            async function renderPreviewCanvas(page, scale) {
                const viewport = page.getViewport({ scale });
                previewCanvas.width = Math.ceil(viewport.width);
                previewCanvas.height = Math.ceil(viewport.height);

                await page.render({
                    canvasContext: previewCanvas.getContext('2d', { alpha: false }),
                    viewport,
                }).promise;
            }

            async function changePreviewPage(direction) {
                if (!pdfDocument) {
                    return;
                }

                const nextPage = previewPageNumber + direction;
                if (nextPage < 1 || nextPage > pdfDocument.numPages) {
                    return;
                }

                await showPreview(nextPage);
            }

            async function uploadPdf(file) {
                const formData = new FormData();
                formData.append('pdf', file);

                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(payload.message || 'The PDF upload failed.');
                }

                return payload;
            }

            function buildProductsFromPreset(preset) {
                products = [];

                if (preset?.products?.length) {
                    preset.products.forEach(product => addProduct(product));
                    return;
                }

                if (preset?.groups?.length) {
                    preset.groups.forEach((pages, index) => {
                        addProduct({
                            name: `${preset.collection} — Design ${String(index + 1).padStart(2, '0')}`,
                            description: preset.description,
                            retail_price: preset.retail_price,
                            discount_percent: preset.discount_percent ?? 3,
                            gender: preset.gender,
                            piece_count: preset.piece_count,
                            fabric: preset.fabric,
                            season: preset.season,
                            pages,
                        });
                    });
                    return;
                }

                addProduct({
                    name: 'Design 01',
                    discount_percent: 3,
                    stock_quantity: 3,
                });
            }

            startButton.addEventListener('click', async () => {
                const file = input.files?.[0];

                if (!file) {
                    uploadStatus.innerHTML = '<div class="alert alert-danger py-2 mb-0">Please select a PDF first.</div>';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    uploadStatus.innerHTML = '<div class="alert alert-danger py-2 mb-0">Please select a valid PDF file.</div>';
                    return;
                }

                startButton.disabled = true;
                uploadStatus.textContent = 'Saving PDF and opening the local gallery…';

                try {
                    const payload = await uploadPdf(file);
                    finalizeUrl = payload.finalize_url;
                    catalogName.textContent = payload.name || file.name;

                    const buffer = await file.arrayBuffer();
                    pdfDocument = await pdfjsLib.getDocument({ data: new Uint8Array(buffer) }).promise;

                    buildProductsFromPreset(payload.preset);
                    renderProducts();

                    uploadPanel.classList.add('d-none');
                    builder.classList.remove('d-none');

                    await renderPages();
                    uploadStatus.textContent = '';
                } catch (error) {
                    console.error(error);
                    uploadStatus.innerHTML = `<div class="alert alert-danger py-2 mb-0">${escapeHtml(error.message || 'Unable to open the PDF.')}</div>`;
                    startButton.disabled = false;
                }
            });

            addDesign.addEventListener('click', () => {
                addProduct({
                    name: `Design ${String(products.length + 1).padStart(2, '0')}`,
                    discount_percent: 3,
                    stock_quantity: 3,
                });
                renderProducts();
                updateProductSelection();
            });

            productList.addEventListener('input', event => {
                const field = event.target.closest('[data-field]');
                if (field) {
                    updateField(field);
                }
            });

            productList.addEventListener('change', event => {
                const field = event.target.closest('[data-field]');
                if (field) {
                    updateField(field);
                }
            });

            productList.addEventListener('click', event => {
                const action = event.target.closest('[data-action]');
                if (!action) {
                    return;
                }

                const index = Number(action.dataset.index);
                const type = action.dataset.action;

                if (type === 'select-product') {
                    activeProduct = index;
                    updateProductSelection();
                    renderProducts();
                    return;
                }

                if (type === 'remove-product') {
                    products.splice(index, 1);
                    activeProduct = Math.max(0, Math.min(activeProduct, products.length - 1));
                    renderProducts();
                    updateProductSelection();
                    return;
                }

                if (type === 'open-picker') {
                    openPicker(index);
                    return;
                }

                if (type === 'remove-image') {
                    removeImage(index, Number(action.dataset.page));
                }
            });

            pageContainer.addEventListener('click', event => {
                const action = event.target.closest('[data-page-action]');
                if (!action) {
                    return;
                }

                const page = Number(action.dataset.page);
                const type = action.dataset.pageAction;

                if (type === 'preview') {
                    showPreview(page);
                    return;
                }

                if (type === 'choose') {
                    togglePageForProduct(activeProduct, page);
                    return;
                }
            });

            pickerGrid.addEventListener('click', event => {
                const action = event.target.closest('[data-picker-action]');
                if (!action) {
                    return;
                }

                const page = Number(action.dataset.page);
                const type = action.dataset.pickerAction;

                if (type === 'toggle') {
                    togglePageForProduct(pickerProductIndex, page);
                    return;
                }

                if (type === 'main') {
                    setMainImage(pickerProductIndex, page);
                    return;
                }

                if (type === 'preview') {
                    showPreview(page);
                }
            });

            let draggedGalleryPage = null;
            let draggedGalleryProduct = null;

            productList.addEventListener('dragstart', event => {
                const item = event.target.closest('.gallery-thumb');
                if (!item) {
                    return;
                }

                draggedGalleryPage = Number(item.dataset.page);
                draggedGalleryProduct = Number(item.dataset.product);
                item.classList.add('dragging');
            });

            productList.addEventListener('dragend', event => {
                event.target.closest('.gallery-thumb')?.classList.remove('dragging');
                draggedGalleryPage = null;
                draggedGalleryProduct = null;
            });

            productList.addEventListener('dragover', event => {
                const item = event.target.closest('.gallery-thumb');
                if (item) {
                    event.preventDefault();
                }
            });

            productList.addEventListener('drop', event => {
                const item = event.target.closest('.gallery-thumb');
                if (!item || draggedGalleryPage === null) {
                    return;
                }

                event.preventDefault();
                const productIndex = Number(item.dataset.product);
                const targetPage = Number(item.dataset.page);

                if (productIndex === draggedGalleryProduct) {
                    moveGalleryItem(productIndex, draggedGalleryPage, targetPage);
                }
            });

            document.getElementById('previewPrevious').addEventListener('click', () => changePreviewPage(-1));
            document.getElementById('previewNext').addEventListener('click', () => changePreviewPage(1));

            document.getElementById('zoomIn').addEventListener('click', async () => {
                if (!pdfDocument) return;
                previewZoom = Math.min(3, previewZoom + .25);
                const page = await pdfDocument.getPage(previewPageNumber);
                await renderPreviewCanvas(page, previewZoom);
                zoomLabel.textContent = `${Math.round(previewZoom * 100)}%`;
            });

            document.getElementById('zoomOut').addEventListener('click', async () => {
                if (!pdfDocument) return;
                previewZoom = Math.max(.5, previewZoom - .25);
                const page = await pdfDocument.getPage(previewPageNumber);
                await renderPreviewCanvas(page, previewZoom);
                zoomLabel.textContent = `${Math.round(previewZoom * 100)}%`;
            });

            createProducts.addEventListener('click', async () => {
                if (!finalizeUrl || !pdfDocument) {
                    return;
                }

                const invalid = products.find(product => product.images.length === 0 || !product.main_page);
                if (invalid) {
                    builderStatus.innerHTML = '<div class="alert alert-danger py-2 mb-0">Every design needs at least one image and one main image.</div>';
                    return;
                }

                createProducts.disabled = true;
                builderStatus.textContent = 'Rendering selected product images…';

                try {
                    const payloadProducts = [];

                    for (let index = 0; index < products.length; index++) {
                        const product = products[index];
                        ensureMainFirst(product);
                        const images = [];

                        for (const pageNumber of product.images) {
                            builderStatus.textContent = `Preparing Design ${String(index + 1).padStart(2, '0')} — page ${pageNumber}…`;
                            const page = await pdfDocument.getPage(pageNumber);
                            const original = page.getViewport({ scale: 1 });
                            const scale = Math.min(1.35, 1600 / original.width);
                            const viewport = page.getViewport({ scale });
                            const canvas = document.createElement('canvas');
                            canvas.width = Math.ceil(viewport.width);
                            canvas.height = Math.ceil(viewport.height);

                            await page.render({
                                canvasContext: canvas.getContext('2d', { alpha: false }),
                                viewport,
                            }).promise;

                            images.push(canvas.toDataURL('image/jpeg', .9));
                        }

                        payloadProducts.push({
                            name: product.name,
                            description: product.description,
                            retail_price: Number(product.retail_price),
                            discount_percent: Number(product.discount_percent),
                            stock_quantity: Number(product.stock_quantity),
                            gender: product.gender,
                            piece_count: product.piece_count === '' ? null : Number(product.piece_count),
                            fabric: product.fabric,
                            season: product.season,
                            design_code: product.design_code,
                            category_id: product.category_id === '' ? null : Number(product.category_id),
                            brand_id: product.brand_id === '' ? null : Number(product.brand_id),
                            featured: Boolean(product.featured),
                            images,
                        });
                    }

                    builderStatus.textContent = 'Creating products…';

                    const response = await fetch(finalizeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({ products: payloadProducts }),
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(payload.message || 'Product creation failed.');
                    }

                    builderStatus.innerHTML = `<div class="alert alert-success py-2 mb-0">${escapeHtml(payload.message || 'Products created successfully.')}</div>`;
                    setTimeout(() => window.location.href = payload.redirect, 700);
                } catch (error) {
                    console.error(error);
                    builderStatus.innerHTML = `<div class="alert alert-danger py-2 mb-0">${escapeHtml(error.message || 'Product creation failed.')}</div>`;
                    createProducts.disabled = false;
                }
            });
        })();
    </script>
@endsection
