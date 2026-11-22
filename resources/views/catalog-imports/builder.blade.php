@extends('layouts.app')

@section('title', 'Catalog Product Builder')

@section('content')
    <div class="container catalog-builder">
        <div class="page-header">
            <div>
                <p class="eyebrow">Catalog import</p>
                <h1>Product Builder</h1>
                <p class="muted">Preview the complete PDF page, select images, arrange their order, then create the product.</p>
            </div>
            <a class="button button-secondary" href="{{ route('catalog-imports.index') }}">Back to Imports</a>
        </div>

        <section class="panel upload-panel">
            <div>
                <h2>1. Select Catalog PDF</h2>
                <p class="muted">The original PDF stays in your browser. Page thumbnails are low-resolution only for speed; selected product images are rendered again at high resolution when you create products.</p>
            </div>
            <input id="pdfFile" type="file" accept="application/pdf">
            <div id="fileStatus" class="status">No PDF selected.</div>
        </section>

        <div class="builder-layout">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>{{ $catalogImport->name }}</h2>
                        <p class="muted">2. Preview and select pages</p>
                    </div>
                    <div class="progress-wrap">
                        <span id="pageStatus" class="status">Waiting for PDF…</span>
                        <div class="progress"><span id="pageProgress"></span></div>
                    </div>
                </div>

                <div class="help-row">
                    <span>Click the page to select it.</span>
                    <span><strong>Preview</strong> opens the complete page in a large modal.</span>
                    <span>Preview uses the original PDF, not the thumbnail.</span>
                </div>

                <div id="pageContainer" class="pdf-grid">
                    <div class="empty-state">Select a PDF above to load all pages.</div>
                </div>
            </section>

            <aside class="panel product-panel">
                <div class="panel-header">
                    <div>
                        <h2>Products</h2>
                        <p class="muted">3. Arrange gallery order and review details.</p>
                    </div>
                    <button type="button" class="button button-secondary" id="addDesign">+ Add design</button>
                </div>

                <div id="productList"></div>

                <div class="builder-actions">
                    <button type="button" class="button button-primary" id="createProducts">Create Products</button>
                    <div id="builderStatus" class="muted"></div>
                </div>
            </aside>
        </div>
    </div>

    <div id="previewModal" class="preview-modal" hidden>
        <div class="preview-backdrop" data-close-preview></div>
        <div class="preview-dialog" role="dialog" aria-modal="true" aria-label="PDF page preview">
            <div class="preview-toolbar">
                <div>
                    <strong id="previewTitle">Page preview</strong>
                    <span id="previewQuality" class="preview-quality">Original PDF render</span>
                </div>
                <button type="button" class="preview-close" data-close-preview aria-label="Close preview">&times;</button>
            </div>
            <div class="preview-canvas-wrap" id="previewCanvasWrap">
                <div id="previewLoading" class="preview-loading">Rendering full-quality page…</div>
                <canvas id="previewCanvas"></canvas>
            </div>
            <div class="preview-controls">
                <button type="button" class="button button-secondary" id="previewPrevious">← Previous</button>
                <div class="zoom-controls">
                    <button type="button" class="button button-secondary" id="zoomOut">−</button>
                    <span id="zoomLabel">Fit</span>
                    <button type="button" class="button button-secondary" id="zoomIn">+</button>
                </div>
                <button type="button" class="button button-secondary" id="previewNext">Next →</button>
            </div>
        </div>
    </div>

    <style>
        .catalog-builder { max-width: 1550px; margin: 0 auto; padding: 24px; }
        .page-header, .panel-header, .builder-actions { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .page-header { margin-bottom: 20px; }
        .eyebrow { margin: 0 0 4px; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        h1, h2, h3 { margin: 0; }
        .muted { color: #6b7280; }
        .upload-panel { display: grid; grid-template-columns: minmax(260px, 1fr) minmax(300px, 1fr) auto; align-items: center; gap: 16px; margin-bottom: 24px; }
        .upload-panel input[type=file] { width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 10px; background: #fff; }
        .builder-layout { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(390px, .8fr); gap: 24px; align-items: start; }
        .panel { padding: 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; box-shadow: 0 8px 30px rgba(0,0,0,.05); }
        .panel-header { margin-bottom: 18px; }
        .progress-wrap { min-width: 240px; }
        .status { display: block; font-size: 13px; }
        .progress { height: 6px; overflow: hidden; margin-top: 6px; background: #e5e7eb; border-radius: 99px; }
        .progress span { display: block; width: 0; height: 100%; transition: width .2s ease; background: #111827; }
        .help-row { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; font-size: 12px; color: #6b7280; }
        .help-row span { padding: 8px 11px; border-radius: 999px; background: #f3f4f6; }
        .pdf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(185px, 1fr)); gap: 14px; }
        .pdf-page { position: relative; display: flex; flex-direction: column; overflow: hidden; border: 2px solid #e5e7eb; border-radius: 13px; background: #fff; transition: .15s ease; }
        .pdf-page:hover { transform: translateY(-1px); box-shadow: 0 8px 22px rgba(0,0,0,.08); }
        .pdf-page.selected { border-color: #111827; box-shadow: 0 0 0 2px #111827 inset; }
        .page-image-wrap { position: relative; cursor: pointer; background: #f8fafc; }
        .pdf-page canvas { display: block; width: 100%; height: auto; }
        .page-number, .page-selected { position: absolute; z-index: 2; top: 8px; padding: 5px 8px; font-size: 11px; font-weight: 800; border-radius: 999px; background: rgba(255,255,255,.95); }
        .page-number { left: 8px; }
        .page-selected { right: 8px; background: #111827; color: #fff; }
        .page-footer { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 9px; border-top: 1px solid #e5e7eb; }
        .page-footer small { color: #6b7280; }
        .preview-button { min-height: 32px; padding: 0 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; cursor: pointer; font-size: 12px; font-weight: 800; }
        .preview-button:hover { background: #f3f4f6; }
        .product-panel { position: sticky; top: 20px; max-height: calc(100vh - 40px); overflow: auto; }
        .product-card { margin-bottom: 14px; padding: 14px; border: 1px solid #e5e7eb; border-radius: 14px; }
        .product-card.active { border-color: #111827; }
        .product-card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .product-card-header button { border: 0; background: transparent; cursor: pointer; font-weight: 700; }
        .field { margin-bottom: 12px; }
        .field label { display: block; margin-bottom: 5px; font-size: 12px; font-weight: 700; }
        .field input, .field select, .field textarea { width: 100%; box-sizing: border-box; padding: 9px 10px; border: 1px solid #d1d5db; border-radius: 9px; background: #fff; }
        .field textarea { min-height: 80px; resize: vertical; }
        .builder-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .check { display: flex !important; align-items: center; gap: 7px; margin-top: 25px; font-size: 13px; }
        .gallery-order { margin-top: 14px; padding: 12px; border: 1px solid #e5e7eb; border-radius: 12px; background: #f9fafb; }
        .gallery-order h3 { margin-bottom: 4px; font-size: 13px; }
        .gallery-order p { margin: 0 0 10px; font-size: 11px; color: #6b7280; }
        .gallery-list { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; }
        .gallery-item { position: relative; min-width: 0; padding: 0; overflow: hidden; border: 2px solid #d1d5db; border-radius: 8px; background: #fff; cursor: grab; }
        .gallery-item.dragging { opacity: .4; }
        .gallery-item img { display: block; width: 100%; aspect-ratio: 3 / 4; object-fit: contain; background: #f7f5f1; }
        .gallery-item .order-number { position: absolute; left: 5px; top: 5px; min-width: 20px; padding: 3px 5px; text-align: center; border-radius: 999px; color: #fff; background: rgba(17,24,39,.9); font-size: 10px; font-weight: 700; }
        .gallery-item .remove-image { position: absolute; right: 4px; top: 4px; width: 22px; height: 22px; padding: 0; border: 0; border-radius: 50%; color: #fff; background: rgba(127,29,29,.9); cursor: pointer; }
        .selected-pages { padding-top: 8px; font-size: 12px; color: #6b7280; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 14px; border: 0; border-radius: 9px; text-decoration: none; cursor: pointer; font-weight: 700; }
        .button-primary { color: #fff; background: #111827; }
        .button-secondary { color: #111827; background: #f3f4f6; }
        .button:disabled { opacity: .5; cursor: not-allowed; }
        .builder-actions { padding-top: 16px; border-top: 1px solid #e5e7eb; }
        .empty-state { padding: 40px 20px; border: 1px dashed #d1d5db; border-radius: 12px; color: #6b7280; text-align: center; grid-column: 1 / -1; }
        .error-box { padding: 16px; border: 1px solid #fecaca; border-radius: 12px; color: #991b1b; background: #fef2f2; line-height: 1.6; }
        .preview-modal[hidden] { display: none; }
        .preview-modal { position: fixed; z-index: 99999; inset: 0; display: flex; align-items: center; justify-content: center; padding: 18px; }
        .preview-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.82); }
        .preview-dialog { position: relative; z-index: 1; width: min(100%, 1200px); max-height: 96vh; padding: 16px; border-radius: 16px; background: #fff; box-shadow: 0 25px 70px rgba(0,0,0,.45); }
        .preview-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 10px; }
        .preview-quality { display: block; margin-top: 2px; color: #6b7280; font-size: 11px; }
        .preview-close { width: 38px; height: 38px; border: 0; border-radius: 50%; color: #fff; background: #111827; cursor: pointer; font-size: 24px; line-height: 1; }
        .preview-canvas-wrap { max-height: 78vh; overflow: auto; text-align: center; background: #f1f1f1; border-radius: 10px; }
        #previewCanvas { display: block; max-width: none; height: auto; margin: 0 auto; }
        .preview-loading { padding: 60px 20px; color: #6b7280; }
        .preview-controls { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 12px; }
        .zoom-controls { display: flex; align-items: center; gap: 8px; }
        #zoomLabel { min-width: 48px; text-align: center; font-size: 12px; font-weight: 700; }
        body.preview-open { overflow: hidden; }
        @media (max-width: 1100px) { .builder-layout { grid-template-columns: 1fr; } .product-panel { position: static; max-height: none; overflow: visible; } }
        @media (max-width: 850px) { .upload-panel { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .catalog-builder { padding: 14px; } .page-header, .panel-header, .builder-actions { align-items: stretch; flex-direction: column; } .progress-wrap { min-width: 0; } .builder-two-col { grid-template-columns: 1fr; } .check { margin-top: 0; } .gallery-list { grid-template-columns: repeat(3, 1fr); } .preview-modal { padding: 8px; } .preview-dialog { padding: 10px; } .preview-controls { flex-wrap: wrap; justify-content: center; } }
    </style>

    @vite('resources/js/catalog-pdf.js')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const finalizeUrl = @json(route('catalog-imports.finalize', $catalogImport));
            const categories = @json($categories);
            const brands = @json($brands);
            const knownCatalog = @json($catalogImport->presetData());

            const pdfFile = document.getElementById('pdfFile');
            const fileStatus = document.getElementById('fileStatus');
            const pageContainer = document.getElementById('pageContainer');
            const pageStatus = document.getElementById('pageStatus');
            const pageProgress = document.getElementById('pageProgress');
            const productList = document.getElementById('productList');
            const builderStatus = document.getElementById('builderStatus');
            const previewModal = document.getElementById('previewModal');
            const previewCanvas = document.getElementById('previewCanvas');
            const previewLoading = document.getElementById('previewLoading');
            const previewTitle = document.getElementById('previewTitle');
            const zoomLabel = document.getElementById('zoomLabel');

            let pdfDocument = null;
            let products = [];
            let activeProduct = 0;
            let thumbnails = new Map();
            let previewPage = 1;
            let previewZoom = 1;
            let previewRendering = false;
            let loading = false;

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const money = (value) => new Intl.NumberFormat('en-PK', { maximumFractionDigits: 0 }).format(Number(value) || 0);

            function optionMarkup(items, selected, emptyLabel) {
                const options = [`<option value="">${escapeHtml(emptyLabel)}</option>`];
                items.forEach(item => options.push(`<option value="${item.id}" ${String(item.id) === String(selected) ? 'selected' : ''}>${escapeHtml(item.name)}</option>`));
                return options.join('');
            }

            function normalizePages(pages) {
                return Array.isArray(pages) ? pages.map(Number).filter(Boolean) : [];
            }

            function addProduct(data = {}, render = true) {
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
                    pages: normalizePages(data.pages),
                });
                activeProduct = products.length - 1;
                if (render) {
                    renderProducts();
                    updatePageSelection();
                }
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => {
                    const salePrice = Number(product.retail_price) * (1 - Number(product.discount_percent) / 100);
                    const gallery = product.pages.map((pageNumber, order) => {
                        const thumbnail = thumbnails.get(pageNumber);
                        if (!thumbnail) return '';
                        return `<div class="gallery-item" draggable="true" data-gallery-product="${index}" data-page="${pageNumber}"><span class="order-number">${order + 1}</span><img src="${thumbnail.dataUrl}" alt="PDF page ${pageNumber}"><button type="button" class="remove-image" data-remove-page="${pageNumber}" data-product="${index}" aria-label="Remove image">&times;</button></div>`;
                    }).join('');

                    return `<article class="product-card ${index === activeProduct ? 'active' : ''}">
                        <div class="product-card-header"><strong>Design ${String(index + 1).padStart(2, '0')}</strong><div><button type="button" data-select="${index}">${index === activeProduct ? 'Editing' : 'Select'}</button>${products.length > 1 ? `<button type="button" data-remove="${index}">Remove</button>` : ''}</div></div>
                        <div class="field"><label>Product title</label><input data-field="name" data-index="${index}" value="${escapeHtml(product.name)}"></div>
                        <div class="builder-two-col"><div class="field"><label>Retail price (PKR)</label><input type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}"></div><div class="field"><label>Discount (%)</label><input type="number" min="0" max="100" step="0.01" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}"></div></div>
                        <div class="field"><label>Customer price</label><input value="PKR ${money(salePrice)}" readonly></div>
                        <div class="builder-two-col"><div class="field"><label>Gender</label><select data-field="gender" data-index="${index}"><option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option><option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option></select></div><div class="field"><label>Pieces</label><input type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${escapeHtml(product.piece_count)}"></div></div>
                        <div class="builder-two-col"><div class="field"><label>Fabric</label><input data-field="fabric" data-index="${index}" value="${escapeHtml(product.fabric)}"></div><div class="field"><label>Season</label><input data-field="season" data-index="${index}" value="${escapeHtml(product.season)}"></div></div>
                        <div class="builder-two-col"><div class="field"><label>Category</label><select data-field="category_id" data-index="${index}">${optionMarkup(categories, product.category_id, 'No category')}</select></div><div class="field"><label>Brand</label><select data-field="brand_id" data-index="${index}">${optionMarkup(brands, product.brand_id, 'No brand')}</select></div></div>
                        <div class="field"><label>Design code</label><input data-field="design_code" data-index="${index}" value="${escapeHtml(product.design_code)}"></div>
                        <div class="field"><label>Description</label><textarea data-field="description" data-index="${index}">${escapeHtml(product.description)}</textarea></div>
                        <div class="builder-two-col"><div class="field"><label>Opening stock</label><input type="number" min="0" data-field="stock_quantity" data-index="${index}" value="${product.stock_quantity}"></div><label class="check"><input type="checkbox" data-field="featured" data-index="${index}" ${product.featured ? 'checked' : ''}> Feature on homepage</label></div>
                        <div class="gallery-order"><h3>Product image order</h3><p>Drag images to set the exact gallery order. Image 1 is the main image on the product page.</p><div class="gallery-list" data-gallery-list="${index}">${gallery || '<span class="muted">Select pages above to add images.</span>'}</div></div>
                        <div class="selected-pages"><strong>${product.pages.length}</strong> image(s) · PDF pages: ${product.pages.length ? product.pages.join(', ') : 'none'}</div>
                    </article>`;
                }).join('');

                productList.querySelectorAll('[data-select]').forEach(button => button.addEventListener('click', () => { activeProduct = Number(button.dataset.select); renderProducts(); updatePageSelection(); }));
                productList.querySelectorAll('[data-remove]').forEach(button => button.addEventListener('click', () => { products.splice(Number(button.dataset.remove), 1); activeProduct = Math.max(0, Math.min(activeProduct, products.length - 1)); renderProducts(); updatePageSelection(); }));
                productList.querySelectorAll('[data-field]').forEach(input => { input.addEventListener('input', updateField); input.addEventListener('change', updateField); });
                productList.querySelectorAll('[data-remove-page]').forEach(button => button.addEventListener('click', event => { event.stopPropagation(); const product = products[Number(button.dataset.product)]; product.pages = product.pages.filter(page => page !== Number(button.dataset.removePage)); renderProducts(); updatePageSelection(); }));
                bindGalleryDragAndDrop();
            }

            function updateField(event) {
                const input = event.currentTarget;
                const product = products[Number(input.dataset.index)];
                product[input.dataset.field] = input.type === 'checkbox' ? input.checked : input.value;
                if (['name', 'retail_price', 'discount_percent'].includes(input.dataset.field)) renderProducts();
            }

            function bindGalleryDragAndDrop() {
                let draggedPage = null;
                let draggedProduct = null;
                productList.querySelectorAll('[data-gallery-product]').forEach(item => {
                    item.addEventListener('dragstart', () => { draggedPage = Number(item.dataset.page); draggedProduct = Number(item.dataset.galleryProduct); item.classList.add('dragging'); });
                    item.addEventListener('dragend', () => item.classList.remove('dragging'));
                    item.addEventListener('dragover', event => event.preventDefault());
                    item.addEventListener('drop', event => {
                        event.preventDefault();
                        const targetPage = Number(item.dataset.page);
                        const targetProduct = Number(item.dataset.galleryProduct);
                        if (draggedPage === null || draggedProduct !== targetProduct || draggedPage === targetPage) return;
                        const pages = products[targetProduct].pages;
                        const fromIndex = pages.indexOf(draggedPage);
                        const toIndex = pages.indexOf(targetPage);
                        if (fromIndex < 0 || toIndex < 0) return;
                        pages.splice(fromIndex, 1);
                        pages.splice(toIndex, 0, draggedPage);
                        renderProducts();
                        updatePageSelection();
                    });
                });
            }

            function updatePageSelection() {
                pageContainer.querySelectorAll('.pdf-page').forEach(card => {
                    const page = Number(card.dataset.page);
                    const selected = products[activeProduct]?.pages.includes(page);
                    card.classList.toggle('selected', selected);
                    card.querySelector('.page-selected')?.remove();
                    if (selected) {
                        const badge = document.createElement('span');
                        badge.className = 'page-selected';
                        badge.textContent = `Image ${products[activeProduct].pages.indexOf(page) + 1}`;
                        card.appendChild(badge);
                    }
                });
            }

            function togglePage(pageNumber) {
                if (!products[activeProduct]) addProduct({}, true);
                const pages = products[activeProduct].pages;
                const index = pages.indexOf(pageNumber);
                if (index >= 0) pages.splice(index, 1); else pages.push(pageNumber);
                renderProducts();
                updatePageSelection();
            }

            async function renderToCanvas(pageNumber, maxDimension) {
                const page = await pdfDocument.getPage(pageNumber);
                const baseViewport = page.getViewport({ scale: 1 });
                const scale = Math.min(maxDimension / Math.max(baseViewport.width, baseViewport.height), 4);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport, background: '#ffffff' }).promise;
                return canvas;
            }

            async function renderThumbnail(pageNumber) {
                const canvas = await renderToCanvas(pageNumber, 620);
                thumbnails.set(pageNumber, { canvas, dataUrl: canvas.toDataURL('image/jpeg', .9) });
            }

            function renderPageGrid() {
                pageContainer.innerHTML = '';
                for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber++) {
                    const thumbnail = thumbnails.get(pageNumber);
                    const card = document.createElement('article');
                    card.className = 'pdf-page';
                    card.dataset.page = String(pageNumber);
                    const imageWrap = document.createElement('div');
                    imageWrap.className = 'page-image-wrap';
                    thumbnail.canvas.style.width = '100%';
                    thumbnail.canvas.style.height = 'auto';
                    imageWrap.appendChild(thumbnail.canvas);
                    const number = document.createElement('span');
                    number.className = 'page-number';
                    number.textContent = `Page ${pageNumber}`;
                    imageWrap.appendChild(number);
                    imageWrap.addEventListener('click', () => togglePage(pageNumber));
                    const footer = document.createElement('div');
                    footer.className = 'page-footer';
                    footer.innerHTML = `<small>Page ${pageNumber}</small><button type="button" class="preview-button">Preview</button>`;
                    footer.querySelector('.preview-button').addEventListener('click', event => { event.stopPropagation(); openPreview(pageNumber); });
                    card.appendChild(imageWrap);
                    card.appendChild(footer);
                    pageContainer.appendChild(card);
                }
                updatePageSelection();
            }

            async function loadPdf(file) {
                if (loading) return;
                loading = true;
                fileStatus.textContent = `Reading ${file.name}…`;
                pageStatus.textContent = 'Opening PDF…';
                pageProgress.style.width = '0%';
                pageContainer.innerHTML = '<div class="empty-state">Opening PDF and preparing page previews…</div>';
                thumbnails = new Map();
                try {
                    const buffer = await file.arrayBuffer();
                    if (!buffer.byteLength) throw new Error('The selected PDF contains no data.');
                    pdfDocument = await pdfjsLib.getDocument({ data: new Uint8Array(buffer) }).promise;
                    products = [];
                    activeProduct = 0;
                    const preset = knownCatalog?.products || [];
                    if (preset.length) preset.forEach(product => addProduct(product, false));
                    else addProduct({ name: 'Design 01', discount_percent: 3, stock_quantity: 3 }, false);
                    activeProduct = 0;
                    for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber++) {
                        pageStatus.textContent = `Preparing preview ${pageNumber} of ${pdfDocument.numPages}…`;
                        pageProgress.style.width = `${Math.round(pageNumber / pdfDocument.numPages * 100)}%`;
                        await renderThumbnail(pageNumber);
                    }
                    renderPageGrid();
                    renderProducts();
                    updatePageSelection();
                    pageStatus.textContent = `${pdfDocument.numPages} pages ready.`;
                    fileStatus.textContent = `${file.name} · ${pdfDocument.numPages} pages loaded.`;
                } catch (error) {
                    console.error(error);
                    pageContainer.innerHTML = `<div class="error-box"><strong>PDF could not be opened.</strong><br>${escapeHtml(error.message || error)}</div>`;
                    pageStatus.textContent = 'PDF error';
                    fileStatus.textContent = 'Could not read this PDF.';
                } finally { loading = false; }
            }

            async function drawPreview(pageNumber) {
                if (!pdfDocument || previewRendering) return;
                previewRendering = true;
                previewPage = pageNumber;
                previewLoading.style.display = 'block';
                previewCanvas.style.display = 'none';
                previewTitle.textContent = `Page ${pageNumber} of ${pdfDocument.numPages}`;
                document.getElementById('previewQuality').textContent = 'Original PDF render · high resolution';
                try {
                    const page = await pdfDocument.getPage(pageNumber);
                    const baseViewport = page.getViewport({ scale: 1 });
                    const fitWidth = Math.min(window.innerWidth * .82, 1100);
                    const fitHeight = Math.min(window.innerHeight * .74, 900);
                    const fitScale = Math.min(fitWidth / baseViewport.width, fitHeight / baseViewport.height);
                    const scale = Math.max(.2, fitScale * previewZoom);
                    const viewport = page.getViewport({ scale });
                    previewCanvas.width = Math.ceil(viewport.width);
                    previewCanvas.height = Math.ceil(viewport.height);
                    await page.render({ canvasContext: previewCanvas.getContext('2d', { alpha: false }), viewport, background: '#ffffff' }).promise;
                    previewCanvas.style.width = `${previewCanvas.width}px`;
                    previewCanvas.style.height = `${previewCanvas.height}px`;
                    previewLoading.style.display = 'none';
                    previewCanvas.style.display = 'block';
                    zoomLabel.textContent = `${Math.round(previewZoom * 100)}%`;
                } finally { previewRendering = false; }
            }

            function openPreview(pageNumber) {
                if (!pdfDocument) return;
                previewZoom = 1;
                previewModal.hidden = false;
                document.body.classList.add('preview-open');
                drawPreview(pageNumber);
            }

            function closePreview() {
                previewModal.hidden = true;
                document.body.classList.remove('preview-open');
            }

            function movePreview(direction) {
                const next = previewPage + direction;
                if (pdfDocument && next >= 1 && next <= pdfDocument.numPages) drawPreview(next);
            }

            document.querySelectorAll('[data-close-preview]').forEach(element => element.addEventListener('click', closePreview));
            document.getElementById('previewPrevious').addEventListener('click', () => movePreview(-1));
            document.getElementById('previewNext').addEventListener('click', () => movePreview(1));
            document.getElementById('zoomIn').addEventListener('click', () => { previewZoom = Math.min(2.5, previewZoom + .25); drawPreview(previewPage); });
            document.getElementById('zoomOut').addEventListener('click', () => { previewZoom = Math.max(.5, previewZoom - .25); drawPreview(previewPage); });
            document.addEventListener('keydown', event => {
                if (previewModal.hidden) return;
                if (event.key === 'Escape') closePreview();
                if (event.key === 'ArrowLeft') movePreview(-1);
                if (event.key === 'ArrowRight') movePreview(1);
            });
            pdfFile.addEventListener('change', event => {
                const file = event.target.files?.[0];
                if (file) loadPdf(file);
            });

            document.getElementById('addDesign').addEventListener('click', () => addProduct({ name: `Design ${String(products.length + 1).padStart(2, '0')}`, discount_percent: 3, stock_quantity: 3 }));

            document.getElementById('createProducts').addEventListener('click', async () => {
                if (!pdfDocument) { builderStatus.textContent = 'Select a PDF first.'; return; }
                if (products.some(product => product.pages.length === 0)) { builderStatus.textContent = 'Every product must have at least one image.'; return; }
                const button = document.getElementById('createProducts');
                button.disabled = true;
                builderStatus.textContent = 'Rendering selected images at high quality…';
                try {
                    const payloadProducts = [];
                    for (let index = 0; index < products.length; index++) {
                        const product = products[index];
                        const images = [];
                        for (let imageIndex = 0; imageIndex < product.pages.length; imageIndex++) {
                            const pageNumber = product.pages[imageIndex];
                            builderStatus.textContent = `Rendering Design ${String(index + 1).padStart(2, '0')} image ${imageIndex + 1} of ${product.pages.length}…`;
                            const canvas = await renderToCanvas(pageNumber, 2200);
                            images.push(canvas.toDataURL('image/jpeg', .95));
                        }
                        payloadProducts.push({
                            ...product,
                            piece_count: product.piece_count === '' ? null : Number(product.piece_count),
                            retail_price: Number(product.retail_price),
                            discount_percent: Number(product.discount_percent),
                            stock_quantity: Number(product.stock_quantity),
                            pages: product.pages,
                            images,
                        });
                    }
                    builderStatus.textContent = 'Uploading high-quality product images…';
                    const response = await fetch(finalizeUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ products: payloadProducts }),
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Product creation failed.');
                    builderStatus.textContent = result.message || 'Products created successfully.';
                    window.location.href = result.redirect;
                } catch (error) {
                    console.error(error);
                    builderStatus.textContent = error.message || 'Product creation failed.';
                    button.disabled = false;
                }
            });
        });
    </script>
@endsection
