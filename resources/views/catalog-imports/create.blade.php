@extends('layouts.app')

@section('title', 'Catalog Product Builder')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
            <div>
                <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">Catalog import</div>
                <h1 class="h2 fw-bold mb-1">PDF Product Builder</h1>
                <p class="text-secondary mb-0">Lime Vol-552 and Leore Vol-01 catalogs are recognized automatically; other PDFs stay local and use the manual builder.</p>
            </div>
            <a class="btn btn-outline-dark" href="{{ route('catalog-imports.index') }}">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Imports
            </a>
        </div>

        @if (session('info'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section id="uploadPanel" class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-dark text-white rounded-3 p-3 flex-shrink-0">
                                <i class="fa-solid fa-file-pdf fs-3"></i>
                            </div>
                            <div>
                                <h2 class="h5 fw-bold mb-1">Choose catalogue PDF</h2>
                                <p class="text-secondary small mb-0">Maximum 50 MB. The selected file is rendered locally in your browser.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <input id="pdfInput" class="form-control" type="file" accept="application/pdf">
                    </div>
                    <div class="col-lg-3 d-grid">
                        <button id="startButton" class="btn btn-dark btn-lg" type="button">
                            <i class="fa-solid fa-folder-open me-1"></i> Open Catalogue
                        </button>
                    </div>
                    <div class="col-12">
                        <div id="uploadStatus" class="small text-secondary"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 px-4 pt-4">
                <h2 class="h5 fw-bold mb-1">Catalog-wide product details</h2>
                <p class="text-secondary small mb-0">Set these once. Every design will use the same price, stock, description, fabric, and piece count.</p>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label fw-semibold" for="catalogTitle">Catalog / product title</label>
                        <input id="catalogTitle" class="form-control" maxlength="255" placeholder="Filled from the PDF filename">
                        <div class="form-text">Product names become this title plus “Design 01”, “Design 02”, and so on.</div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogPrice">Retail price (PKR)</label>
                        <input id="catalogPrice" class="form-control" type="number" min="0" step="0.01" required>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogStock">Opening stock per design</label>
                        <input id="catalogStock" class="form-control" type="number" min="0" max="100000" value="3" required>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogDiscount">Discount (%)</label>
                        <input id="catalogDiscount" class="form-control" type="number" min="0" max="100" step="0.01" value="0">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogPieces">Total pieces</label>
                        <input id="catalogPieces" class="form-control" type="number" min="1" max="10">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogFabric">Fabric</label>
                        <input id="catalogFabric" class="form-control" maxlength="100">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogSeason">Season</label>
                        <input id="catalogSeason" class="form-control" maxlength="50">
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogGender">Gender</label>
                        <select id="catalogGender" class="form-select">
                            <option value="women">Women</option>
                            <option value="men">Men</option>
                        </select>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogCategory">Category</label>
                        <select id="catalogCategory" class="form-select">
                            <option value="">No category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-3">
                        <label class="form-label fw-semibold" for="catalogBrand">Brand</label>
                        <select id="catalogBrand" class="form-select">
                            <option value="">No brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="catalogDescription">Product description</label>
                        <textarea id="catalogDescription" class="form-control" rows="3" maxlength="10000"></textarea>
                    </div>
                </div>
            </div>
        </section>

        <div id="builder" class="d-none">
            <div id="automationNotice" class="alert d-none mb-4" role="status"></div>
            <section class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <h2 class="h5 fw-bold mb-1">Homepage slider (optional)</h2>
                            <p class="small text-secondary mb-0">Choose “Use for slider” on any PDF page below. Leave it unselected to skip the slide.</p>
                            <div id="sliderImagePreview" class="small text-secondary mt-2">No slider image selected.</div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <label class="form-label fw-semibold" for="sliderTitle">Slider title <span class="text-secondary fw-normal">optional</span></label>
                            <input id="sliderTitle" class="form-control" maxlength="160">
                        </div>
                        <div class="col-sm-6 col-lg-5">
                            <label class="form-label fw-semibold" for="sliderLink">Slider link <span class="text-secondary fw-normal">optional</span></label>
                            <input id="sliderLink" class="form-control" maxlength="500" placeholder="/shop">
                        </div>
                    </div>
                </div>
            </section>
            <div class="row g-4 align-items-start">
                <section class="col-xl-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 p-4">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                                <div>
                                    <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">PDF Gallery</div>
                                    <h2 id="catalogName" class="h4 fw-bold mb-1">Catalogue pages</h2>
                                    <p class="text-secondary small mb-0">Catalog details are shared automatically. Choose images for each design below.</p>
                                </div>
                                <span id="pageStatus" class="badge text-bg-light border text-dark align-self-start px-3 py-2"></span>
                            </div>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div class="alert alert-light border small">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                <strong>WordPress-style workflow:</strong> select images in the media modal, set exactly one Main image, then arrange the remaining Gallery images in the order you want.
                            </div>
                            <div id="pageContainer" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
                        </div>
                    </div>
                </section>

                <aside class="col-xl-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-0 p-4">
                            <div class="d-flex justify-content-between align-items-center gap-3">
                                <div>
                                    <div class="text-uppercase small fw-bold text-warning-emphasis mb-1">Products</div>
                                    <h2 class="h5 fw-bold mb-1">Product details</h2>
                                    <p class="text-secondary small mb-0">Typing in fields will not refresh the form.</p>
                                </div>
                                <button id="addDesign" class="btn btn-outline-dark btn-sm" type="button">
                                    <i class="fa-solid fa-plus me-1"></i> Design
                                </button>
                            </div>
                        </div>
                        <div id="productList" class="card-body p-3"></div>
                        <div class="card-footer bg-white border-0 p-3">
                            <button id="createProducts" class="btn btn-dark btn-lg w-100" type="button">
                                <i class="fa-solid fa-circle-check me-1"></i> Create Drafts
                            </button>
                            <div id="builderStatus" class="small text-secondary mt-2"></div>
                        </div>
                    </div>
                </aside>
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
                    <div class="alert alert-info small">
                        <i class="fa-solid fa-lightbulb me-1"></i>
                        Tick pages to add them to the gallery. Use <strong>Preview</strong> to inspect a page and <strong>Set main</strong> to choose the primary image.
                    </div>
                    <div id="pickerGrid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span id="pickerCount" class="small text-secondary"></span>
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-0 shadow-lg">
                <div class="modal-header border-secondary">
                    <div>
                        <h5 id="previewTitle" class="modal-title fw-bold">Page preview</h5>
                        <div class="small text-white-50">Original PDF render</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="previewLoading" class="text-white-50 py-5">Rendering preview…</div>
                    <canvas id="previewCanvas" class="img-fluid"></canvas>
                </div>
                <div class="modal-footer border-secondary justify-content-between">
                    <button type="button" class="btn btn-outline-light" id="previewPrevious"><i class="fa-solid fa-chevron-left"></i> Previous</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-light" id="zoomOut">−</button>
                        <button type="button" class="btn btn-outline-light disabled" id="zoomLabel">Fit</button>
                        <button type="button" class="btn btn-outline-light" id="zoomIn">+</button>
                    </div>
                    <button type="button" class="btn btn-outline-light" id="previewNext">Next <i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            const builder = document.getElementById('builder');
            const catalogName = document.getElementById('catalogName');
            const catalogTitle = document.getElementById('catalogTitle');
            const catalogPrice = document.getElementById('catalogPrice');
            const catalogStock = document.getElementById('catalogStock');
            const catalogDiscount = document.getElementById('catalogDiscount');
            const catalogPieces = document.getElementById('catalogPieces');
            const catalogFabric = document.getElementById('catalogFabric');
            const catalogSeason = document.getElementById('catalogSeason');
            const catalogGender = document.getElementById('catalogGender');
            const catalogCategory = document.getElementById('catalogCategory');
            const catalogBrand = document.getElementById('catalogBrand');
            const catalogDescription = document.getElementById('catalogDescription');
            const sliderTitle = document.getElementById('sliderTitle');
            const sliderLink = document.getElementById('sliderLink');
            const sliderImagePreview = document.getElementById('sliderImagePreview');
            const pageStatus = document.getElementById('pageStatus');
            const pageContainer = document.getElementById('pageContainer');
            const productList = document.getElementById('productList');
            const addDesign = document.getElementById('addDesign');
            const createProducts = document.getElementById('createProducts');
            const builderStatus = document.getElementById('builderStatus');
            const automationNotice = document.getElementById('automationNotice');
            const pickerGrid = document.getElementById('pickerGrid');
            const pickerSubtitle = document.getElementById('pickerSubtitle');
            const pickerCount = document.getElementById('pickerCount');
            const previewCanvas = document.getElementById('previewCanvas');
            const previewLoading = document.getElementById('previewLoading');
            const previewTitle = document.getElementById('previewTitle');
            const zoomLabel = document.getElementById('zoomLabel');

            let pdfDocument = null;
            let finalizeUrl = null;
            let catalogPreset = null;
            let products = [];
            let activeProduct = 0;
            let previewPage = 1;
            let previewZoom = 1;
            let sliderPage = null;
            const pageImages = new Map();
            const pickerModal = new bootstrap.Modal(document.getElementById('pagePickerModal'));
            const previewModal = new bootstrap.Modal(document.getElementById('pagePreviewModal'));

            [
                catalogTitle,
                catalogPrice,
                catalogStock,
                catalogDiscount,
                catalogPieces,
                catalogFabric,
                catalogSeason,
                catalogGender,
                catalogCategory,
                catalogBrand,
                catalogDescription,
            ].forEach((element) => {
                element.addEventListener('input', () => {
                    element.dataset.edited = 'true';
                    if (element === catalogTitle) {
                        renderProducts();
                    }
                });
                element.addEventListener('change', () => {
                    element.dataset.edited = 'true';
                });
            });

            function applyCatalogPreset(preset) {
                const defaults = [
                    [catalogPrice, preset.retail_price],
                    [catalogDiscount, preset.discount_percent],
                    [catalogPieces, preset.piece_count],
                    [catalogFabric, preset.fabric],
                    [catalogSeason, preset.season],
                    [catalogGender, preset.gender],
                    [catalogDescription, preset.description],
                ];

                defaults.forEach(([element, value]) => {
                    if (element.dataset.edited !== 'true' && value !== null && value !== undefined) {
                        element.value = value;
                    }
                });

                const presetCategory = categories.find((category) => category.slug === preset.category_slug);
                const presetBrand = brands.find((brand) => brand.name.toLowerCase() === preset.brand_name.toLowerCase());
                if (catalogCategory.dataset.edited !== 'true' && presetCategory) {
                    catalogCategory.value = String(presetCategory.id);
                }
                if (catalogBrand.dataset.edited !== 'true' && presetBrand) {
                    catalogBrand.value = String(presetBrand.id);
                }
            }

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            function setPresetValue(element, value) {
                if (element.dataset.edited !== 'true' && !element.value.trim()) {
                    element.value = value;
                }
            }

            function designTitle(index) {
                return `${catalogTitle.value.trim()} — Design ${String(index + 1).padStart(2, '0')}`;
            }

            function addProduct(data = {}) {
                products.push({
                    design_code: data.design_code || '',
                    selectedPages: [...(data.pages || [])],
                    mainPage: data.mainPage || (data.pages?.[0] ?? null),
                });
                activeProduct = products.length - 1;
            }

            function getOrderedPages(product) {
                const pages = [...product.selectedPages];
                if (product.mainPage && pages.includes(product.mainPage)) {
                    return [product.mainPage, ...pages.filter((page) => page !== product.mainPage)];
                }
                return pages;
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => {
                    const orderedPages = getOrderedPages(product);
                    const mainPage = orderedPages[0] || null;

                    return `
                        <article class="card mb-3 ${index === activeProduct ? 'border-dark shadow-sm' : ''}">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2">
                                <strong>Design ${String(index + 1).padStart(2, '0')}</strong>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-dark" data-select="${index}">${index === activeProduct ? 'Editing' : 'Select'}</button>
                                    ${products.length > 1 ? `<button type="button" class="btn btn-outline-danger" data-remove="${index}">Remove</button>` : ''}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="fw-semibold mb-3">${escapeHtml(designTitle(index))}</div>
                                <div class="d-grid mb-3">
                                    <button type="button" class="btn btn-outline-dark" data-open-picker="${index}">
                                        <i class="fa-solid fa-images me-1"></i> Choose Images (${product.selectedPages.length})
                                    </button>
                                </div>

                                ${mainPage ? `
                                    <div class="mb-2 small fw-bold">Main image</div>
                                    <div class="ratio ratio-4x5 bg-light rounded overflow-hidden mb-3">
                                        <img class="w-100 h-100 object-fit-contain" src="${pageImages.get(mainPage)?.dataUrl || ''}" alt="Page ${mainPage}">
                                    </div>
                                ` : `
                                    <div class="alert alert-light border small">Choose at least one image for this design.</div>
                                `}
                                <div class="small text-secondary">${orderedPages.length} image(s) selected</div>
                            </div>
                        </article>
                    `;
                }).join('');

                productList.querySelectorAll('[data-select]').forEach((button) => {
                    button.addEventListener('click', () => {
                        activeProduct = Number(button.dataset.select);
                        renderProducts();
                        updatePageCards();
                    });
                });

                productList.querySelectorAll('[data-remove]').forEach((button) => {
                    button.addEventListener('click', () => {
                        products.splice(Number(button.dataset.remove), 1);
                        activeProduct = Math.max(0, Math.min(activeProduct, products.length - 1));
                        renderProducts();
                        updatePageCards();
                    });
                });

                productList.querySelectorAll('[data-open-picker]').forEach((button) => {
                    button.addEventListener('click', () => openPicker(Number(button.dataset.openPicker)));
                });

            }

            function updatePageCards() {
                pageContainer.querySelectorAll('[data-page]').forEach((card) => {
                    const page = Number(card.dataset.page);
                    const selected = products[activeProduct]?.selectedPages.includes(page);
                    card.classList.toggle('border-dark', selected);
                    card.querySelector('[data-selected-badge]')?.remove();
                    if (selected) {
                        const badge = document.createElement('span');
                        badge.dataset.selectedBadge = 'true';
                        badge.className = 'badge text-bg-dark position-absolute top-0 end-0 m-2';
                        badge.textContent = products[activeProduct].mainPage === page ? 'Main' : 'Selected';
                        card.querySelector('.position-relative')?.appendChild(badge);
                    }
                });
            }

            async function renderPage(pageNumber, scale = 0.55) {
                const page = await pdfDocument.getPage(pageNumber);
                const base = page.getViewport({ scale: 1 });
                const actualScale = Math.min(scale, 950 / base.width);
                const viewport = page.getViewport({ scale: actualScale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport }).promise;
                return canvas;
            }

            async function renderPages() {
                pageContainer.innerHTML = '';
                const total = pdfDocument.numPages;

                for (let pageNumber = 1; pageNumber <= total; pageNumber++) {
                    pageStatus.textContent = `Rendering ${pageNumber} of ${total}`;
                    const canvas = await renderPage(pageNumber);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.78);
                    pageImages.set(pageNumber, { dataUrl });

                    const column = document.createElement('div');
                    column.className = 'col';
                    column.innerHTML = `
                        <div class="card h-100 shadow-sm border" data-page="${pageNumber}">
                            <div class="position-relative">
                                <span class="badge text-bg-light position-absolute top-0 start-0 m-2">Page ${pageNumber}</span>
                                <img class="card-img-top object-fit-contain bg-light" src="${dataUrl}" alt="PDF page ${pageNumber}">
                            </div>
                            <div class="card-body p-2">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-preview="${pageNumber}">
                                        <i class="fa-solid fa-eye me-1"></i> Preview
                                    </button>
                                    <button type="button" class="btn btn-sm ${sliderPage === pageNumber ? 'btn-warning' : 'btn-outline-secondary'}" data-slider-page="${pageNumber}">
                                        <i class="fa-solid fa-panorama me-1"></i> ${sliderPage === pageNumber ? 'Slider image' : 'Use for slider'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    column.querySelector('[data-preview]').addEventListener('click', () => openPreview(pageNumber));
                    column.querySelector('[data-slider-page]').addEventListener('click', () => {
                        sliderPage = sliderPage === pageNumber ? null : pageNumber;
                        updateSliderPreview();
                        updateSliderPageButtons();
                    });
                    pageContainer.appendChild(column);
                }

                pageStatus.textContent = `${total} pages ready`;
                updatePageCards();
                updateSliderPageButtons();
            }

            function updateSliderPreview() {
                const image = sliderPage ? pageImages.get(sliderPage)?.dataUrl : null;
                sliderImagePreview.innerHTML = image
                    ? `<div class="d-flex align-items-center gap-2"><img src="${image}" alt="Slider page ${sliderPage}" class="rounded object-fit-contain bg-light" width="64" height="80"><span>PDF page ${sliderPage} selected</span><button type="button" class="btn btn-sm btn-outline-secondary ms-auto" id="clearSliderImage">Remove</button></div>`
                    : 'No slider image selected.';

                document.getElementById('clearSliderImage')?.addEventListener('click', () => {
                    sliderPage = null;
                    updateSliderPreview();
                    updateSliderPageButtons();
                });
            }

            function updateSliderPageButtons() {
                pageContainer.querySelectorAll('[data-slider-page]').forEach((button) => {
                    const selected = Number(button.dataset.sliderPage) === sliderPage;
                    button.classList.toggle('btn-warning', selected);
                    button.classList.toggle('btn-outline-secondary', !selected);
                    button.innerHTML = `<i class="fa-solid fa-panorama me-1"></i> ${selected ? 'Slider image' : 'Use for slider'}`;
                });
            }

            function openPicker(productIndex) {
                activeProduct = productIndex;
                const product = products[productIndex];
                pickerSubtitle.textContent = product.name || `Design ${productIndex + 1}`;
                pickerGrid.innerHTML = '';

                for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber++) {
                    const selected = product.selectedPages.includes(pageNumber);
                    const isMain = product.mainPage === pageNumber;
                    const image = pageImages.get(pageNumber)?.dataUrl || '';
                    const column = document.createElement('div');
                    column.className = 'col';
                    column.innerHTML = `
                        <div class="card h-100 ${selected ? 'border-dark shadow-sm' : ''}">
                            <div class="position-relative">
                                <span class="badge text-bg-light position-absolute top-0 start-0 m-2">Page ${pageNumber}</span>
                                ${isMain ? '<span class="badge text-bg-warning position-absolute top-0 end-0 m-2">Main</span>' : ''}
                                <img class="card-img-top object-fit-contain bg-light" src="${image}" alt="PDF page ${pageNumber}">
                            </div>
                            <div class="card-body p-2 d-grid gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" ${selected ? 'checked' : ''} id="pick-${productIndex}-${pageNumber}">
                                    <label class="form-check-label small" for="pick-${productIndex}-${pageNumber}">Add to gallery</label>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-preview-page="${pageNumber}">
                                    <i class="fa-solid fa-eye me-1"></i> Preview
                                </button>
                                <button type="button" class="btn btn-sm ${isMain ? 'btn-warning' : 'btn-outline-dark'}" data-main-page="${pageNumber}" ${selected ? '' : 'disabled'}>
                                    <i class="fa-solid fa-star me-1"></i> ${isMain ? 'Main image' : 'Set main'}
                                </button>
                            </div>
                        </div>
                    `;

                    const checkbox = column.querySelector('input[type="checkbox"]');
                    checkbox.addEventListener('change', () => {
                        if (checkbox.checked) {
                            if (!product.selectedPages.includes(pageNumber)) product.selectedPages.push(pageNumber);
                            if (!product.mainPage) product.mainPage = pageNumber;
                        } else {
                            product.selectedPages = product.selectedPages.filter((page) => page !== pageNumber);
                            if (product.mainPage === pageNumber) product.mainPage = product.selectedPages[0] || null;
                        }
                        renderProducts();
                        updatePageCards();
                        openPicker(productIndex);
                    });

                    column.querySelector('[data-preview-page]').addEventListener('click', () => openPreview(pageNumber));
                    column.querySelector('[data-main-page]').addEventListener('click', () => {
                        if (!product.selectedPages.includes(pageNumber)) return;
                        product.mainPage = pageNumber;
                        product.selectedPages = [pageNumber, ...product.selectedPages.filter((page) => page !== pageNumber)];
                        renderProducts();
                        updatePageCards();
                        openPicker(productIndex);
                    });

                    pickerGrid.appendChild(column);
                }

                updatePickerCount(product);
                pickerModal.show();
            }

            function updatePickerCount(product) {
                pickerCount.textContent = `${product.selectedPages.length} image(s) selected · Main: ${product.mainPage || 'not set'}`;
            }

            function moveGallery(productIndex, page, direction) {
                const product = products[productIndex];
                const pages = getOrderedPages(product);
                const currentIndex = pages.indexOf(page);
                const targetIndex = currentIndex + direction;
                if (currentIndex < 0 || targetIndex < 0 || targetIndex >= pages.length) return;

                [pages[currentIndex], pages[targetIndex]] = [pages[targetIndex], pages[currentIndex]];
                product.mainPage = pages[0];
                product.selectedPages = pages;
                renderProducts();
                updatePageCards();
            }

            async function openPreview(pageNumber) {
                previewPage = pageNumber;
                previewZoom = 1;
                previewTitle.textContent = `Page ${pageNumber}`;
                zoomLabel.textContent = 'Fit';
                previewCanvas.classList.add('d-none');
                previewLoading.classList.remove('d-none');
                previewModal.show();
                await renderPreview();
            }

            async function renderPreview() {
                if (!pdfDocument) return;
                previewLoading.textContent = 'Rendering preview…';
                const page = await pdfDocument.getPage(previewPage);
                const viewport = page.getViewport({ scale: previewZoom });
                previewCanvas.width = Math.ceil(viewport.width);
                previewCanvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: previewCanvas.getContext('2d', { alpha: false }), viewport }).promise;
                previewLoading.classList.add('d-none');
                previewCanvas.classList.remove('d-none');
                zoomLabel.textContent = `${Math.round(previewZoom * 100)}%`;
            }

            document.getElementById('previewPrevious').addEventListener('click', async () => {
                if (previewPage > 1) await openPreview(previewPage - 1);
            });

            document.getElementById('previewNext').addEventListener('click', async () => {
                if (pdfDocument && previewPage < pdfDocument.numPages) await openPreview(previewPage + 1);
            });

            document.getElementById('zoomIn').addEventListener('click', async () => {
                previewZoom = Math.min(2.5, previewZoom + 0.25);
                await renderPreview();
            });

            document.getElementById('zoomOut').addEventListener('click', async () => {
                previewZoom = Math.max(0.5, previewZoom - 0.25);
                await renderPreview();
            });

            async function uploadPdf(file) {
                const formData = new FormData();
                formData.append('pdf', file);
                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                    body: formData,
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'The PDF upload failed.');
                return payload;
            }

            startButton.addEventListener('click', async () => {
                const file = input.files?.[0];
                if (!file) {
                    uploadStatus.innerHTML = '<span class="text-danger">Please select a PDF first.</span>';
                    return;
                }
                if (file.type !== 'application/pdf') {
                    uploadStatus.innerHTML = '<span class="text-danger">Please select a valid PDF file.</span>';
                    return;
                }

                startButton.disabled = true;
                uploadStatus.textContent = 'Saving PDF and preparing the viewer…';

                try {
                    const payload = await uploadPdf(file);
                    finalizeUrl = payload.finalize_url;
                    catalogName.textContent = payload.name || file.name;
                    setPresetValue(catalogTitle, payload.name || file.name.replace(/\.pdf$/i, ''));

                    const buffer = await file.arrayBuffer();
                    pdfDocument = await pdfjsLib.getDocument({ data: new Uint8Array(buffer) }).promise;

                    builder.classList.remove('d-none');

                    catalogPreset = payload.preset || null;
                    if (catalogPreset) {
                        const pagesFit = catalogPreset.products?.every((product) =>
                            product.pages?.length && product.pages.every((page) => page >= 1 && page <= pdfDocument.numPages)
                        );

                        if (!pagesFit) {
                            catalogPreset = null;
                        }
                    }

                    if (catalogPreset) {
                        applyCatalogPreset(catalogPreset);
                        automationNotice.className = 'alert alert-success mb-4';
                        automationNotice.textContent = `${catalogPreset.collection} recognized. Shared catalog details and suggested design images are ready; review or adjust them before creating drafts.`;
                        createProducts.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Create Draft Collection';
                    } else {
                        automationNotice.className = 'alert alert-warning mb-4';
                        automationNotice.textContent = 'This catalog is not a recognized format. Enter shared details above and choose images for each design. Imported products will remain drafts.';
                        createProducts.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Create Draft Products';
                    }

                    products = [];
                    if (catalogPreset?.products?.length) {
                        catalogPreset.products.forEach((product) => addProduct(product));
                    } else {
                        addProduct();
                    }

                    await renderPages();
                    renderProducts();
                    updatePageCards();
                    updateSliderPreview();
                    startButton.classList.add('d-none');
                    input.disabled = true;
                } catch (error) {
                    console.error(error);
                    uploadStatus.innerHTML = `<span class="text-danger">${escapeHtml(error.message || 'Unable to open the PDF.')}</span>`;
                    startButton.disabled = false;
                }
            });

            addDesign.addEventListener('click', () => {
                addProduct();
                renderProducts();
                updatePageCards();
            });

            createProducts.addEventListener('click', async () => {
                if (!finalizeUrl || !pdfDocument) return;

                if (!catalogTitle.value.trim()) {
                    catalogTitle.focus();
                    builderStatus.innerHTML = '<span class="text-danger">Enter a catalog title before creating designs.</span>';
                    return;
                }
                if (catalogPrice.value === '' || Number(catalogPrice.value) < 0) {
                    catalogPrice.focus();
                    builderStatus.innerHTML = '<span class="text-danger">Enter a valid retail price before creating designs.</span>';
                    return;
                }
                if (catalogStock.value === '' || Number(catalogStock.value) < 0) {
                    catalogStock.focus();
                    builderStatus.innerHTML = '<span class="text-danger">Enter a valid opening stock quantity.</span>';
                    return;
                }

                const invalid = products.find((product) => product.selectedPages.length === 0);
                if (invalid) {
                    builderStatus.innerHTML = '<span class="text-danger">Every design needs at least one selected image.</span>';
                    return;
                }
                if ((sliderTitle.value.trim() || sliderLink.value.trim()) && !sliderPage) {
                    builderStatus.innerHTML = '<span class="text-danger">Choose a slider image before entering slider title or link.</span>';
                    return;
                }

                createProducts.disabled = true;
                builderStatus.textContent = 'Preparing selected product images…';

                try {
                    const payloadProducts = [];
                    const sharedDetails = {
                        description: catalogDescription.value.trim(),
                        retail_price: Number(catalogPrice.value),
                        discount_percent: catalogDiscount.value === '' ? 0 : Number(catalogDiscount.value),
                        gender: catalogGender.value,
                        piece_count: catalogPieces.value === '' ? null : Number(catalogPieces.value),
                        fabric: catalogFabric.value.trim(),
                        season: catalogSeason.value.trim(),
                        category_id: catalogCategory.value === '' ? null : Number(catalogCategory.value),
                        brand_id: catalogBrand.value === '' ? null : Number(catalogBrand.value),
                        stock_quantity: Number(catalogStock.value),
                    };

                    for (let index = 0; index < products.length; index++) {
                        const product = products[index];
                        const orderedPages = getOrderedPages(product);
                        const images = [];

                        for (const pageNumber of orderedPages) {
                            builderStatus.textContent = `Preparing Design ${String(index + 1).padStart(2, '0')} — page ${pageNumber}…`;
                            images.push(await selectedPageImage(pageNumber));
                        }

                        payloadProducts.push({
                            ...sharedDetails,
                            name: designTitle(index),
                            design_code: product.design_code || `DESIGN-${String(index + 1).padStart(2, '0')}`,
                            images,
                        });
                    }

                    const sliderImage = sliderPage ? await selectedPageImage(sliderPage) : null;

                    builderStatus.textContent = 'Creating draft listings…';
                    const response = await fetch(finalizeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify({
                            products: payloadProducts,
                            ...(sliderImage ? {
                                slider_image: sliderImage,
                                slider_title: sliderTitle.value.trim(),
                                slider_link: sliderLink.value.trim(),
                            } : {}),
                        }),
                    });
                    const payload = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(payload.message || 'Product creation failed.');

                    builderStatus.innerHTML = `<span class="text-success fw-bold">${escapeHtml(payload.message || 'Products created successfully.')}</span>`;
                    setTimeout(() => window.location.href = payload.redirect, 700);
                } catch (error) {
                    console.error(error);
                    builderStatus.innerHTML = `<span class="text-danger">${escapeHtml(error.message || 'Product creation failed.')}</span>`;
                    createProducts.disabled = false;
                }
            });

            async function selectedPageImage(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const original = page.getViewport({ scale: 1 });
                const scale = Math.min(1.35, 1200 / original.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport }).promise;
                return canvas.toDataURL('image/jpeg', 0.9);
            }
        })();
    </script>
@endpush
