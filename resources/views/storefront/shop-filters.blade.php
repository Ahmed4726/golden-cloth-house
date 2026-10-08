<form method="get" action="{{ route('shop') }}">
    <div class="mb-4 pb-3 border-bottom">
        <label class="form-label fw-bold text-uppercase small" for="shopSearch">Search</label>
        <input class="form-control" id="shopSearch" type="search" name="q" value="{{ $filter['q'] }}" placeholder="Search products...">
    </div>

    <div class="mb-4 pb-3 border-bottom">
        <div class="fw-bold text-uppercase small mb-3">Collection</div>
        <div class="vstack gap-2">
            <label class="form-check">
                <input class="form-check-input" type="radio" name="category" value="" @checked($filter['category'] === '')>
                <span class="form-check-label">All products</span>
            </label>
            @foreach ($categories as $category)
                <label class="form-check">
                    <input class="form-check-input" type="radio" name="category" value="{{ $category->slug }}" @checked($filter['category'] === $category->slug || $filter['category'] == $category->id)>
                    <span class="form-check-label">{{ $category->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="mb-4 pb-3 border-bottom">
        <label class="form-label fw-bold text-uppercase small" for="shopBrand">Brand</label>
        <select class="form-select" id="shopBrand" name="brand">
            <option value="">All brands</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected($filter['brand'] == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-4 pb-3 border-bottom">
        <div class="fw-bold text-uppercase small mb-3">Price</div>
        <div class="row g-2">
            <div class="col-6"><input class="form-control" type="number" name="min_price" value="{{ $filter['min_price'] }}" min="0" placeholder="Min"></div>
            <div class="col-6"><input class="form-control" type="number" name="max_price" value="{{ $filter['max_price'] }}" min="0" placeholder="Max"></div>
        </div>
    </div>

    <div class="mb-4 pb-3 border-bottom">
        <div class="fw-bold text-uppercase small mb-3">Availability</div>
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="inStock" name="in_stock" value="1" @checked($filter['in_stock'])>
            <label class="form-check-label" for="inStock">In stock only</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="saleItems" name="sale" value="1" @checked($filter['sale'])>
            <label class="form-check-label" for="saleItems">Sale items</label>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold text-uppercase small" for="shopSort">Sort</label>
        <select class="form-select" id="shopSort" name="sort">
            <option value="featured" @selected($filter['sort'] === 'featured')>Featured</option>
            <option value="newest" @selected($filter['sort'] === 'newest')>Newest</option>
            <option value="price_low" @selected($filter['sort'] === 'price_low')>Price: Low to High</option>
            <option value="price_high" @selected($filter['sort'] === 'price_high')>Price: High to Low</option>
            <option value="name_az" @selected($filter['sort'] === 'name_az')>Name: A to Z</option>
        </select>
    </div>

    <button class="btn btn-dark w-100 text-uppercase fw-bold" type="submit">Apply filters</button>
    <a class="btn btn-outline-dark w-100 mt-2" href="{{ route('shop') }}">Clear all</a>
</form>
