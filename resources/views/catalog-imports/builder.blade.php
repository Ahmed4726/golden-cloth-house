@extends('layouts.app')

@section('title', 'Catalog Product Builder')

@section('content')
    <div class="container">
        <div class="page-header">
            <div>
                <p class="eyebrow">Catalog import</p>
                <h1>Product Builder</h1>
                <p class="muted">Select the PDF pages that belong to each product, review the product details, then create the listings.</p>
            </div>
            <a class="button button-secondary" href="{{ route('catalog-imports.index') }}">Back to Imports</a>
        </div>

        <div class="builder-layout">
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>{{ $catalogImport->name }}</h2>
                        <p class="muted">PDF pages</p>
                    </div>
                    <div class="progress-wrap">
                        <span id="pageStatus" class="status">Preparing PDF…</span>
                        <div class="progress"><span id="pageProgress"></span></div>
                    </div>
                </div>
                <div id="pageContainer" class="pdf-grid">
                    <div class="muted">Opening uploaded PDF…</div>
                </div>
            </section>

            <aside class="panel product-panel">
                <div class="panel-header">
                    <div>
                        <h2>Products</h2>
                        <p class="muted">Review each design before creating listings.</p>
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

    <style>
        .container { max-width: 1500px; margin: 0 auto; padding: 24px; }
        .page-header, .panel-header, .builder-actions { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .page-header { margin-bottom: 24px; }
        .eyebrow { margin: 0 0 4px; font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        h1, h2 { margin: 0; }
        .muted { color: #6b7280; }
        .builder-layout { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(360px, .8fr); gap: 24px; align-items: start; }
        .panel { padding: 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 18px; box-shadow: 0 8px 30px rgba(0,0,0,.05); }
        .panel-header { margin-bottom: 18px; }
        .progress-wrap { min-width: 230px; }
        .status { display: block; margin-bottom: 6px; font-size: 13px; text-align: right; }
        .progress { height: 6px; overflow: hidden; background: #e5e7eb; border-radius: 99px; }
        .progress span { display: block; width: 0; height: 100%; transition: width .2s ease; background: #111827; }
        .pdf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
        .pdf-page { position: relative; display: flex; flex-direction: column; align-items: stretch; padding: 0; overflow: hidden; border: 2px solid #e5e7eb; border-radius: 12px; background: #fff; cursor: pointer; }
        .pdf-page.selected { border-color: #111827; box-shadow: 0 0 0 2px #111827 inset; }
        .pdf-page canvas { display: block; width: 100%; height: auto; background: #f8fafc; }
        .page-number, .page-selected { position: absolute; z-index: 2; top: 8px; padding: 4px 8px; font-size: 12px; font-weight: 700; border-radius: 999px; background: rgba(255,255,255,.94); }
        .page-number { left: 8px; }
        .page-selected { right: 8px; }
        .product-panel { position: sticky; top: 20px; }
        .product-card { margin-bottom: 14px; padding: 14px; border: 1px solid #e5e7eb; border-radius: 14px; }
        .product-card.active { border-color: #111827; }
        .product-card-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
        .product-card-header button { border: 0; background: transparent; cursor: pointer; }
        .field { margin-bottom: 12px; }
        .field label { display: block; margin-bottom: 5px; font-size: 12px; font-weight: 700; }
        .field input, .field select, .field textarea { width: 100%; box-sizing: border-box; padding: 9px 10px; border: 1px solid #d1d5db; border-radius: 9px; background: #fff; }
        .field textarea { min-height: 80px; resize: vertical; }
        .builder-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .check { display: flex; align-items: center; gap: 7px; margin-top: 25px; font-size: 13px; }
        .selected-pages { padding-top: 8px; font-size: 12px; color: #6b7280; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 0 14px; border: 0; border-radius: 9px; text-decoration: none; cursor: pointer; font-weight: 700; }
        .button-primary { color: #fff; background: #111827; }
        .button-secondary { color: #111827; background: #f3f4f6; }
        .builder-actions { padding-top: 16px; border-top: 1px solid #e5e7eb; }
        .error-box { padding: 16px; border: 1px solid #fecaca; border-radius: 12px; color: #991b1b; background: #fef2f2; line-height: 1.6; }
        @media (max-width: 1000px) { .builder-layout { grid-template-columns: 1fr; } .product-panel { position: static; } }
        @media (max-width: 640px) { .container { padding: 14px; } .page-header, .panel-header, .builder-actions { align-items: stretch; flex-direction: column; } .progress-wrap { min-width: 0; } .status { text-align: left; } .builder-two-col { grid-template-columns: 1fr; } .check { margin-top: 0; } }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        (() => {
            const pdfUrl = @json(route('catalog-imports.pdf', $catalogImport));
            const finalizeUrl = @json(route('catalog-imports.finalize', $catalogImport));
            const categories = @json($categories);
            const brands = @json($brands);
            const knownCatalog = @json($catalogImport->presetData());

            const pageContainer = document.getElementById('pageContainer');
            const pageStatus = document.getElementById('pageStatus');
            const pageProgress = document.getElementById('pageProgress');
            const productList = document.getElementById('productList');
            const builderStatus = document.getElementById('builderStatus');

            let pdfDocument = null;
            let products = [];
            let activeProduct = 0;

            window.addEventListener('error', event => {
                console.error('Catalog builder JavaScript error:', event.error || event.message);
            });

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
                const options = [`<option value="">${escapeHtml(emptyLabel)}</option>`];
                items.forEach(item => {
                    const isSelected = String(item.id) === String(selected) ? 'selected' : '';
                    options.push(`<option value="${item.id}" ${isSelected}>${escapeHtml(item.name)}</option>`);
                });
                return options.join('');
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
                renderProducts();
                updatePageSelection();
            }

            function renderProducts() {
                productList.innerHTML = products.map((product, index) => {
                    const salePrice = Number(product.retail_price) * (1 - (Number(product.discount_percent) / 100));
                    return `
                        <article class="product-card ${index === activeProduct ? 'active' : ''}">
                            <div class="product-card-header">
                                <strong>Design ${String(index + 1).padStart(2, '0')}</strong>
                                <div>
                                    <button type="button" data-select="${index}">${index === activeProduct ? 'Editing' : 'Select'}</button>
                                    ${products.length > 1 ? `<button type="button" data-remove="${index}">Remove</button>` : ''}
                                </div>
                            </div>
                            <div class="field"><label>Product title</label><input data-field="name" data-index="${index}" value="${escapeHtml(product.name)}"></div>
                            <div class="builder-two-col">
                                <div class="field"><label>Retail price (PKR)</label><input type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}"></div>
                                <div class="field"><label>Discount (%)</label><input type="number" min="0" max="100" step="0.01" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}"></div>
                            </div>
                            <div class="field"><label>Customer price</label><input value="PKR ${money(salePrice)}" readonly></div>
                            <div class="builder-two-col">
                                <div class="field"><label>Gender</label><select data-field="gender" data-index="${index}"><option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option><option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option></select></div>
                                <div class="field"><label>Pieces</label><input type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${escapeHtml(product.piece_count)}"></div>
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

                productList.querySelectorAll('[data-field]').forEach(input => {
                    input.addEventListener('input', updateField);
                    input.addEventListener('change', updateField);
                });
            }

            function updateField(event) {
                const input = event.currentTarget;
                const product = products[Number(input.dataset.index)];
                product[input.dataset.field] = input.type === 'checkbox' ? input.checked : input.value;
                if (['name', 'retail_price', 'discount_percent'].includes(input.dataset.field)) {
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
                    pageContainer.innerHTML = `<div class="error-box"><strong>PDF could not be rendered.</strong><br>${escapeHtml(error.message)}<br><br>Please verify the uploaded PDF is valid and readable.</div>`;
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
                    name: `${knownCatalog.collection} — Design ${String(index + 1).padStart(2, '0')}`,
                    description: knownCatalog.description,
                    retail_price: knownCatalog.retail_price,
                    discount_percent: 3,
                    gender: knownCatalog.gender,
                    piece_count: knownCatalog.piece_count,
                    fabric: knownCatalog.fabric,
                    season: knownCatalog.season,
                    pages,
                }));
            } else {
                addProduct();
            }

            renderPdf();
        })();
    </script>
@endsection
