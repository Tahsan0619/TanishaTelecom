App.ready(async () => {
    const { api, esc, productCard, skeletonCards, emptyBox, imgSrc, priceHTML, remember } = App;
    const s = App.settings;

    // Promo banner: uploaded image or the built-in text banner
    const banner = document.getElementById('promoBanner');
    banner.innerHTML = s.banner_image_url
        ? `<a href="shop.html"><img src="${esc(s.banner_image_url)}" alt="${esc(s.site_name)}"></a>`
        : `<div class="promo-text-banner">
                <img class="promo-logo" src="${esc(s.logo_url || 'assets/images/icon.png')}" alt="">
                <div>
                    ${s.banner_subtitle ? `<div class="promo-sub">${esc(s.banner_subtitle)}</div>` : ''}
                    <div class="promo-title">${esc(s.banner_title || s.site_name)}</div>
                    ${s.banner_badge ? `<div class="promo-badge">${esc(s.banner_badge)}</div>` : ''}
                </div>
           </div>`;

    const grid = document.getElementById('allProducts');
    grid.innerHTML = skeletonCards(10);

    const [slidersRes, productsRes, popularRes] = await Promise.allSettled([
        api('/sliders', { auth: false }),
        api('/products?per_page=20', { auth: false }),
        api('/products?per_page=4&on_sale=1&sort=popular', { auth: false }),
    ]);

    // Slider
    const sliderEl = document.getElementById('mainSlider');
    const sideEl = document.getElementById('heroSide');
    if (slidersRes.status === 'fulfilled') {
        const { main, side } = slidersRes.value.data;
        buildSlider(sliderEl, main);
        sideEl.innerHTML = side.slice(0, 3).map((sl) => `
            <a href="${esc(sl.link || 'shop.html')}" title="${esc(sl.title || '')}">
                <span class="sp-text">
                    ${sl.title ? `<span class="sp-title">${esc(sl.title)}</span>` : ''}
                    <span class="sp-link">Shop now <i class="fa-solid fa-arrow-right"></i></span>
                </span>
                <img src="${esc(sl.image_url)}" alt="${esc(sl.title || '')}">
            </a>`).join('');
        if (!side.length) sideEl.remove();
    } else {
        sliderEl.innerHTML = '';
    }

    // Products
    if (productsRes.status === 'fulfilled') {
        const products = productsRes.value.data;
        grid.innerHTML = products.length
            ? products.map(productCard).join('')
            : emptyBox('fa-box-open', 'No products yet', 'Please check back soon.');
    } else {
        grid.innerHTML = emptyBox('fa-triangle-exclamation', 'Could not load products', productsRes.reason.message);
    }

    // Category tiles
    const icons = { smartphones: 'fa-mobile-screen-button', 'laptops-tablets': 'fa-laptop', smartwatches: 'fa-clock', audio: 'fa-headphones', 'power-charging': 'fa-battery-full', accessories: 'fa-plug' };
    document.getElementById('categoryTiles').innerHTML = App.categories.map((c) => `
        <a class="category-tile" href="shop.html?category=${encodeURIComponent(c.slug)}">
            <div class="ic">${c.image_url ? `<img src="${esc(c.image_url)}" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover">` : `<i class="fa-solid ${icons[c.slug] || 'fa-tag'}"></i>`}</div>
            <div class="nm">${esc(c.name)}</div>
            <div class="ct">${c.products_count} products</div>
        </a>`).join('');

    // Mini product list
    const mini = document.getElementById('miniProducts');
    if (popularRes.status === 'fulfilled') {
        remember(popularRes.value.data);
        mini.innerHTML = popularRes.value.data.map((p) => `
            <a class="mini-product" href="product.html?slug=${encodeURIComponent(p.slug)}">
                <img src="${esc(imgSrc(p.image_url))}" alt="" loading="lazy">
                <div><div class="name">${esc(p.name)}</div><div class="price">${priceHTML(p)}</div></div>
            </a>`).join('');
    }
});

function buildSlider(el, slides) {
    const { esc } = App;
    if (!slides.length) {
        el.closest('.hero').classList.add('hidden');
        return;
    }
    el.innerHTML = slides.map((s, i) => `
        <a class="slide ${i === 0 ? 'active' : ''}" href="${esc(s.link || 'shop.html')}">
            <img src="${esc(s.image_url)}" alt="${esc(s.title || '')}" ${i ? 'loading="lazy"' : ''}>
            ${s.title ? `<div class="slide-caption"><h2>${esc(s.title)}</h2>${s.subtitle ? `<p>${esc(s.subtitle)}</p>` : ''}<span class="btn">Shop Now</span></div>` : ''}
        </a>`).join('')
        + (slides.length > 1 ? `
        <button class="slider-arrow prev" aria-label="Previous"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="slider-arrow next" aria-label="Next"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="slider-dots">${slides.map((_, i) => `<button class="${i === 0 ? 'active' : ''}" data-i="${i}" aria-label="Slide ${i + 1}"></button>`).join('')}</div>` : '');

    if (slides.length < 2) return;

    const slideEls = el.querySelectorAll('.slide');
    const dots = el.querySelectorAll('.slider-dots button');
    let current = 0;
    let timer;

    const go = (i) => {
        current = (i + slideEls.length) % slideEls.length;
        slideEls.forEach((s, idx) => s.classList.toggle('active', idx === current));
        dots.forEach((d, idx) => d.classList.toggle('active', idx === current));
    };
    const start = () => { clearInterval(timer); timer = setInterval(() => go(current + 1), 5000); };

    el.querySelector('.prev').addEventListener('click', (e) => { e.preventDefault(); go(current - 1); start(); });
    el.querySelector('.next').addEventListener('click', (e) => { e.preventDefault(); go(current + 1); start(); });
    dots.forEach((d) => d.addEventListener('click', (e) => { e.preventDefault(); go(Number(d.dataset.i)); start(); }));
    el.addEventListener('mouseenter', () => clearInterval(timer));
    el.addEventListener('mouseleave', start);

    let touchX = null;
    el.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; }, { passive: true });
    el.addEventListener('touchend', (e) => {
        if (touchX === null) return;
        const dx = e.changedTouches[0].clientX - touchX;
        if (Math.abs(dx) > 40) { go(current + (dx < 0 ? 1 : -1)); start(); }
        touchX = null;
    });

    start();
}
