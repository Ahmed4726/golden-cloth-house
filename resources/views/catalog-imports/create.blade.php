@extends('layouts.app')

@section('title', 'Catalog Product Builder')

@section('content')
    <div class="catalog-page">
        <div class="catalog-header">
            <div>
                <p class="eyebrow">Catalog import</p>
                <h1>PDF Product Builder</h1>
                <p class="muted">
                    The PDF is rendered directly from your computer. Laravel never has to stream the PDF back to the browser.
                </p>
            </div>
            <a class="btn secondary" href="{{ route('catalog-imports.index') }}">Back to Imports</a>
        </div>

        @if (session('info'))
            <div class="notice">{{ session('info') }}</div>
        @endif

        <section id="uploadPanel" class="panel upload-panel">
            <div class="upload-copy">
                <span class="upload-icon">PDF</span>
                <div>
                    <h2>Choose your catalogue</h2>
                    <p class="muted">Select the original PDF from your computer. Maximum 50 MB.</p>
                </div>
            </div>

            <input id="pdfInput" type="file" accept="application/pdf">
            <button id="startButton" class="btn primary" type="button">Open Catalogue</button>
            <div id="uploadStatus" class="status"></div>
        </section>

        <div id="builder" class="hidden">
            <div class="builder-grid">
                <section class="panel pages-panel">
                    <div class="panel-title">
                        <div>
                            <p class="eyebrow">Step 1</p>
                            <h2 id="catalogName">Catalogue pages</h2>
                            <p id="pageHint" class="muted">Select pages for the active design.</p>
                        </div>
                        <div id="pageStatus" class="status"></div>
                    </div>

                    <div id="pageContainer" class="page-grid"></div>
                </section>

                <aside class="panel products-panel">
                    <div class="panel-title">
                        <div>
                            <p class="eyebrow">Step 2</p>
                            <h2>Products</h2>
                            <p class="muted">Choose a design, then click its catalogue pages.</p>
                        </div>
                        <button id="addDesign" class="btn secondary" type="button">+ Design</button>
                    </div>

                    <div id="productList"></div>

                    <div class="actions">
                        <button id="createProducts" class="btn primary" type="button">Create Products</button>
                        <div id="builderStatus" class="status"></div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <style>
        .catalog-page { max-width: 1600px; margin: 0 auto; padding: 28px 24px 50px; }
        .catalog-header, .panel-title, .actions, .upload-copy { display: flex; align-items: center; justify-content: space-between; gap: 18px; }
        .catalog-header { margin-bottom: 24px; }
        .eyebrow { margin: 0 0 5px; font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: #8b6a32; }
        h1, h2, h3, p { margin-top: 0; }
        h1 { margin-bottom: 7px; font-size: clamp(28px, 4vw, 42px); }
        h2 { margin-bottom: 4px; font-size: 21px; }
        .muted { color: #6b7280; }
        .hidden { display: none !important; }
        .notice { margin-bottom: 18px; padding: 14px 16px; border: 1px solid #fde68a; border-radius: 12px; background: #fffbeb; }
        .panel { border: 1px solid #e5e7eb; border-radius: 18px; background: #fff; box-shadow: 0 10px 35px rgba(0,0,0,.05); }
        .upload-panel { display: grid; grid-template-columns: 1fr auto auto; align-items: center; gap: 18px; padding: 22px; margin-bottom: 24px; }
        .upload-icon { display: inline-flex; align-items: center; justify-content: center; width: 54px; height: 54px; border-radius: 14px; background: #111827; color: #fff; font-weight: 800; }
        .upload-panel input[type=file] { max-width: 390px; }
        .status { min-height: 20px; font-size: 13px; color: #6b7280; }
        .builder-grid { display: grid; grid-template-columns: minmax(0, 1.5fr) minmax(380px, .75fr); gap: 24px; align-items: start; }
        .pages-panel, .products-panel { padding: 20px; }
        .products-panel { position: sticky; top: 18px; }
        .panel-title { margin-bottom: 18px; align-items: flex-start; }
        .page-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 14px; }
        .page-card { position: relative; overflow: hidden; border: 2px solid #e5e7eb; border-radius: 13px; background: #f8fafc; cursor: pointer; transition: .15s ease; }
        .page-card:hover { transform: translateY(-1px); border-color: #9ca3af; }
        .page-card.selected { border-color: #111827; box-shadow: 0 0 0 2px #111827 inset; }
        .page-card img { display: block; width: 100%; height: auto; aspect-ratio: 0.72; object-fit: contain; background: #f3f4f6; }
        .page-number { position: absolute; top: 8px; left: 8px; padding: 4px 8px; border-radius: 99px; background: rgba(255,255,255,.95); font-size: 11px; font-weight: 800; }
        .selected-badge { position: absolute; top: 8px; right: 8px; padding: 4px 8px; border-radius: 99px; background: #111827; color: #fff; font-size: 11px; font-weight: 800; }
        .product-card { margin-bottom: 14px; padding: 14px; border: 1px solid #e5e7eb; border-radius: 14px; }
        .product-card.active { border-color: #111827; box-shadow: 0 0 0 1px #111827 inset; }
        .product-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 12px; }
        .product-head button { border: 0; background: transparent; cursor: pointer; font-weight: 700; }
        .field { margin-bottom: 11px; }
        .field label { display: block; margin-bottom: 5px; font-size: 12px; font-weight: 750; }
        .field input, .field select, .field textarea { width: 100%; box-sizing: border-box; padding: 9px 10px; border: 1px solid #d1d5db; border-radius: 9px; background: #fff; }
        .field textarea { min-height: 78px; resize: vertical; }
        .two { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .check { display: flex; align-items: center; gap: 7px; margin-top: 25px; font-size: 12px; }
        .selected-pages { padding-top: 4px; font-size: 12px; color: #6b7280; line-height: 1.5; }
        .actions { padding-top: 15px; border-top: 1px solid #e5e7eb; align-items: flex-start; }
        .btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 15px; border: 0; border-radius: 9px; text-decoration: none; cursor: pointer; font-weight: 750; }
        .btn.primary { color: #fff; background: #111827; }
        .btn.secondary { color: #111827; background: #f3f4f6; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        .error-box { margin-top: 10px; padding: 12px; border: 1px solid #fecaca; border-radius: 10px; background: #fef2f2; color: #991b1b; line-height: 1.5; }
        .success-box { margin-top: 10px; padding: 12px; border: 1px solid #bbf7d0; border-radius: 10px; background: #f0fdf4; color: #166534; }
        @media (max-width: 1050px) { .builder-grid { grid-template-columns: 1fr; } .products-panel { position: static; } }
        @media (max-width: 760px) { .catalog-page { padding: 18px 12px 35px; } .catalog-header, .upload-copy, .actions { align-items: stretch; flex-direction: column; } .upload-panel { grid-template-columns: 1fr; } .upload-panel input[type=file] { max-width: none; } .two { grid-template-columns: 1fr; } .check { margin-top: 0; } }
    </style>

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

            let pdfDocument = null;
            let importId = null;
            let finalizeUrl = null;
            let products = [];
            let activeProduct = 0;
            const pageCanvases = new Map();

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

            function addProduct(data = {}) {
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
                    pages: new Set(data.pages || []),
                });
                activeProduct = products.length - 1;
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => {
                    const salePrice = Number(product.retail_price) * (1 - Number(product.discount_percent) / 100);

                    return `
                        <article class="product-card ${index === activeProduct ? 'active' : ''}">
                            <div class="product-head">
                                <strong>Design ${String(index + 1).padStart(2, '0')}</strong>
                                <div>
                                    <button type="button" data-select="${index}">${index === activeProduct ? 'Editing' : 'Select'}</button>
                                    ${products.length > 1 ? `<button type="button" data-remove="${index}">Remove</button>` : ''}
                                </div>
                            </div>
                            <div class="field"><label>Product title</label><input data-field="name" data-index="${index}" value="${escapeHtml(product.name)}"></div>
                            <div class="two">
                                <div class="field"><label>Retail price (PKR)</label><input type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}"></div>
                                <div class="field"><label>Discount (%)</label><input type="number" min="0" max="100" step="0.01" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}"></div>
                            </div>
                            <div class="field"><label>Customer price</label><input value="PKR ${money(salePrice)}" readonly></div>
                            <div class="two">
                                <div class="field"><label>Gender</label><select data-field="gender" data-index="${index}"><option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option><option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option></select></div>
                                <div class="field"><label>Pieces</label><input type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${escapeHtml(product.piece_count)}"></div>
                            </div>
                            <div class="two">
                                <div class="field"><label>Fabric</label><input data-field="fabric" data-index="${index}" value="${escapeHtml(product.fabric)}"></div>
                                <div class="field"><label>Season</label><input data-field="season" data-index="${index}" value="${escapeHtml(product.season)}"></div>
                            </div>
                            <div class="two">
                                <div class="field"><label>Category</label><select data-field="category_id" data-index="${index}">${optionMarkup(categories, product.category_id, 'No category')}</select></div>
                                <div class="field"><label>Brand</label><select data-field="brand_id" data-index="${index}">${optionMarkup(brands, product.brand_id, 'No brand')}</select></div>
                            </div>
                            <div class="field"><label>Design code</label><input data-field="design_code" data-index="${index}" value="${escapeHtml(product.design_code)}"></div>
                            <div class="field"><label>Description</label><textarea data-field="description" data-index="${index}">${escapeHtml(product.description)}</textarea></div>
                            <div class="two">
                                <div class="field"><label>Opening stock</label><input type="number" min="0" data-field="stock_quantity" data-index="${index}" value="${product.stock_quantity}"></div>
                                <label class="check"><input type="checkbox" data-field="featured" data-index="${index}" ${product.featured ? 'checked' : ''}> Feature on homepage</label>
                            </div>
                            <div class="selected-pages"><strong>${product.pages.size}</strong> page(s): ${product.pages.size ? [...product.pages].sort((a,b) => a-b).join(', ') : 'none selected'}</div>
                        </article>
                    `;
                }).join('');

                productList.querySelectorAll('[data-select]').forEach(button => button.addEventListener('click', () => {
                    activeProduct = Number(button.dataset.select);
                    renderProducts();
                    updatePageSelection();
                }));

                productList.querySelectorAll('[data-remove]').forEach(button => button.addEventListener('click', () => {
                    products.splice(Number(button.dataset.remove), 1);
                    activeProduct = Math.max(0, Math.min(activeProduct, products.length - 1));
                    renderProducts();
                    updatePageSelection();
                }));

                productList.querySelectorAll('[data-field]').forEach(element => {
                    element.addEventListener('input', updateField);
                    element.addEventListener('change', updateField);
                });
            }

            function updateField(event) {
                const element = event.currentTarget;
                const product = products[Number(element.dataset.index)];
                product[element.dataset.field] = element.type === 'checkbox' ? element.checked : element.value;

                if (['name', 'retail_price', 'discount_percent'].includes(element.dataset.field)) {
                    renderProducts();
                }
            }

            function updatePageSelection() {
                pageContainer.querySelectorAll('[data-page]').forEach(card => {
                    const page = Number(card.dataset.page);
                    const selected = products[activeProduct]?.pages.has(page);
                    card.classList.toggle('selected', selected);
                    card.querySelector('.selected-badge')?.remove();

                    if (selected) {
                        const badge = document.createElement('span');
                        badge.className = 'selected-badge';
                        badge.textContent = 'Selected';
                        card.appendChild(badge);
                    }
                });
            }

            async function renderPage(pageNumber, scale = 0.55) {
                const page = await pdfDocument.getPage(pageNumber);
                const viewportAtOne = page.getViewport({ scale: 1 });
                const actualScale = Math.min(scale, 950 / viewportAtOne.width);
                const viewport = page.getViewport({ scale: actualScale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({
                    canvasContext: canvas.getContext('2d', { alpha: false }),
                    viewport,
                }).promise;
                return canvas;
            }

            async function renderPages() {
                pageContainer.innerHTML = '';
                pageCanvases.clear();
                const total = pdfDocument.numPages;

                for (let pageNumber = 1; pageNumber <= total; pageNumber++) {
                    pageStatus.textContent = `Rendering page ${pageNumber} of ${total}…`;
                    const canvas = await renderPage(pageNumber);
                    pageCanvases.set(pageNumber, canvas);

                    const card = document.createElement('button');
                    card.type = 'button';
                    card.className = 'page-card';
                    card.dataset.page = pageNumber;
                    card.innerHTML = `<span class="page-number">Page ${pageNumber}</span>`;
                    card.appendChild(canvas);
                    card.addEventListener('click', () => {
                        if (!products[activeProduct]) return;
                        if (products[activeProduct].pages.has(pageNumber)) {
                            products[activeProduct].pages.delete(pageNumber);
                        } else {
                            products[activeProduct].pages.add(pageNumber);
                        }
                        updatePageSelection();
                        renderProducts();
                    });
                    pageContainer.appendChild(card);
                }

                pageStatus.textContent = `${total} pages ready`;
                updatePageSelection();
            }

            async function selectedPageImage(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const original = page.getViewport({ scale: 1 });
                const scale = Math.min(1.35, 1200 / original.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);

                await page.render({
                    canvasContext: canvas.getContext('2d', { alpha: false }),
                    viewport,
                }).promise;

                return canvas.toDataURL('image/jpeg', 0.78);
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

            startButton.addEventListener('click', async () => {
                const file = input.files?.[0];

                if (!file) {
                    uploadStatus.innerHTML = '<div class="error-box">Please select a PDF first.</div>';
                    return;
                }

                if (file.type !== 'application/pdf') {
                    uploadStatus.innerHTML = '<div class="error-box">Please select a valid PDF file.</div>';
                    return;
                }

                startButton.disabled = true;
                uploadStatus.textContent = 'Saving PDF and preparing the local viewer…';

                try {
                    const payload = await uploadPdf(file);
                    importId = payload.import_id;
                    finalizeUrl = payload.finalize_url;
                    catalogName.textContent = payload.name || file.name;

                    const buffer = await file.arrayBuffer();
                    pdfDocument = await pdfjsLib.getDocument({ data: new Uint8Array(buffer) }).promise;

                    uploadPanel.classList.add('hidden');
                    builder.classList.remove('hidden');

                    if (payload.preset?.groups?.length) {
                        payload.preset.groups.forEach((pages, index) => {
                            addProduct({
                                name: `${payload.preset.collection} — Design ${String(index + 1).padStart(2, '0')}`,
                                description: payload.preset.description,
                                retail_price: payload.preset.retail_price,
                                discount_percent: payload.preset.discount_percent ?? 3,
                                gender: payload.preset.gender,
                                piece_count: payload.preset.piece_count,
                                fabric: payload.preset.fabric,
                                season: payload.preset.season,
                                pages,
                            });
                        });
                    } else {
                        addProduct({ name: 'Design 01', discount_percent: 3, stock_quantity: 3 });
                    }

                    renderProducts();
                    await renderPages();
                } catch (error) {
                    console.error(error);
                    uploadStatus.innerHTML = `<div class="error-box">${escapeHtml(error.message || 'Unable to open the PDF.')}</div>`;
                    startButton.disabled = false;
                }
            });

            addDesign.addEventListener('click', () => {
                addProduct({ discount_percent: 3, stock_quantity: 3 });
                renderProducts();
                updatePageSelection();
            });

            createProducts.addEventListener('click', async () => {
                if (!finalizeUrl || !pdfDocument) return;

                const invalid = products.find(product => product.pages.size === 0);
                if (invalid) {
                    builderStatus.innerHTML = '<div class="error-box">Every design needs at least one selected page.</div>';
                    return;
                }

                createProducts.disabled = true;
                builderStatus.textContent = 'Rendering selected product images…';

                try {
                    const payloadProducts = [];

                    for (let index = 0; index < products.length; index++) {
                        const product = products[index];
                        const pageNumbers = [...product.pages].sort((a, b) => a - b);
                        const images = [];

                        for (const pageNumber of pageNumbers) {
                            builderStatus.textContent = `Preparing images for Design ${String(index + 1).padStart(2, '0')} — page ${pageNumber}…`;
                            images.push(await selectedPageImage(pageNumber));
                        }

                        payloadProducts.push({
                            ...product,
                            piece_count: product.piece_count === '' ? null : Number(product.piece_count),
                            category_id: product.category_id === '' ? null : Number(product.category_id),
                            brand_id: product.brand_id === '' ? null : Number(product.brand_id),
                            retail_price: Number(product.retail_price),
                            discount_percent: Number(product.discount_percent),
                            stock_quantity: Number(product.stock_quantity),
                            pages: undefined,
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

                    builderStatus.innerHTML = `<div class="success-box">${escapeHtml(payload.message || 'Products created successfully.')}</div>`;
                    setTimeout(() => window.location.href = payload.redirect, 700);
                } catch (error) {
                    console.error(error);
                    builderStatus.innerHTML = `<div class="error-box">${escapeHtml(error.message || 'Product creation failed.')}</div>`;
                    createProducts.disabled = false;
                }
            });
        })();
    </script>
@endsection
