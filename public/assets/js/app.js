/* =========================================================
   Tanisha Telecom: shared storefront logic
   (API client, cart, wishlist, auth, header/footer, helpers)
   ========================================================= */
(function () {
    'use strict';

    const CFG = window.APP_CONFIG || {};
    const API_BASE = (CFG.API_BASE || 'api').replace(/\/$/, '');
    const KEYS = {
        cart: 'tt_cart',
        wishlist: 'tt_wishlist',
        token: 'tt_token',
        user: 'tt_user',
        settings: 'tt_settings',
        categories: 'tt_categories',
    };

    /* ---------------- Helpers ---------------- */
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    const money = (amount) => (CFG.CURRENCY || '৳') + Number(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    const qs = (name) => new URLSearchParams(location.search).get(name);

    const readJSON = (key, fallback) => {
        try {
            const v = JSON.parse(localStorage.getItem(key));
            return v ?? fallback;
        } catch (e) {
            return fallback;
        }
    };
    const writeJSON = (key, value) => localStorage.setItem(key, JSON.stringify(value));

    const debounce = (fn, wait = 300) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), wait);
        };
    };

    const formatDate = (iso) => {
        if (!iso) return '';
        return new Date(iso).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    const PLACEHOLDER = 'assets/images/placeholder.jpg';
    const imgSrc = (url) => url || PLACEHOLDER;

    /** wa.me needs the international format, so local 01XXXXXXXXX numbers get the 88 country code. */
    const whatsappUrl = (number) => {
        const digits = String(number || '').replace(/\D/g, '');
        return `https://wa.me/${/^01\d{9}$/.test(digits) ? '88' + digits : digits}`;
    };

    /* ---------------- API client ---------------- */
    async function api(path, { method = 'GET', body, auth = true } = {}) {
        const headers = { Accept: 'application/json' };
        if (body !== undefined) headers['Content-Type'] = 'application/json';
        const token = localStorage.getItem(KEYS.token);
        if (auth && token) headers.Authorization = 'Bearer ' + token;

        let res;
        try {
            res = await fetch(API_BASE + path, {
                method,
                headers,
                body: body !== undefined ? JSON.stringify(body) : undefined,
            });
        } catch (e) {
            throw { status: 0, message: 'Network error. Please check your internet connection.', errors: {} };
        }

        let data = null;
        try { data = await res.json(); } catch (e) { /* empty body */ }

        if (!res.ok) {
            if (res.status === 401 && token) Auth.clear();
            let message = data?.message || 'Something went wrong. Please try again.';
            if (res.status === 429) message = 'Too many attempts. Please wait a minute and try again.';
            throw { status: res.status, message, errors: data?.errors || {} };
        }
        return data;
    }

    /* ---------------- Toast ---------------- */
    function toast(message, type = 'success', link = null) {
        let wrap = $('.toast-wrap');
        if (!wrap) {
            wrap = document.createElement('div');
            wrap.className = 'toast-wrap';
            document.body.appendChild(wrap);
        }
        const el = document.createElement('div');
        el.className = 'toast' + (type === 'error' ? ' error' : '');
        el.innerHTML = `<i class="fa-solid ${type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check'}"></i>
            <span>${esc(message)}</span>${link ? `<a href="${esc(link.href)}">${esc(link.text)}</a>` : ''}`;
        wrap.appendChild(el);
        setTimeout(() => {
            el.style.transition = 'opacity .3s, transform .3s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(30px)';
            setTimeout(() => el.remove(), 300);
        }, 3200);
    }

    /* ---------------- Product registry ---------------- */
    const productCache = new Map();
    const remember = (products) => (products || []).forEach((p) => productCache.set(String(p.id), p));

    /* ---------------- Cart ---------------- */
    const Cart = {
        items() { return readJSON(KEYS.cart, []); },
        save(items) {
            writeJSON(KEYS.cart, items);
            updateBadges();
            document.dispatchEvent(new CustomEvent('cart:changed'));
        },
        count() { return this.items().reduce((s, i) => s + i.qty, 0); },
        subtotal() { return this.items().reduce((s, i) => s + i.price * i.qty, 0); },
        freeShipping() { const items = this.items(); return items.length > 0 && items.every((i) => i.free_shipping); },
        add(product, qty = 1) {
            if (!product) return false;
            if (!product.in_stock) {
                toast('Sorry, this product is out of stock.', 'error');
                return false;
            }
            const items = this.items();
            const existing = items.find((i) => i.id === product.id);
            const max = product.stock || 99;
            if (existing) {
                existing.qty = Math.min(existing.qty + qty, max);
                existing.price = product.final_price;
                existing.stock = product.stock;
                existing.free_shipping = !!product.free_shipping;
            } else {
                items.push({
                    id: product.id,
                    slug: product.slug,
                    name: product.name,
                    price: product.final_price,
                    regular_price: product.price,
                    image: product.image_url,
                    unit: product.unit,
                    stock: product.stock,
                    qty: Math.min(qty, max),
                });
            }
            this.save(items);
            return true;
        },
        setQty(id, qty) {
            const items = this.items();
            const item = items.find((i) => i.id === id);
            if (!item) return;
            item.qty = Math.max(1, Math.min(qty, item.stock || 99));
            this.save(items);
        },
        remove(id) { this.save(this.items().filter((i) => i.id !== id)); },
        clear() { this.save([]); },
        /** Re-sync prices/stock with the server; drops products that no longer exist. */
        async refresh() {
            const items = this.items();
            if (!items.length) return { items, changed: false };
            const res = await api('/products?per_page=100&ids=' + items.map((i) => i.id).join(','));
            remember(res.data);
            const byId = new Map(res.data.map((p) => [p.id, p]));
            let changed = false;
            const fresh = [];
            items.forEach((i) => {
                const p = byId.get(i.id);
                if (!p || !p.in_stock) { changed = true; return; }
                const qty = Math.min(i.qty, p.stock);
                if (p.final_price !== i.price || qty !== i.qty) changed = true;
                fresh.push({ ...i, name: p.name, price: p.final_price, regular_price: p.price, image: p.image_url, stock: p.stock, free_shipping: !!p.free_shipping, qty });
            });
            this.save(fresh);
            return { items: fresh, changed };
        },
    };

    /* ---------------- Wishlist ---------------- */
    const Wishlist = {
        items() { return readJSON(KEYS.wishlist, []); },
        has(id) { return this.items().some((i) => i.id === Number(id)); },
        toggle(product) {
            let items = this.items();
            if (this.has(product.id)) {
                items = items.filter((i) => i.id !== product.id);
                writeJSON(KEYS.wishlist, items);
                toast('Removed from wishlist.');
            } else {
                items.push({
                    id: product.id, slug: product.slug, name: product.name, price: product.price,
                    final_price: product.final_price, on_sale: product.on_sale, image_url: product.image_url,
                    in_stock: product.in_stock, stock: product.stock, unit: product.unit,
                });
                writeJSON(KEYS.wishlist, items);
                toast('Added to wishlist.', 'success', { href: 'wishlist.html', text: 'View' });
            }
            updateBadges();
            syncWishButtons();
            document.dispatchEvent(new CustomEvent('wishlist:changed'));
        },
        remove(id) {
            writeJSON(KEYS.wishlist, this.items().filter((i) => i.id !== id));
            updateBadges();
            document.dispatchEvent(new CustomEvent('wishlist:changed'));
        },
    };

    /* ---------------- Auth ---------------- */
    const Auth = {
        token() { return localStorage.getItem(KEYS.token); },
        user() { return readJSON(KEYS.user, null); },
        check() { return !!this.token(); },
        set(token, user) {
            localStorage.setItem(KEYS.token, token);
            writeJSON(KEYS.user, user);
            renderTopbar();
        },
        setUser(user) { writeJSON(KEYS.user, user); renderTopbar(); },
        clear() {
            localStorage.removeItem(KEYS.token);
            localStorage.removeItem(KEYS.user);
            renderTopbar();
        },
        async logout() {
            try { await api('/auth/logout', { method: 'POST' }); } catch (e) { /* token may already be invalid */ }
            this.clear();
        },
    };

    /* ---------------- Settings & categories ---------------- */
    let settings = readJSON(KEYS.settings, null) || {
        site_name: 'Tanisha Telecom',
        site_tagline: 'GADGETS & ACCESSORIES',
        footer_text: 'Your trusted destination for original gadgets.',
        phone: '', email: '', address: '', facebook: '',
        shipping_inside_dhaka: 60, shipping_outside_dhaka: 120,
    };
    let categories = readJSON(KEYS.categories, []);

    async function loadSettings() {
        try {
            const res = await api('/settings', { auth: false });
            settings = res.data;
            writeJSON(KEYS.settings, settings);
        } catch (e) { /* use cached values */ }
        return settings;
    }

    async function loadCategories() {
        try {
            const res = await api('/categories', { auth: false });
            categories = res.data;
            writeJSON(KEYS.categories, categories);
        } catch (e) { /* use cached values */ }
        return categories;
    }

    const DEFAULT_LOGO = 'assets/images/logo.png';
    const logoUrl = () => settings.logo_url || DEFAULT_LOGO;

    // The default logo already contains the shop name; a custom (square) logo gets the name beside it.
    const brandHTML = () => (settings.logo_url
        ? `<img src="${esc(settings.logo_url)}" alt="${esc(settings.site_name)}" class="brand-icon">
           <div>
               <div class="brand-name">${esc(settings.site_name)}</div>
               <div class="brand-tagline">${esc(settings.site_tagline || '')}</div>
           </div>`
        : `<img src="${DEFAULT_LOGO}" alt="${esc(settings.site_name)}" class="brand-logo">`);

    /* ---------------- Layout rendering ---------------- */
    const NAV = [
        { key: 'home', label: 'Home', href: 'index.html' },
        { key: 'shop', label: 'Shop', href: 'shop.html' },
        { key: 'categories', label: 'Product Categories', dropdown: true },
        { key: 'contact', label: 'Contact Us', href: 'contact.html' },
        { key: 'about', label: 'About Us', href: 'about-us.html' },
        { key: 'account', label: 'My Account', href: 'account.html' },
        { key: 'refund', label: 'Refund and Returns Policy', href: 'refund-returns.html' },
        { key: 'terms', label: 'Terms & Conditions', href: 'terms-conditions.html' },
    ];

    function headerHTML() {
        const page = document.body.dataset.page;
        const navItems = NAV.map((n) => {
            if (n.dropdown) {
                return `<li class="has-dropdown">
                    <a href="shop.html" class="${page === 'shop' && qs('category') ? 'active' : ''}" data-dropdown-toggle>${n.label}<i class="fa-solid fa-chevron-down caret"></i></a>
                    <ul class="dropdown" id="navCategories">${categoriesDropdownHTML()}</ul>
                </li>`;
            }
            const active = page === n.key && !(n.key === 'shop' && qs('category'));
            return `<li><a href="${n.href}" class="${active ? 'active' : ''}">${n.label}</a></li>`;
        }).join('');

        return `
        <div class="topbar"><div class="container" id="topbarLinks"></div></div>
        <header class="site-header">
            <div class="header-main"><div class="container">
                <button class="menu-toggle" id="menuToggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
                <a href="index.html" class="brand" id="brand">${brandHTML()}</a>
                <form class="header-search" id="headerSearch" action="shop.html" autocomplete="off">
                    <input type="search" name="search" placeholder="Search products…" value="${esc(qs('search') || '')}" aria-label="Search products">
                    <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <div class="search-suggest hidden" id="searchSuggest"></div>
                </form>
                <div class="header-icons">
                    <a href="wishlist.html" class="icon-link" title="Wishlist"><i class="fa-regular fa-heart"></i><span class="count" data-wish-count>0</span></a>
                    <a href="cart.html" class="cart-link" title="Cart">
                        <span class="icon-link"><i class="fa-solid fa-basket-shopping"></i><span class="count" data-cart-count>0</span></span>
                        <span class="cart-total">Total<strong data-cart-total>${money(0)}</strong></span>
                    </a>
                </div>
            </div></div>
        </header>
        <nav class="main-nav" id="mainNav">
            <div class="container">
                <div class="nav-close" style="display:none;justify-content:space-between;align-items:center;padding:14px 20px;border-bottom:1px solid #eee">
                    <strong style="color:var(--primary)">Menu</strong>
                    <button id="menuClose" style="background:none;border:0;font-size:20px;cursor:pointer" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <ul>${navItems}</ul>
            </div>
        </nav>
        <div class="mobile-backdrop" id="mobileBackdrop"></div>`;
    }

    function categoriesDropdownHTML() {
        if (!categories.length) return '<li><a href="shop.html">All products</a></li>';
        return categories.map((c) => `<li><a href="shop.html?category=${encodeURIComponent(c.slug)}">${esc(c.name)} <span>(${c.products_count})</span></a></li>`).join('');
    }

    function footerHTML() {
        const s = settings;
        const year = new Date().getFullYear();
        return `
        <footer class="site-footer">
            <div class="container footer-grid">
                <div class="footer-brand">
                    <img src="${esc(logoUrl())}" alt="${esc(s.site_name)}" class="${s.logo_url ? 'custom' : ''}">
                    <p class="footer-slogan">${esc(s.footer_text || '')}</p>
                </div>
                <div class="footer-col footer-pay">
                    <h4>We Accept</h4>
                    <img class="pay-methods" src="assets/images/payments/shurjopay-methods.jpg" width="1024" height="303" loading="lazy"
                        alt="Powered by shurjoPay: Mastercard, Visa, American Express, NexusPay, UnionPay, bKash, Nagad, Rocket, Upay, Tap, M Cash, OK Wallet, Islamic Wallet, Pathao Pay, Pocket, Meghna Pay, Cellfin">
                    <div class="pay-cod"><i class="fa-solid fa-truck-fast"></i> Cash on Delivery available all over Bangladesh</div>
                </div>
                <div class="footer-col">
                    <h4>Shop Location</h4>
                    <ul>
                        ${s.address ? `<li><i class="fa-solid fa-location-dot"></i> ${esc(s.address)}</li>` : ''}
                        ${s.phone ? `<li><i class="fa-solid fa-square-phone"></i> <a href="tel:${esc(s.phone)}">${esc(s.phone)}</a></li>` : ''}
                        ${s.whatsapp ? `<li><i class="fa-brands fa-whatsapp"></i> <a href="${esc(whatsappUrl(s.whatsapp))}" target="_blank" rel="noopener">WhatsApp: ${esc(s.whatsapp)}</a></li>` : ''}
                        ${s.email ? `<li><i class="fa-solid fa-envelope"></i> <a href="mailto:${esc(s.email)}">${esc(s.email)}</a></li>` : ''}
                        ${s.facebook ? `<li><i class="fa-brands fa-facebook"></i> <a href="${esc(s.facebook)}" target="_blank" rel="noopener">facebook</a></li>` : ''}
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="shop.html">Shop</a></li>
                        <li><a href="track-order.html">Track Your Order</a></li>
                        <li><a href="account.html">My Account</a></li>
                        <li><a href="refund-returns.html">Refund &amp; Returns</a></li>
                        <li><a href="terms-conditions.html">Terms &amp; Conditions</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom"><div class="container">
                <span>© ${year} ${esc(s.site_name)}. All rights reserved.</span>
                <span>Made with <i class="fa-solid fa-heart" style="color:var(--primary)"></i> in Bangladesh</span>
            </div></div>
        </footer>
        ${s.whatsapp ? `<a class="whatsapp-float" href="${esc(whatsappUrl(s.whatsapp))}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>` : ''}
        <button class="scroll-top" id="scrollTop" aria-label="Back to top"><i class="fa-solid fa-chevron-up"></i></button>`;
    }

    function renderTopbar() {
        const el = $('#topbarLinks');
        if (!el) return;
        const user = Auth.user();
        el.innerHTML = user
            ? `<a href="track-order.html">Track Order</a><a href="account.html"><i class="fa-regular fa-user"></i> ${esc(user.name)}</a><a href="#" data-logout>Logout</a>`
            : `<a href="track-order.html">Track Order</a><a href="account.html">My Account</a><a href="account.html#login">Login</a>`;
    }

    function updateBadges() {
        $$('[data-cart-count]').forEach((el) => { el.textContent = Cart.count(); });
        $$('[data-cart-total]').forEach((el) => { el.textContent = money(Cart.subtotal()); });
        $$('[data-wish-count]').forEach((el) => { el.textContent = Wishlist.items().length; });
    }

    function syncWishButtons() {
        $$('[data-wish]').forEach((btn) => {
            const on = Wishlist.has(btn.dataset.wish);
            btn.classList.toggle('active', on);
            const icon = btn.querySelector('i');
            if (icon) icon.className = (on ? 'fa-solid' : 'fa-regular') + ' fa-heart';
            const label = btn.querySelector('.lbl');
            if (label) label.textContent = on ? 'In wishlist' : 'Add to wishlist';
        });
    }

    function renderLayout() {
        const header = $('#site-header');
        const footer = $('#site-footer');
        if (header) header.innerHTML = headerHTML();
        if (footer) footer.innerHTML = footerHTML();
        renderTopbar();
        updateBadges();
        bindLayout();
    }

    function refreshBranding() {
        const brand = $('#brand');
        if (brand) brand.innerHTML = brandHTML();
        const dd = $('#navCategories');
        if (dd) dd.innerHTML = categoriesDropdownHTML();
        const footer = $('#site-footer');
        if (footer) {
            footer.innerHTML = footerHTML();
            bindScrollTop();
        }
        if (document.title.includes('{site}')) document.title = document.title.replace('{site}', settings.site_name);
    }

    /* ---------------- Layout behaviour ---------------- */
    function bindScrollTop() {
        const btn = $('#scrollTop');
        if (!btn) return;
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    function bindLayout() {
        const nav = $('#mainNav');
        const backdrop = $('#mobileBackdrop');
        const closeMenu = () => { nav?.classList.remove('open'); backdrop?.classList.remove('open'); };
        $('#menuToggle')?.addEventListener('click', () => { nav.classList.add('open'); backdrop.classList.add('open'); });
        $('#menuClose')?.addEventListener('click', closeMenu);
        backdrop?.addEventListener('click', closeMenu);

        $$('[data-dropdown-toggle]').forEach((a) => {
            a.addEventListener('click', (e) => {
                if (window.innerWidth <= 900) {
                    e.preventDefault();
                    a.parentElement.classList.toggle('open');
                }
            });
        });

        bindScrollTop();
        window.addEventListener('scroll', () => {
            $('#scrollTop')?.classList.toggle('show', window.scrollY > 400);
        }, { passive: true });

        // Live search suggestions
        const form = $('#headerSearch');
        const input = form?.querySelector('input');
        const box = $('#searchSuggest');
        if (form && input && box) {
            const run = debounce(async () => {
                const term = input.value.trim();
                if (term.length < 2) { box.classList.add('hidden'); return; }
                try {
                    const res = await api('/products?per_page=6&search=' + encodeURIComponent(term), { auth: false });
                    box.innerHTML = res.data.length
                        ? res.data.map((p) => `<a href="product.html?slug=${encodeURIComponent(p.slug)}">
                                <img src="${esc(imgSrc(p.image_url))}" alt="">
                                <div><div class="s-name">${esc(p.name)}</div><div class="s-price">${money(p.final_price)}</div></div>
                            </a>`).join('') + `<a href="shop.html?search=${encodeURIComponent(term)}" style="justify-content:center;color:var(--primary)">View all results</a>`
                        : '<div class="s-empty">No products found.</div>';
                    box.classList.remove('hidden');
                } catch (e) { box.classList.add('hidden'); }
            }, 280);
            input.addEventListener('input', run);
            input.addEventListener('focus', () => { if (box.innerHTML && input.value.trim().length >= 2) box.classList.remove('hidden'); });
            document.addEventListener('click', (e) => { if (!form.contains(e.target)) box.classList.add('hidden'); });
            form.addEventListener('submit', (e) => {
                if (!input.value.trim()) e.preventDefault();
            });
        }
    }

    /* ---------------- Global click handlers ---------------- */
    document.addEventListener('click', async (e) => {
        const orderBtn = e.target.closest('[data-order]');
        if (orderBtn) {
            e.preventDefault();
            const p = productCache.get(orderBtn.dataset.order);
            if (Cart.add(p, 1)) location.href = 'checkout.html';
            return;
        }

        const addBtn = e.target.closest('[data-add-cart]');
        if (addBtn) {
            e.preventDefault();
            const p = productCache.get(addBtn.dataset.addCart);
            if (Cart.add(p, 1)) toast(`"${p.name}" added to cart.`, 'success', { href: 'cart.html', text: 'View cart' });
            return;
        }

        const wishBtn = e.target.closest('[data-wish]');
        if (wishBtn) {
            e.preventDefault();
            const p = productCache.get(wishBtn.dataset.wish);
            if (p) Wishlist.toggle(p);
            return;
        }

        if (e.target.closest('[data-logout]')) {
            e.preventDefault();
            await Auth.logout();
            toast('You have been logged out.');
            if (document.body.dataset.page === 'account') location.reload();
        }
    });

    // Keep tabs in sync when the cart changes in another tab
    window.addEventListener('storage', (e) => {
        if ([KEYS.cart, KEYS.wishlist].includes(e.key)) {
            updateBadges();
            syncWishButtons();
            document.dispatchEvent(new CustomEvent('cart:changed'));
        }
    });

    /* ---------------- Reusable UI builders ---------------- */
    function priceHTML(p) {
        return p.on_sale
            ? `<span class="now">${money(p.final_price)}</span><del>${money(p.price)}</del>`
            : `<span class="now">${money(p.final_price)}</span>`;
    }

    function productCard(p) {
        remember([p]);
        const url = 'product.html?slug=' + encodeURIComponent(p.slug);
        const inWish = Wishlist.has(p.id);
        return `
        <div class="product-card">
            ${p.on_sale ? '<div class="sale-ribbon"><span>Sale!</span></div>' : ''}
            ${!p.in_stock ? '<span class="out-badge">Out of stock</span>' : ''}
            <a href="${url}" class="thumb"><img src="${esc(imgSrc(p.image_url))}" alt="${esc(p.name)}" loading="lazy"></a>
            <div class="info">
                <a href="${url}" class="name">${esc(p.name)}</a>
                <div class="price">${priceHTML(p)}</div>
                <div class="actions-row">
                    <button class="btn-order" data-add-cart="${p.id}" title="Add to cart" ${p.in_stock ? '' : 'disabled'}><i class="fa-solid fa-basket-shopping"></i></button>
                    <button class="btn-order" data-order="${p.id}" ${p.in_stock ? '' : 'disabled'}><i class="fa-solid fa-cart-shopping"></i> Order Now</button>
                </div>
                <button class="wish-btn ${inWish ? 'active' : ''}" data-wish="${p.id}">
                    <i class="${inWish ? 'fa-solid' : 'fa-regular'} fa-heart"></i> <span class="lbl">${inWish ? 'In wishlist' : 'Add to wishlist'}</span>
                </button>
            </div>
        </div>`;
    }

    function skeletonCards(n = 10) {
        return Array.from({ length: n }, () => '<div class="product-card skeleton skeleton-card"></div>').join('');
    }

    function emptyBox(icon, title, text = '', action = '') {
        return `<div class="empty-box"><i class="fa-solid ${icon}"></i><h3>${esc(title)}</h3>${text ? `<p>${esc(text)}</p>` : ''}${action}</div>`;
    }

    /** Renders the standard right-hand sidebar used on content pages. */
    async function renderSidebar(el, opts = {}) {
        if (!el) return;
        const parts = [];
        if (opts.search !== false) {
            parts.push(`<div class="widget"><form class="widget-search" action="shop.html">
                <input type="search" name="search" placeholder="Search …" aria-label="Search">
                <button type="submit" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form></div>`);
        }
        parts.push(`<div class="widget"><div class="widget-title">Product Categories</div><ul class="cat-list">
            ${categories.map((c) => `<li><a href="shop.html?category=${encodeURIComponent(c.slug)}" class="${opts.activeCategory === c.slug ? 'active' : ''}"><span class="nm">${esc(c.name)}</span><span>(${c.products_count})</span></a></li>`).join('')}
        </ul></div>`);
        if (opts.priceFilter) parts.push('<div class="widget price-filter" id="priceFilterWidget"></div>');
        parts.push('<div class="widget"><div class="widget-title">Popular Products</div><ul class="widget-products" id="sidebarProducts"><li class="text-muted">Loading…</li></ul></div>');
        el.innerHTML = parts.join('');

        try {
            const res = await api('/products?per_page=5&sort=popular', { auth: false });
            remember(res.data);
            $('#sidebarProducts', el).innerHTML = res.data.map((p) => `
                <li>
                    <a href="product.html?slug=${encodeURIComponent(p.slug)}"><img src="${esc(imgSrc(p.image_url))}" alt="" loading="lazy"></a>
                    <div>
                        <a class="nm" href="product.html?slug=${encodeURIComponent(p.slug)}">${esc(p.name)}</a>
                        <div class="price">${priceHTML(p)}</div>
                    </div>
                </li>`).join('');
        } catch (e) {
            $('#sidebarProducts', el).innerHTML = '';
        }
    }

    /** Shows Laravel validation errors next to the matching inputs. */
    function showFormErrors(form, err) {
        clearFormErrors(form);
        const errors = err.errors || {};
        let first = null;
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            const msg = Array.isArray(messages) ? messages[0] : messages;
            if (input) {
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'field-error';
                div.textContent = msg;
                (input.closest('.password-wrap') || input).insertAdjacentElement('afterend', div);
                first = first || input;
            }
        });
        const alertBox = form.querySelector('[data-form-alert]');
        if (alertBox) {
            const unmatched = Object.entries(errors).filter(([f]) => !form.querySelector(`[name="${f}"]`));
            const list = unmatched.flatMap(([, m]) => m);
            if (list.length || !Object.keys(errors).length) {
                alertBox.className = 'alert alert-error';
                alertBox.innerHTML = list.length ? `<ul>${list.map((m) => `<li>${esc(m)}</li>`).join('')}</ul>` : esc(err.message);
            }
        }
        first?.focus();
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.field-error').forEach((e) => e.remove());
        form.querySelectorAll('.is-invalid').forEach((e) => e.classList.remove('is-invalid'));
        const alertBox = form.querySelector('[data-form-alert]');
        if (alertBox) { alertBox.className = 'hidden'; alertBox.innerHTML = ''; }
    }

    function setLoading(btn, loading, text) {
        if (!btn) return;
        if (loading) {
            btn.dataset.label = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner"></span> ${esc(text || 'Please wait…')}`;
        } else {
            btn.disabled = false;
            if (btn.dataset.label) btn.innerHTML = btn.dataset.label;
        }
    }

    function bindPasswordToggles(root = document) {
        $$('.password-wrap', root).forEach((wrap) => {
            const btn = wrap.querySelector('button');
            const input = wrap.querySelector('input');
            if (!btn || btn.dataset.bound) return;
            btn.dataset.bound = '1';
            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.innerHTML = `<i class="fa-regular ${show ? 'fa-eye' : 'fa-eye-slash'}"></i>`;
            });
        });
    }

    /* ---------------- Boot ---------------- */
    const readyCallbacks = [];
    let booted = false;

    function ready(fn) {
        if (booted) fn();
        else readyCallbacks.push(fn);
    }

    async function boot() {
        renderLayout();
        await Promise.all([loadSettings(), loadCategories()]);
        refreshBranding();
        booted = true;
        readyCallbacks.forEach((fn) => {
            try { fn(); } catch (e) { console.error(e); }
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
    else boot();

    window.App = {
        api, money, esc, qs, toast, debounce, formatDate, imgSrc, whatsappUrl,
        Cart, Wishlist, Auth,
        get settings() { return settings; },
        get categories() { return categories; },
        remember, productCard, priceHTML, skeletonCards, emptyBox,
        renderSidebar, showFormErrors, clearFormErrors, setLoading, bindPasswordToggles,
        syncWishButtons, updateBadges, ready,
    };
})();
