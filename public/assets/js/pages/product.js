App.ready(async () => {
    const { api, esc, money, qs, productCard, emptyBox, imgSrc, priceHTML, Cart, Wishlist, toast, remember } = App;
    const box = document.getElementById('productBox');
    const slug = qs('slug');

    if (!slug) {
        box.outerHTML = emptyBox('fa-box-open', 'Product not found', '', '<a href="shop.html" class="btn">Go to shop</a>');
        return;
    }

    let res;
    try {
        res = await api('/products/' + encodeURIComponent(slug), { auth: false });
    } catch (err) {
        box.outerHTML = emptyBox('fa-box-open', err.status === 404 ? 'Product not found' : 'Could not load product',
            err.status === 404 ? 'This product may have been removed.' : err.message,
            '<a href="shop.html" class="btn" style="margin-top:10px">Go to shop</a>');
        return;
    }

    const p = res.data;
    remember([p]);
    document.title = `${p.name} | ${App.settings.site_name}`;

    document.getElementById('breadcrumb').innerHTML = '<a href="index.html">Home</a><span>/</span><a href="shop.html">Shop</a>'
        + (p.category ? `<span>/</span><a href="shop.html?category=${encodeURIComponent(p.category.slug)}">${esc(p.category.name)}</a>` : '')
        + `<span>/</span>${esc(p.name)}`;

    const shareUrl = encodeURIComponent(location.href);
    const maxQty = Math.max(1, Math.min(p.stock, 99));

    box.innerHTML = `
        <div class="product-detail">
            <div class="gallery">
                ${p.on_sale ? '<div class="sale-ribbon"><span>Sale!</span></div>' : ''}
                <img src="${esc(imgSrc(p.image_url))}" alt="${esc(p.name)}">
            </div>
            <div>
                <h1>${esc(p.name)}</h1>
                <div class="price">${priceHTML(p)}${p.on_sale ? `<span class="save-tag">Save ${p.discount_percent}%</span>` : ''}</div>
                ${p.short_description ? `<p class="short">${esc(p.short_description)}</p>` : ''}
                <ul class="meta-list">
                    <li><strong>Availability:</strong> ${p.in_stock
                        ? `<span class="stock-in"><i class="fa-solid fa-circle-check"></i> In stock (${p.stock})</span>`
                        : '<span class="stock-out"><i class="fa-solid fa-circle-xmark"></i> Out of stock</span>'}</li>
                    ${p.unit ? `<li><strong>Variant:</strong> ${esc(p.unit)}</li>` : ''}
                    ${p.category ? `<li><strong>Category:</strong> <a href="shop.html?category=${encodeURIComponent(p.category.slug)}">${esc(p.category.name)}</a></li>` : ''}
                </ul>
                ${p.in_stock ? `
                <div class="buy-row">
                    <div class="qty">
                        <button type="button" data-step="-1" aria-label="Decrease">−</button>
                        <input type="number" id="qty" value="1" min="1" max="${maxQty}" aria-label="Quantity">
                        <button type="button" data-step="1" aria-label="Increase">+</button>
                    </div>
                    <button class="btn btn-light btn-lg" id="addToCart"><i class="fa-solid fa-basket-shopping"></i> Add to cart</button>
                    <button class="btn btn-lg" id="buyNow"><i class="fa-solid fa-cart-shopping"></i> Order Now</button>
                </div>` : '<div class="alert alert-info">This product is currently out of stock. Please check back later.</div>'}
                <button class="wish-btn" data-wish="${p.id}" style="font-size:13px;margin:4px 0 18px">
                    <i class="fa-regular fa-heart"></i> <span class="lbl">Add to wishlist</span>
                </button>
                ${App.settings.phone ? `<div class="alert alert-info" style="margin-bottom:16px"><i class="fa-solid fa-phone"></i> Need help choosing? Call us: <a href="tel:${esc(App.settings.phone)}"><strong>${esc(App.settings.phone)}</strong></a></div>` : ''}
                <div class="share-row">Share:
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${shareUrl}" target="_blank" rel="noopener" aria-label="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://wa.me/?text=${shareUrl}" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="https://www.messenger.com/" target="_blank" rel="noopener" aria-label="Messenger"><i class="fa-brands fa-facebook-messenger"></i></a>
                </div>
            </div>
        </div>
        ${p.description ? `
        <div class="product-tabs">
            <div class="tab-head"><span>Description</span></div>
            <div class="rich-content">${p.description}</div>
        </div>` : ''}`;

    App.syncWishButtons();

    const qtyInput = document.getElementById('qty');
    const getQty = () => Math.max(1, Math.min(Number(qtyInput.value) || 1, maxQty));
    box.querySelectorAll('[data-step]').forEach((b) => b.addEventListener('click', () => {
        qtyInput.value = Math.max(1, Math.min(getQty() + Number(b.dataset.step), maxQty));
    }));
    qtyInput?.addEventListener('change', () => { qtyInput.value = getQty(); });

    document.getElementById('addToCart')?.addEventListener('click', () => {
        if (Cart.add(p, getQty())) toast(`"${p.name}" added to cart.`, 'success', { href: 'cart.html', text: 'View cart' });
    });
    document.getElementById('buyNow')?.addEventListener('click', () => {
        if (Cart.add(p, getQty())) location.href = 'checkout.html';
    });

    if (res.related?.length) {
        document.getElementById('relatedWrap').classList.remove('hidden');
        document.getElementById('related').innerHTML = res.related.map(productCard).join('');
    }
});
