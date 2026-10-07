@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Product Builder</h1>
            <p class="muted">{{ $catalogImport->name }} · Select a design below, then click its PDF pages to use them as product images.</p>
        </div>
        <a class="btn secondary" href="{{ route('catalog-imports.index') }}">Back to Catalogs</a>
    </div>

    <div class="catalog-builder" id="catalogBuilder"
         data-pdf-url="{{ Storage::disk('public')->url($catalogImport->pdf_path) }}"
         data-finalize-url="{{ route('catalog-imports.finalize', $catalogImport) }}"
         data-catalog-name="{{ e($catalogImport->name) }}">
        <section class="builder-products card">
            <div class="builder-heading">
                <div>
                    <strong>Products / Designs</strong>
                    <div class="muted">The selected design receives the highlighted page images.</div>
                </div>
                <button class="btn secondary" type="button" id="addDesign">+ Add Design</button>
            </div>

            <div id="productList" class="product-builder-list"></div>

            <div class="builder-actions">
                <button class="btn" type="button" id="createProducts">Create Selected Products</button>
                <span id="builderStatus" class="muted"></span>
            </div>
        </section>

        <aside class="builder-pages card">
            <div class="builder-heading">
                <div>
                    <strong>PDF Pages</strong>
                    <div class="muted">Click pages to add/remove them from the active design.</div>
                </div>
                <span id="pageStatus" class="badge">Loading PDF…</span>
            </div>
            <div id="pdfPages" class="pdf-pages">
                <div class="muted">Preparing catalogue pages…</div>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <script type="module">
        import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';

        const root = document.getElementById('catalogBuilder');
        const pdfUrl = root.dataset.pdfUrl;
        const finalizeUrl = root.dataset.finalizeUrl;
        const catalogName = root.dataset.catalogName.toLowerCase();
        const pageContainer = document.getElementById('pdfPages');
        const productList = document.getElementById('productList');
        const pageStatus = document.getElementById('pageStatus');
        const builderStatus = document.getElementById('builderStatus');
        const products = [];
        const renderedPages = new Map();
        let activeProduct = 0;

        const knownCatalog = catalogName.includes('lime')
            ? {
                gender: 'women',
                retail: 6200,
                fabric: 'Peach Slub',
                season: 'Winter',
                description: 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                groups: [[2, 3, 4], [5, 6, 7], [8, 9, 10, 11], [12, 13, 14, 15], [16, 17, 18], [19, 20, 21, 22]],
            }
            : catalogName.includes('leore')
                ? {
                    gender: 'women',
                    retail: 7800,
                    fabric: 'Fancy Zari Slub Dhanak',
                    season: 'Winter',
                    description: 'Premium embroidered fancy zari slub dhanak shirt with fancy zari slub embroidered lace dupatta and comfort of same trouser.',
                    groups: [[2, 3, 4, 5, 6], [7, 8, 9, 10, 11], [12, 13, 14, 15, 16], [17, 18, 19], [20, 21, 22], [23, 24]],
                }
                : null;

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[char]));
        }

        function addProduct(seed = {}) {
            const index = products.length + 1;
            products.push({
                name: seed.name || `Unstitched Design ${String(index).padStart(2, '0')}`,
                description: seed.description || '',
                retail_price: seed.retail_price ?? '',
                discount_percent: seed.discount_percent ?? 3,
                stock_quantity: seed.stock_quantity ?? 3,
                gender: seed.gender || 'women',
                piece_count: seed.piece_count || '',
                fabric: seed.fabric || '',
                season: seed.season || '',
                design_code: seed.design_code || `DES-${String(index).padStart(2, '0')}`,
                category_id: seed.category_id || '',
                brand_id: seed.brand_id || '',
                featured: seed.featured ?? false,
                pages: new Set(seed.pages || []),
            });
            activeProduct = products.length - 1;
            renderProducts();
        }

        function salePrice(product) {
            const retail = Number(product.retail_price || 0);
            const discount = Number(product.discount_percent || 0);
            if (!retail) return '—';
            return (retail * (1 - discount / 100)).toFixed(0);
        }

        function renderProducts() {
            productList.innerHTML = products.map((product, index) => `
                <article class="design-card ${index === activeProduct ? 'active' : ''}" data-product="${index}">
                    <div class="design-card-head">
                        <button type="button" class="design-select" data-select-product="${index}">
                            <span class="design-number">${String(index + 1).padStart(2, '0')}</span>
                            <span>${escapeHtml(product.name)}</span>
                        </button>
                        ${products.length > 1 ? `<button type="button" class="remove-design" data-remove-product="${index}" aria-label="Remove design">×</button>` : ''}
                    </div>
                    <div class="design-form">
                        <div class="field"><label>Product title</label><input data-field="name" data-index="${index}" value="${escapeHtml(product.name)}"></div>
                        <div class="builder-two-col">
                            <div class="field"><label>Retail price (PKR)</label><input type="number" min="0" step="1" data-field="retail_price" data-index="${index}" value="${product.retail_price}"></div>
                            <div class="field"><label>Discount %</label><input type="number" min="0" max="100" step="0.1" data-field="discount_percent" data-index="${index}" value="${product.discount_percent}"></div>
                        </div>
                        <div class="sale-preview">Customer price: <strong>PKR ${salePrice(product)}</strong> <span>after ${product.discount_percent}% discount</span></div>
                        <div class="builder-two-col">
                            <div class="field"><label>Gender</label><select data-field="gender" data-index="${index}"><option value="women" ${product.gender === 'women' ? 'selected' : ''}>Women</option><option value="men" ${product.gender === 'men' ? 'selected' : ''}>Men</option></select></div>
                            <div class="field"><label>Pieces</label><input type="number" min="1" max="10" data-field="piece_count" data-index="${index}" value="${product.piece_count}" placeholder="e.g. 3"></div>
                        </div>
                        <div class="builder-two-col">
                            <div class="field"><label>Fabric</label><input data-field="fabric" data-index="${index}" value="${escapeHtml(product.fabric)}" placeholder="Khaddar, Lawn, Slub…"></div>
                            <div class="field"><label>Season</label><input data-field="season" data-index="${index}" value="${escapeHtml(product.season)}" placeholder="Winter / Summer"></div>
                        </div>
                        <div class="builder-two-col">
                            <div class="field"><label>Category</label><select data-field="category_id" data-index="${index}"><option value="">No category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" ${String(product.category_id) === '{{ $category->id }}' ? 'selected' : ''}>{{ e($category->name) }}</option>@endforeach</select></div>
                            <div class="field"><label>Brand</label><select data-field="brand_id" data-index="${index}"><option value="">No brand</option>@foreach ($brands as $brand)<option value="{{ $brand->id }}" ${String(product.brand_id) === '{{ $brand->id }}' ? 'selected' : ''}>{{ e($brand->name) }}</option>@endforeach</select></div>
                        </div>
                        <div class="field"><label>Design code</label><input data-field="design_code" data-index="${index}" value="${escapeHtml(product.design_code)}"></div>
                        <div class="field"><label>Description</label><textarea data-field="description" data-index="${index}">${escapeHtml(product.description)}</textarea></div>
                        <div class="builder-two-col">
                            <div class="field"><label>Opening stock</label><input type="number" min="0" data-field="stock_quantity" data-index="${index}" value="${product.stock_quantity}"></div>
                            <label class="check"><input type="checkbox" data-field="featured" data-index="${index}" ${product.featured ? 'checked' : ''}> Feature on homepage</label>
                        </div>
                        <div class="selected-pages"><strong>${product.pages.size}</strong> page(s) selected for this product.</div>
                    </div>
                </article>
            `).join('');

            productList.querySelectorAll('[data-select-product]').forEach(button => button.addEventListener('click', () => {
                activeProduct = Number(button.dataset.selectProduct);
                renderProducts();
            }));

            productList.querySelectorAll('[data-remove-product]').forEach(button => button.addEventListener('click', () => {
                products.splice(Number(button.dataset.removeProduct), 1);
                activeProduct = Math.max(0, Math.min(activeProduct, products.length - 1));
                renderProducts();
                renderPageSelection();
            }));

            productList.querySelectorAll('[data-field]').forEach(input => input.addEventListener('input', updateField));
            productList.querySelectorAll('[data-field]').forEach(input => input.addEventListener('change', updateField));
        }

        function updateField(event) {
            const input = event.currentTarget;
            const product = products[Number(input.dataset.index)];
            const key = input.dataset.field;
            product[key] = input.type === 'checkbox' ? input.checked : input.value;
            if (key === 'retail_price' || key === 'discount_percent' || key === 'name') renderProducts();
        }

        function renderPageSelection() {
            pageContainer.querySelectorAll('[data-page]').forEach(card => {
                const page = Number(card.dataset.page);
                const selected = products[activeProduct]?.pages.has(page);
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

        function addPageToActive(pageNumber) {
            if (!products[activeProduct]) return;
            const pages = products[activeProduct].pages;
            pages.has(pageNumber) ? pages.delete(pageNumber) : pages.add(pageNumber);
            renderProducts();
            renderPageSelection();
        }

        async function renderPdf() {
            pageStatus.textContent = 'Loading PDF…';
            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
            pageStatus.textContent = `${pdf.numPages} pages`;
            pageContainer.innerHTML = '';

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                const page = await pdf.getPage(pageNumber);
                const baseViewport = page.getViewport({ scale: 1 });
                const scale = Math.min(0.72, 900 / baseViewport.width);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const context = canvas.getContext('2d', { alpha: false });
                await page.render({ canvasContext: context, viewport }).promise;
                renderedPages.set(pageNumber, { canvas });

                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'pdf-page';
                card.dataset.page = pageNumber;
                card.innerHTML = `<span class="page-number">Page ${pageNumber}</span>`;
                card.appendChild(canvas);
                card.addEventListener('click', () => addPageToActive(pageNumber));
                pageContainer.appendChild(card);
            }

            renderPageSelection();
        }

        function defaultProducts() {
            if (!knownCatalog) {
                addProduct();
                return;
            }

            knownCatalog.groups.forEach((pages, index) => addProduct({
                name: `${catalogName.includes('lime') ? 'Lime Vol-552' : 'Leore Vol-01'} — Design ${String(index + 1).padStart(2, '0')}`,
                description: knownCatalog.description,
                retail_price: knownCatalog.retail,
                discount_percent: 3,
                gender: knownCatalog.gender,
                fabric: knownCatalog.fabric,
                season: knownCatalog.season,
                design_code: `DES-${String(index + 1).padStart(2, '0')}`,
                pages,
            }));
        }

        document.getElementById('addDesign').addEventListener('click', () => addProduct());

        document.getElementById('createProducts').addEventListener('click', async () => {
            builderStatus.textContent = 'Preparing selected images…';
            const payload = [];

            for (const product of products) {
                if (!product.pages.size) {
                    alert(`Select at least one PDF page for “${product.name}”.`);
                    builderStatus.textContent = '';
                    return;
                }

                const images = [...product.pages].sort((a, b) => a - b).map(page => renderedPages.get(page).canvas.toDataURL('image/jpeg', 0.88));
                payload.push({ ...product, pages: undefined, images });
            }

            const response = await fetch(finalizeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                },
                body: JSON.stringify({ products: payload }),
            });

            const result = await response.json().catch(() => ({}));
            if (!response.ok) {
                builderStatus.textContent = result.message || 'Could not create products.';
                return;
            }

            builderStatus.textContent = result.message;
            window.location.href = result.redirect;
        });

        defaultProducts();
        renderPdf().catch(error => {
            console.error(error);
            pageStatus.textContent = 'PDF preview failed';
            pageContainer.innerHTML = '<div class="error">The PDF could not be rendered in this browser. Make sure the PDF URL is publicly readable from this admin session and try again.</div>';
        });
    </script>

    <style>
        .catalog-builder { display:grid; grid-template-columns:minmax(360px, 0.9fr) minmax(500px, 1.4fr); gap:18px; align-items:start; }
        .builder-products,.builder-pages { min-width:0; }
        .builder-pages { position:sticky; top:18px; max-height:calc(100vh - 36px); overflow:auto; }
        .builder-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:14px; margin-bottom:16px; }
        .product-builder-list { display:grid; gap:12px; }
        .design-card { border:1px solid var(--line); border-radius:14px; overflow:hidden; background:#fff; }
        .design-card.active { border-color:#111827; box-shadow:0 5px 18px #11182712; }
        .design-card-head { display:flex; align-items:center; justify-content:space-between; background:#f8fafc; }
        .design-select { border:0; background:transparent; cursor:pointer; padding:13px; display:flex; align-items:center; gap:10px; font-weight:800; text-align:left; flex:1; }
        .design-number { width:34px; height:34px; display:grid; place-items:center; border-radius:50%; background:#111827; color:#fff; font-size:12px; }
        .remove-design { border:0; background:transparent; color:#b91c1c; font-size:22px; cursor:pointer; padding:10px 14px; }
        .design-form { padding:14px; }
        .builder-two-col { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .sale-preview { padding:10px 12px; border-radius:10px; background:#fff7ed; color:#9a3412; margin-bottom:14px; }
        .sale-preview span { font-size:12px; margin-left:6px; }
        .selected-pages { padding:9px 11px; border-radius:9px; background:#f1f5f9; font-size:13px; color:#475569; }
        .builder-actions { display:flex; align-items:center; gap:12px; margin-top:16px; }
        .pdf-pages { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
        .pdf-page { position:relative; padding:0; border:1px solid var(--line); border-radius:10px; background:#fff; overflow:hidden; cursor:pointer; text-align:left; }
        .pdf-page:hover { border-color:#111827; }
        .pdf-page.selected { border:3px solid #111827; }
        .pdf-page canvas { display:block; width:100%; height:auto; }
        .page-number { display:block; position:absolute; top:8px; left:8px; z-index:2; padding:4px 7px; border-radius:999px; background:#fff; color:#111827; font-size:11px; font-weight:800; box-shadow:0 2px 8px #0002; }
        .page-selected { position:absolute; right:8px; top:8px; z-index:2; padding:4px 7px; border-radius:999px; background:#111827; color:#fff; font-size:11px; font-weight:800; }
        @media(max-width:1000px) { .catalog-builder { grid-template-columns:1fr; } .builder-pages { position:static; max-height:none; } }
        @media(max-width:600px) { .pdf-pages,.builder-two-col { grid-template-columns:1fr; } .builder-heading { flex-direction:column; } .builder-actions { flex-direction:column; align-items:stretch; } }
    </style>
@endpush
