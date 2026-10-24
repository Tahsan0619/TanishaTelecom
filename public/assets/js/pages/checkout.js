App.ready(async () => {
    const { api, esc, money, imgSrc, emptyBox, Cart, Auth, toast, showFormErrors, clearFormErrors, setLoading } = App;
    const root = document.getElementById('checkoutContent');
    const s = App.settings;

    if (!Cart.items().length) {
        root.innerHTML = emptyBox('fa-basket-shopping', 'Your cart is empty', 'Add some products before checking out.',
            '<a href="shop.html" class="btn" style="margin-top:10px">Go to shop</a>');
        return;
    }

    let notice = '';
    try {
        const { changed, items } = await Cart.refresh();
        if (!items.length) {
            root.innerHTML = emptyBox('fa-basket-shopping', 'Items are no longer available', 'The products in your cart are out of stock.',
                '<a href="shop.html" class="btn" style="margin-top:10px">Go to shop</a>');
            return;
        }
        if (changed) notice = '<div class="alert alert-info">Your cart was updated to reflect current prices and stock.</div>';
    } catch (e) { /* proceed with local cart; server re-validates */ }

    const user = Auth.user() || {};

    root.innerHTML = `
    ${notice}
    ${!Auth.check() ? '<div class="alert alert-info">Returning customer? <a href="account.html#login" class="text-primary"><strong>Click here to login</strong></a> or simply continue as a guest.</div>' : ''}
    <form id="checkoutForm" novalidate>
        <div class="cart-layout">
            <div class="box">
                <h2 class="page-title" style="margin-bottom:18px">Billing &amp; Shipping</h2>
                <div data-form-alert class="hidden"></div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Full name <span class="req">*</span></label>
                        <input class="form-control" name="customer_name" value="${esc(user.name || '')}" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile number <span class="req">*</span></label>
                        <input class="form-control" name="phone" type="tel" placeholder="01XXXXXXXXX" value="${esc(user.phone || '')}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email address (optional)</label>
                    <input class="form-control" name="email" type="email" value="${esc(user.email || '')}">
                </div>
                <div class="form-group">
                    <label>Full address <span class="req">*</span></label>
                    <textarea class="form-control" name="address" rows="3" placeholder="House, road, area" required>${esc(user.address || '')}</textarea>
                </div>
                <div class="form-group">
                    <label>City / District</label>
                    <input class="form-control" name="city" value="${esc(user.city || '')}">
                </div>

                <div class="form-group">
                    <label>Delivery area <span class="req">*</span></label>
                    <div class="option-cards" id="areaOptions">
                        <label class="option-card selected">
                            <input type="radio" name="shipping_area" value="inside_dhaka" checked>
                            <div><div class="oc-title">Inside Dhaka</div><div class="oc-sub">Delivery in 24-48 hours</div></div>
                            <span class="oc-price">${Cart.freeShipping() ? 'Free' : money(s.shipping_inside_dhaka)}</span>
                        </label>
                        <label class="option-card">
                            <input type="radio" name="shipping_area" value="outside_dhaka">
                            <div><div class="oc-title">Outside Dhaka</div><div class="oc-sub">Delivery in 2-5 days</div></div>
                            <span class="oc-price">${Cart.freeShipping() ? 'Free' : money(s.shipping_outside_dhaka)}</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Order notes (optional)</label>
                    <textarea class="form-control" name="notes" rows="2" placeholder="e.g. Please call before delivery"></textarea>
                </div>
            </div>

            <div class="box summary-box">
                <h3>Your order</h3>
                <div class="summary-items" id="summaryItems"></div>
                <div class="summary-row"><span>Subtotal</span><span id="sumSubtotal"></span></div>
                <div class="summary-row"><span>Delivery charge</span><span id="sumShipping"></span></div>
                <div class="summary-row total"><span>Total</span><span id="sumTotal"></span></div>

                <h3 style="margin-top:20px">Payment method</h3>
                <div class="option-cards" id="payOptions">
                    <label class="option-card selected">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <span class="pay-logo cod">COD</span>
                        <div><div class="oc-title">Cash On Delivery</div><div class="oc-sub">Pay when you receive</div></div>
                    </label>
                    ${s.online_payment ? `<label class="option-card">
                        <input type="radio" name="payment_method" value="shurjopay">
                        <div><div class="oc-title">ShurjoPay</div><div class="oc-sub">bKash, Nagad, Rocket, Visa, Mastercard &amp; more</div></div>
                        <img class="sp-logo" src="assets/images/payments/shurjopay-logo.png" width="332" height="92" alt="shurjoPay">
                    </label>` : ''}
                </div>
                <div class="pay-instructions hidden" id="payInstructions">
                    <i class="fa-solid fa-lock" style="color:var(--success)"></i>
                    You will be redirected to the secure <strong>shurjoPay</strong> payment page to complete your payment.
                </div>

                <p class="text-muted" style="font-size:12px;margin:16px 0">By placing your order you agree to our <a href="terms-conditions.html" class="text-primary">terms &amp; conditions</a> and <a href="refund-returns.html" class="text-primary">refund policy</a>.</p>
                <button type="submit" class="btn btn-block btn-lg" id="placeOrder"><i class="fa-solid fa-lock"></i> Place order</button>
            </div>
        </div>
    </form>`;

    const form = document.getElementById('checkoutForm');

    function shipping() {
        if (Cart.freeShipping()) return 0;
        const area = form.querySelector('[name=shipping_area]:checked').value;
        return area === 'outside_dhaka' ? Number(s.shipping_outside_dhaka) : Number(s.shipping_inside_dhaka);
    }

    function renderSummary() {
        const items = Cart.items();
        document.getElementById('summaryItems').innerHTML = items.map((i) => `
            <div class="summary-item">
                <img src="${esc(imgSrc(i.image))}" alt="">
                <div class="nm">${esc(i.name)}<small>${money(i.price)} × ${i.qty}</small></div>
                <strong>${money(i.price * i.qty)}</strong>
            </div>`).join('');
        const sub = Cart.subtotal();
        document.getElementById('sumSubtotal').textContent = money(sub);
        document.getElementById('sumShipping').textContent = Cart.freeShipping() ? 'Free' : money(shipping());
        document.getElementById('sumTotal').textContent = money(sub + shipping());
    }

    const isOnline = () => form.querySelector('[name=payment_method]:checked').value === 'shurjopay';

    function renderPayInstructions() {
        document.getElementById('payInstructions').classList.toggle('hidden', !isOnline());
        document.getElementById('placeOrder').innerHTML = isOnline()
            ? `<i class="fa-solid fa-lock"></i> Pay ${money(Cart.subtotal() + shipping())} securely`
            : '<i class="fa-solid fa-lock"></i> Place order';
    }

    form.addEventListener('change', (e) => {
        if (e.target.name === 'shipping_area' || e.target.name === 'payment_method') {
            const group = e.target.closest('.option-cards');
            group.querySelectorAll('.option-card').forEach((c) => c.classList.toggle('selected', c.contains(e.target)));
            renderSummary();
            renderPayInstructions();
        }
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFormErrors(form);
        const fd = new FormData(form);
        const payload = Object.fromEntries(fd.entries());
        payload.phone = (payload.phone || '').replace(/[\s-]/g, '');
        payload.items = Cart.items().map((i) => ({ product_id: i.id, quantity: i.qty }));

        const btn = document.getElementById('placeOrder');
        setLoading(btn, true, isOnline() ? 'Connecting to shurjoPay…' : 'Placing order…');
        try {
            const res = await api('/orders', { method: 'POST', body: payload });
            Cart.clear();
            const o = res.data;
            if (res.payment_url) {
                location.href = res.payment_url;
                return;
            }
            location.href = `track-order.html?order=${encodeURIComponent(o.order_number)}&phone=${encodeURIComponent(o.phone)}&new=1`;
        } catch (err) {
            setLoading(btn, false);
            renderPayInstructions();
            showFormErrors(form, err);
            if (err.status !== 422) toast(err.message, 'error');
            if (err.errors?.items) {
                Cart.refresh().then(renderSummary).catch(() => {});
            }
            form.querySelector('[data-form-alert]').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    renderSummary();
    renderPayInstructions();
});
