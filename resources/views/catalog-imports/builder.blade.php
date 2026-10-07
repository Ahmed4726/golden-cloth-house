@extends('layouts.app')

@section('content')
    <style>
        .catalog-builder { display:grid; grid-template-columns:minmax(0,1fr) minmax(420px,48%); gap:20px; align-items:start; }
        .card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px; box-shadow:0 8px 30px rgba(0,0,0,.05); }
        .builder-heading { display:flex; justify-content:space-between; gap:16px; align-items:center; margin-bottom:16px; }
        .muted { color:#6b7280; }
        .builder-note { padding:12px 14px; border-radius:10px; background:#faf7ef; border:1px solid #eadfca; margin-bottom:16px; font-size:14px; }
        .product-builder-list { display:grid; gap:14px; }
        .design-card { border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; background:#fff; }
        .design-card.active { border-color:#b89035; box-shadow:0 0 0 2px rgba(184,144,53,.12); }
        .design-card-head { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; background:#fafafa; }
        .design-select { border:0; background:none; display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:700; text-align:left; }
        .design-number { width:32px; height:32px; display:grid; place-items:center; border-radius:50%; background:#111827; color:#fff; font-size:12px; }
        .remove-design { border:0; background:#fee2e2; color:#991b1b; width:30px; height:30px; border-radius:8px; cursor:pointer; font-size:18px; }
        .design-form { padding:14px; display:grid; gap:12px; }
        .builder-two-col { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .field { display:grid; gap:6px; }
        .field label, .check { font-size:13px; font-weight:600; color:#374151; }
        .field input, .field select, .field textarea { width:100%; box-sizing:border-box; border:1px solid #d1d5db; border-radius:9px; padding:9px 10px; font:inherit; background:#fff; }
        .field textarea { min-height:90px; resize:vertical; }
        .sale-preview { padding:10px 12px; border-radius:9px; background:#f9fafb; border:1px solid #e5e7eb; }
        .sale-preview span { color:#6b7280; font-size:12px; margin-left:6px; }
        .selected-pages { padding:10px 12px; border-radius:9px; background:#f3f4f6; font-size:13px; }
        .builder-actions { display:flex; align-items:center; gap:12px; margin-top:16px; }
        .pdf-pages { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; max-height:calc(100vh - 190px); overflow:auto; padding-right:4px; }
        .pdf-page { position:relative; display:block; width:100%; border:2px solid #e5e7eb; border-radius:10px; background:#fff; padding:7px; cursor:pointer; text-align:left; }
        .pdf-page:hover { border-color:#b89035; }
        .pdf-page.selected { border-color:#b89035; box-shadow:0 0 0 2px rgba(184,144,53,.18); }
        .pdf-page canvas { display:block; width:100%; height:auto; border-radius:5px; background:#eee; }
        .page-number { display:block; font-size:12px; font-weight:700; padding:3px 2px 7px; }
        .page-selected { position:absolute; right:8px; top:8px; background:#111827; color:#fff; border-radius:6px; padding:4px 7px; font-size:11px; font-weight:700; }
        .progress { height:8px; background:#e5e7eb; border-radius:99px; overflow:hidden; margin:10px 0 4px; }
        .progress > span { display:block; height:100%; width:0; background:#b89035; transition:width .15s ease; }
        .error-box { padding:14px; border-radius:10px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; line-height:1.5; }
        @media (max-width:900px) { .catalog-builder { grid-template-columns:1fr; } .pdf-pages { max-height:none; } }
        @media (max-width:600px) { .builder-two-col { grid-template-columns:1fr; } .pdf-pages { grid-template-columns:1fr 1fr; } }
    </style>

    <div class="top">
        <div>
            <h1>Product Builder</h1>
            <p class="muted">{{ $catalogImport->name }} · Select catalogue pages, review product information, then create the listings.</p>
        </div>
        <a class="btn secondary" href="{{ route('catalog-imports.index') }}">Back to Catalogs</a>
    </div>

    <div id="catalogBuilder" class="catalog-builder"
         data-pdf-url="{{ route('catalog-imports.pdf', $catalogImport) }}"
         data-finalize-url="{{ route('catalog-imports.finalize', $catalogImport) }}">
        <section class="card">
            <div class="builder-heading">
                <div><strong>Products / Designs</strong><div class="muted">Known catalog layouts are pre-filled when detected.</div></div>
                <button class="btn secondary" type="button" id="addDesign">+ Add Design</button>
            </div>

            <div class="builder-note"><strong>Pricing:</strong> customer price is calculated from retail price using the default <strong>3% discount</strong>.</div>
            <div id="productList" class="product-builder-list"></div>
            <div class="builder-actions">
                <button class="btn" type="button" id="createProducts">Create Selected Products</button>
                <span id="builderStatus" class="muted"></span>
            </div>
        </section>

        <aside class="card">
            <div class="builder-heading">
                <div><strong>PDF Pages</strong><div class="muted">Click a page to add/remove it from the active design.</div></div>
                <span id="pageStatus" class="badge">Starting…</span>
            </div>
            <div class="progress"><span id="pageProgress"></span></div>
            <div id="pdfPages" class="pdf-pages"><div class="muted">Preparing PDF…</div></div>
        </aside>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        (() => {
            'use strict';

            const root = document.getElementById('catalogBuilder');
            const pdfUrl = root.dataset.pdfUrl;
            const finalizeUrl = root.dataset.finalizeUrl;
            const pageContainer = document.getElementById('pdfPages');
            const productList = document.getElementById('productList');
            const pageStatus = document.getElementById('pageStatus');
            const pageProgress = document.getElementById('pageProgress');
            const builderStatus = document.getElementById('builderStatus');
            const products = [];
            let pdfDocument = null;
            let activeProduct = 0;

            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            const catalogName = @json(strtolower($catalogImport->name));
            const categories = @json($categories->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values());
            const brands = @json($brands->map(fn ($item) => ['id' => $item->id, 'name' => $item->name])->values());

            const knownCatalog = catalogName.includes('lime')
                ? {
                    collection: 'Lime Vol-552', gender: 'women', retail: 6200,
                    fabric: 'Peach Slub', season: 'Winter', piece_count: '',
                    description: 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                    groups: [[2,3,4],[5,6,7],[8,9,10,11],[12,13,14,15],[16,17,18],[19,20,21,22]]
                }
                : catalogName.includes('leore')
                    ? {
                        collection: 'Leore Vol-01', gender: 'women', retail: 7800,
                        fabric: 'Fancy Zari Slub Dhanak', season: 'Winter', piece_count: 3,
                        description: 'Premium embroidered fancy zari slub dhanak shirt with fancy zari slub embroidered lace dupatta and comfort of same trouser.',
                        groups: [[2,3,4,5,6],[7,8,9,10,11],[12,13,14,15,16],[17,18,19],[20,21,22],[23,24]]
                    }
                    : null;

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>\"']/g, char => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
            }

            function optionMarkup(items, selected, emptyLabel) {
                return `<option value="">${emptyLabel}</option>` + items.map(item =>
                    `<option value="${item.id}" ${String(item.id) === String(selected) ? 'selected' : ''}>${escapeHtml(item.name)}</option>`
                ).join('');
            }

            function addProduct(seed = {}) {
                const number = products.length + 1;
                products.push({
                    name: seed.name || `Unstitched Design ${String(number).padStart(2,'0')}`,
                    description: seed.description || '',
                    retail_price: seed.retail_price ?? '',
                    discount_percent: seed.discount_percent ?? 3,
                    stock_quantity: seed.stock_quantity ?? 3,
                    gender: seed.gender || 'women',
                    piece_count: seed.piece_count || '',
                    fabric: seed.fabric || '',
                    season: seed.season || '',
                    design_code: seed.design_code || `DES-${String(number).padStart(2,'0')}`,
                    category_id: seed.category_id || '',
                    brand_id: seed.brand_id || '',
                    featured: seed.featured ?? false,
                    pages: new Set(seed.pages || [])
                });
                activeProduct = products.length - 1;
                renderProducts();
                updatePageSelection();
            }

            function salePrice(product) {
                const retail = Number(product.retail_price || 0);
                const discount = Number(product.discount_percent || 0);
                return retail ? Math.round(retail * (1 - discount / 100)) : 0;
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => `
                    <article class="design-card ${index === activeProduct ? 'active' : ''}">
                        <div class="design-card-head">
                            <button type="button" class="design-select" data-select="${index}">
                                <span class="design-number">${String(index + 1).padStart(2,'0')}</span>
                                <span>${escapeHtml(product.name)}</span>
                            </button>
                            ${products.length > 1 ? `<button type="button" class="remove-design" data-remove="${index}">×</button>` : ''}
                        </div>
                        <div class="design-form">
                            <div class="field"><label>Product title</label><input data-field="name" data-index="${index}" value="${escapeHtml(product.name)}"></div>
                            <div class="builder-two-col">
                                <div class="field"><label>Retail price (PKR)</label><input type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}"></div>
                                <div class="field"><label>Discount %</label><input type="number" min="0" max="100" step="0.1" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}"></div>
                            </div>
                            <div class="sale-preview">Customer price: <strong>PKR ${salePrice(product).toLocaleString()}</strong> <span>after ${product.discount_percent}% discount</span></div>
                            <div class="builder-two-col">
                                <div class="field"><label>Gender</label><select data-field="gender" data-index="${index}"><option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option><option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option></select></div>
                                <div class="field"><label>Pieces</label><input type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${product.piece_count}" placeholder="e.g. 3"></div>
                            </div>
                            <div class="builder-two-col">
                                <div class="field"><label>Fabric</label><input data-field="fabric" data-index="${index}" value="${escapeHtml(product.fabric)}"></div>
                                <div class="field"><label>Season</label><input data-field="season" data-index="${index}" value="${escapeHtml(product.season)}"></div>
                            </div>
                            <div class="builder-two-col">
                                <div class="field"><label>Category</label><select data-field="category_id" data-index="${index}">${optionMarkup(categories, product.category_id, 'No category')}</select></div>
                                <div class="field"><label>Brand</label><select data-field="brand_id" data-index="${index}">${optionMarkup(brands, product.brand_id, 'No brand')}</select></div>
                            </div>
                            <div class="field"><label>Design code</label><input data-field="design_code" data-index="${index}" value="${escapeHtml(product.design_code)}"></div>
                            <div class="field"><label>Description</label><textarea data-field="description" data-index="${index}">${escapeHtml(product.description)}</textarea></div>
                            <div class="builder-two-col">
                                <div class="field"><label>Opening stock</label><input type="number" min="0" data-field="stock_quantity" data-index="${index}" value="${product.stock_quantity}"></div>
                                <label class="check"><input type="checkbox" data-field="featured" data-index="${index}" ${product.featured ? 'checked' : ''}> Feature on homepage</label>
                            </div>
                            <div class="selected-pages"><strong>${product.pages.size}</strong> page(s) selected: ${product.pages.size ? [...product.pages].sort((a,b)=>a-b).join(', ') : 'none yet'}</div>
                        </div>
                    </article>
                `).join('');

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

                productList.querySelectorAll('[data-field]').forEach(input => {
                    input.addEventListener('input', updateField);
                    input.addEventListener('change', updateField);
                });
            }

            function updateField(event) {
                const input = event.currentTarget;
                const product = products[Number(input.dataset.index)];
                product[input.dataset.field] = input.type === 'checkbox' ? input.checked : input.value;
                if (['name','retail_price','discount_percent'].includes(input.dataset.field)) {
                    renderProducts();
                    updatePageSelection();
                }
            }

            function updatePageSelection() {
                pageContainer.querySelectorAll('[data-page]').forEach(card => {
                    const selected = products[activeProduct]?.pages.has(Number(card.dataset.page));
                    card.classList.toggle('selected', selected);
                    card.querySelector('.page-selected')?.remove();
                    if (selected) {
                        const badge = document.createElement('span');
                        badge.className = 'page-selected';
                        badge.textContent = 'Selected';
                        card.appendChild(badge);
                    }
                });
            }

            async function renderThumbnail(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const base = page.getViewport({ scale: 1 });
                const scale = Math.min(0.48, 460 / base.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport }).promise;
                return canvas;
            }

            async function renderSelectedImage(pageNumber) {
                const page = await pdfDocument.getPage(pageNumber);
                const base = page.getViewport({ scale: 1 });
                const scale = Math.min(1.5, 1500 / base.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = Math.ceil(viewport.width);
                canvas.height = Math.ceil(viewport.height);
                await page.render({ canvasContext: canvas.getContext('2d', { alpha: false }), viewport }).promise;
                return canvas.toDataURL('image/jpeg', 0.9);
            }

            function togglePage(pageNumber) {
                if (!products[activeProduct]) return;
                const pages = products[activeProduct].pages;
                pages.has(pageNumber) ? pages.delete(pageNumber) : pages.add(pageNumber);
                renderProducts();
                updatePageSelection();
            }

            async function renderPdf() {
                pageStatus.textContent = 'Opening PDF…';
                pageContainer.innerHTML = '<div class="muted">Opening uploaded PDF…</div>';
                try {
                    const response = await fetch(pdfUrl, { credentials: 'same-origin', cache: 'no-store' });
                    if (!response.ok) throw new Error(`PDF request failed with HTTP ${response.status}.`);
                    const buffer = await response.arrayBuffer();
                    if (!buffer.byteLength) throw new Error('The uploaded PDF is empty.');

                    pdfDocument = await pdfjsLib.getDocument({ data: buffer }).promise;
                    pageContainer.innerHTML = '';
                    pageStatus.textContent = `${pdfDocument.numPages} pages`;

                    for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber++) {
                        pageStatus.textContent = `Rendering page ${pageNumber} of ${pdfDocument.numPages}…`;
                        pageProgress.style.width = `${Math.round((pageNumber / pdfDocument.numPages) * 100)}%`;

                        const card = document.createElement('button');
                        card.type = 'button';
                        card.className = 'pdf-page';
                        card.dataset.page = pageNumber;
                        const label = document.createElement('span');
                        label.className = 'page-number';
                        label.textContent = `Page ${pageNumber}`;
                        card.appendChild(label);
                        card.appendChild(await renderThumbnail(pageNumber));
                        card.addEventListener('click', () => togglePage(pageNumber));
                        pageContainer.appendChild(card);

                        await new Promise(resolve => setTimeout(resolve, 0));
                    }

                    pageStatus.textContent = `${pdfDocument.numPages} pages ready`;
                    pageProgress.style.width = '100%';
                    updatePageSelection();
                } catch (error) {
                    console.error('Catalog PDF builder error:', error);
                    pageStatus.textContent = 'PDF failed';
                    pageContainer.innerHTML = `<div class="error-box"><strong>PDF could not be rendered.</strong><br>${escapeHtml(error.message)}<br><br>The builder now loads the PDF through the authenticated Laravel PDF endpoint, so <code>php artisan storage:link</code> is not required for this step.</div>`;
                }
            }

            async function createProducts() {
                if (!products.length) return;
                for (const product of products) {
                    if (!product.pages.size) {
                        alert(`Select at least one PDF page for “${product.name}”.`);
                        return;
                    }
                }

                const button = document.getElementById('createProducts');
                button.disabled = true;
                builderStatus.textContent = 'Rendering selected product images…';

                try {
                    const payload = [];
                    let productNumber = 0;
                    for (const product of products) {
                        productNumber++;
                        const images = [];
                        const pages = [...product.pages].sort((a,b) => a-b);
                        for (let i = 0; i < pages.length; i++) {
                            builderStatus.textContent = `Rendering images for product ${productNumber}/${products.length}: ${i + 1}/${pages.length}…`;
                            images.push(await renderSelectedImage(pages[i]));
                        }
                        payload.push({ ...product, pages: undefined, images });
                    }

                    builderStatus.textContent = 'Creating products…';
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    const response = await fetch(finalizeUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(token ? {'X-CSRF-TOKEN': token} : {})
                        },
                        body: JSON.stringify({ products: payload })
                    });

                    const result = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(result.message || `Server returned HTTP ${response.status}.`);
                    builderStatus.textContent = result.message || 'Products created successfully.';
                    window.location.href = result.redirect || '{{ route('products.index') }}';
                } catch (error) {
                    console.error('Product creation error:', error);
                    builderStatus.textContent = `Error: ${error.message}`;
                    alert(error.message);
                    button.disabled = false;
                }
            }

            document.getElementById('addDesign').addEventListener('click', () => addProduct());
            document.getElementById('createProducts').addEventListener('click', createProducts);

            if (knownCatalog) {
                knownCatalog.groups.forEach((pages, index) => addProduct({
                    name: `${knownCatalog.collection} — Design ${String(index + 1).padStart(2,'0')}`,
                    description: knownCatalog.description,
                    retail_price: knownCatalog.retail,
                    discount_percent: 3,
                    gender: knownCatalog.gender,
                    piece_count: knownCatalog.piece_count,
                    fabric: knownCatalog.fabric,
                    season: knownCatalog.season,
                    design_code: `DES-${String(index + 1).padStart(2,'0')}`,
                    pages
                }));
            } else {
                addProduct();
            }

            renderPdf();
        })();
    </script>
@endpush