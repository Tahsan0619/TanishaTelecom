App.ready(async () => {
    const { api, esc, money, productCard, skeletonCards, emptyBox } = App;

    const grid = document.getElementById('shopGrid');
    const pager = document.getElementById('pagination');
    const countEl = document.getElementById('resultCount');
    const sortSel = document.getElementById('sortSelect');
    const filtersEl = document.getElementById('activeFilters');
    const crumb = document.getElementById('breadcrumb');

    const params = new URLSearchParams(location.search);
    const state = {
        category: params.get('category') || '',
        search: params.get('search') || '',
        sort: params.get('sort') || '',
        min_price: params.get('min_price') || '',
        max_price: params.get('max_price') || '',
        page: Number(params.get('page')) || 1,
    };

    await App.renderSidebar(document.getElementById('sidebar'), {
        search: false,
        priceFilter: true,
        activeCategory: state.category,
    });

    sortSel.value = state.sort;
    sortSel.addEventListener('change', () => {
        state.sort = sortSel.value;
        state.page = 1;
        load();
    });

    let priceRange = null;

    function syncUrl() {
        const p = new URLSearchParams();
        Object.entries(state).forEach(([k, v]) => {
            if (v && !(k === 'page' && v === 1)) p.set(k, v);
        });
        history.replaceState(null, '', 'shop.html' + (p.toString() ? '?' + p : ''));
    }

    function renderFilters() {
        const chips = [];
        const cat = App.categories.find((c) => c.slug === state.category);
        if (cat) chips.push(['category', `Category: ${cat.name}`]);
        if (state.search) chips.push(['search', `Search: "${state.search}"`]);
        if (state.min_price || state.max_price) chips.push(['price', `Price: ${money(state.min_price || 0)} - ${money(state.max_price || priceRange?.max || 0)}`]);
        filtersEl.innerHTML = chips.map(([key, label]) => `<a href="#" data-clear="${key}">${esc(label)}<i class="fa-solid fa-xmark"></i></a>`).join('')
            + (chips.length > 1 ? '<a href="shop.html" style="background:#eee;color:#555">Clear all</a>' : '');

        crumb.innerHTML = '<a href="index.html">Home</a><span>/</span>' + (cat
            ? `<a href="shop.html">Shop</a><span>/</span>${esc(cat.name)}`
            : 'Shop');
        document.title = (cat ? cat.name : state.search ? `Search: ${state.search}` : 'Shop') + ' | ' + App.settings.site_name;
    }

    filtersEl.addEventListener('click', (e) => {
        const a = e.target.closest('[data-clear]');
        if (!a) return;
        e.preventDefault();
        const key = a.dataset.clear;
        if (key === 'price') { state.min_price = ''; state.max_price = ''; }
        else state[key] = '';
        state.page = 1;
        if (key === 'category') {
            document.querySelectorAll('.cat-list a.active').forEach((x) => x.classList.remove('active'));
        }
        if (key === 'search') {
            const input = document.querySelector('#headerSearch input');
            if (input) input.value = '';
        }
        load();
    });

    function renderPriceFilter(range) {
        const box = document.getElementById('priceFilterWidget');
        if (!box || !range || range.max <= 0) return;
        const min = Math.floor(range.min);
        const max = Math.ceil(range.max);
        const curMin = state.min_price ? Number(state.min_price) : min;
        const curMax = state.max_price ? Number(state.max_price) : max;
        box.innerHTML = `
            <div class="widget-title">Filter by price</div>
            <div class="range-wrap">
                <div class="range-track"></div><div class="range-fill"></div>
                <input type="range" id="rangeMin" min="${min}" max="${max}" step="10" value="${curMin}" aria-label="Minimum price">
                <input type="range" id="rangeMax" min="${min}" max="${max}" step="10" value="${curMax}" aria-label="Maximum price">
            </div>
            <div class="pf-row">
                <button class="btn btn-sm" id="priceApply">Filter</button>
                <span id="priceLabel"></span>
            </div>`;
        const rMin = box.querySelector('#rangeMin');
        const rMax = box.querySelector('#rangeMax');
        const fill = box.querySelector('.range-fill');
        const label = box.querySelector('#priceLabel');
        const update = () => {
            if (Number(rMin.value) > Number(rMax.value) - 10) {
                if (document.activeElement === rMin) rMin.value = Number(rMax.value) - 10;
                else rMax.value = Number(rMin.value) + 10;
            }
            const a = ((rMin.value - min) / (max - min || 1)) * 100;
            const b = ((rMax.value - min) / (max - min || 1)) * 100;
            fill.style.left = a + '%';
            fill.style.width = (b - a) + '%';
            label.textContent = `Price: ${money(rMin.value).replace('.00', '')} - ${money(rMax.value).replace('.00', '')}`;
        };
        rMin.addEventListener('input', update);
        rMax.addEventListener('input', update);
        update();
        box.querySelector('#priceApply').addEventListener('click', () => {
            state.min_price = Number(rMin.value) > min ? rMin.value : '';
            state.max_price = Number(rMax.value) < max ? rMax.value : '';
            state.page = 1;
            load();
        });
    }

    function renderPager(meta) {
        if (meta.last_page <= 1) { pager.innerHTML = ''; return; }
        const pages = [];
        for (let i = 1; i <= meta.last_page; i++) {
            if (i === 1 || i === meta.last_page || Math.abs(i - meta.current_page) <= 1) pages.push(i);
            else if (pages[pages.length - 1] !== '…') pages.push('…');
        }
        pager.innerHTML = `<button data-page="${meta.current_page - 1}" ${meta.current_page === 1 ? 'disabled' : ''} aria-label="Previous">←</button>`
            + pages.map((p) => p === '…' ? '<button disabled>…</button>' : `<button data-page="${p}" class="${p === meta.current_page ? 'active' : ''}">${p}</button>`).join('')
            + `<button data-page="${meta.current_page + 1}" ${meta.current_page === meta.last_page ? 'disabled' : ''} aria-label="Next">→</button>`;
    }

    pager.addEventListener('click', (e) => {
        const b = e.target.closest('[data-page]');
        if (!b || b.disabled) return;
        state.page = Number(b.dataset.page);
        load();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    let requestId = 0;
    async function load() {
        const id = ++requestId;
        syncUrl();
        renderFilters();
        grid.innerHTML = skeletonCards(8);
        const q = new URLSearchParams({ per_page: 16, page: state.page });
        ['category', 'search', 'sort', 'min_price', 'max_price'].forEach((k) => { if (state[k]) q.set(k, state[k]); });
        if (state.sort === 'latest') q.delete('sort');

        try {
            const res = await api('/products?' + q, { auth: false });
            if (id !== requestId) return;
            const { data, meta } = res;
            if (!priceRange) {
                priceRange = meta.price_range;
                renderPriceFilter(priceRange);
                renderFilters();
            }
            countEl.textContent = meta.total
                ? `Showing ${meta.from}-${meta.to} of ${meta.total} results`
                : 'No results';
            grid.innerHTML = data.length
                ? data.map(productCard).join('')
                : emptyBox('fa-magnifying-glass', 'No products found', 'Try a different search or filter.', '<a href="shop.html" class="btn" style="margin-top:10px">View all products</a>');
            renderPager(meta);
        } catch (err) {
            grid.innerHTML = emptyBox('fa-triangle-exclamation', 'Could not load products', err.message);
            pager.innerHTML = '';
        }
    }

    load();
});
