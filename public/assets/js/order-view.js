/* Renders a single order (used on the tracking and account pages). */
window.OrderView = (function () {
    const STEPS = [
        ['pending', 'Order placed', 'fa-receipt'],
        ['processing', 'Processing', 'fa-gear'],
        ['shipped', 'Shipped', 'fa-truck'],
        ['delivered', 'Delivered', 'fa-house-circle-check'],
    ];

    function tracker(status) {
        if (status === 'cancelled') {
            return '<div class="alert alert-error"><i class="fa-solid fa-ban"></i> This order has been cancelled.</div>';
        }
        const idx = STEPS.findIndex(([key]) => key === status);
        const pct = idx <= 0 ? 0 : (idx / (STEPS.length - 1)) * 84;
        return `<div class="tracker"><div class="fill" style="width:${pct}%"></div>
            ${STEPS.map(([, label, icon], i) => `
                <div class="step ${i <= idx ? 'done' : ''}">
                    <div class="dot"><i class="fa-solid ${icon}"></i></div><span>${label}</span>
                </div>`).join('')}
        </div>`;
    }

    function hero(o, payment) {
        if (payment === 'failed' || payment === 'cancelled') {
            return `<div class="success-hero">
                <div class="check-circle failed"><i class="fa-solid fa-xmark"></i></div>
                <h2>${payment === 'cancelled' ? 'Payment cancelled' : 'Payment failed'}</h2>
                <p class="text-muted">Your order is saved but has not been paid yet. You can try again below.</p>
            </div>`;
        }
        if (payment === 'pending' && o.payment_status !== 'paid') {
            return `<div class="success-hero">
                <div class="check-circle pending"><i class="fa-solid fa-hourglass-half"></i></div>
                <h2>Waiting for payment confirmation</h2>
                <p class="text-muted">We haven't received a confirmation from shurjoPay yet. If you completed the payment, it will be updated shortly.</p>
            </div>`;
        }
        return `<div class="success-hero">
            <div class="check-circle"><i class="fa-solid fa-check"></i></div>
            <h2>${o.payment_status === 'paid' ? 'Payment successful!' : 'Thank you for your order!'}</h2>
            <p class="text-muted">${o.payment_status === 'paid'
                ? 'We have received your payment and will start processing your order right away.'
                : 'We have received your order and will call you shortly to confirm it.'}</p>
        </div>`;
    }

    function payButton(o) {
        const { esc, money } = App;
        if (!o.can_pay) return '';
        return `<div class="pay-now-box">
            <div><strong>This order is not paid yet.</strong><br><span class="text-muted">Pay securely with bKash, Nagad, Rocket or card via shurjoPay.</span></div>
            <button type="button" class="btn" data-pay-order="${esc(o.order_number)}" data-phone="${esc(o.phone)}"><i class="fa-solid fa-lock"></i> Pay ${money(o.total)} now</button>
        </div>`;
    }

    // One delegated handler for every "Pay now" button rendered by this module.
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-pay-order]');
        if (!btn) return;
        App.setLoading(btn, true, 'Connecting to shurjoPay…');
        try {
            const res = await App.api(`/orders/${encodeURIComponent(btn.dataset.payOrder)}/pay`, {
                method: 'POST', auth: false, body: { phone: btn.dataset.phone },
            });
            location.href = res.payment_url;
        } catch (err) {
            App.setLoading(btn, false);
            App.toast(err.message, 'error');
        }
    });

    function render(o, { isNew = false, payment = null } = {}) {
        const { esc, money, formatDate, imgSrc } = App;
        return `
        <div class="box" style="margin-bottom:24px">
            ${isNew || payment ? hero(o, payment) : ''}
            ${payButton(o)}
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:center">
                <div>
                    <div class="text-muted" style="font-size:12px">ORDER NUMBER</div>
                    <div style="font-size:20px;font-weight:600">${esc(o.order_number)}</div>
                </div>
                <div style="text-align:right">
                    <span class="status status-${esc(o.status)}">${esc(o.status_label)}</span>
                    <div class="text-muted" style="font-size:12px;margin-top:4px">Placed on ${formatDate(o.created_at)}</div>
                </div>
            </div>
            ${tracker(o.status)}

            <table class="orders-table">
                <thead><tr><th>Product</th><th>Qty</th><th style="text-align:right">Total</th></tr></thead>
                <tbody>
                ${o.items.map((i) => `<tr>
                    <td><div style="display:flex;gap:10px;align-items:center">
                        <img src="${esc(imgSrc(i.image_url))}" alt="" style="width:44px;height:44px;object-fit:cover">
                        <span class="bn" style="font-size:14px">${esc(i.product_name)}</span>
                    </div></td>
                    <td>${money(i.price)} × ${i.quantity}</td>
                    <td style="text-align:right">${money(i.total)}</td>
                </tr>`).join('')}
                </tbody>
            </table>
            <div style="max-width:320px;margin:14px 0 0 auto">
                <div class="summary-row"><span>Subtotal</span><span>${money(o.subtotal)}</span></div>
                <div class="summary-row"><span>Delivery (${esc(o.shipping_area_label)})</span><span>${money(o.shipping_cost)}</span></div>
                ${o.discount > 0 ? `<div class="summary-row"><span>Discount</span><span>-${money(o.discount)}</span></div>` : ''}
                <div class="summary-row total"><span>Total</span><span>${money(o.total)}</span></div>
            </div>

            <div class="form-row" style="margin-top:20px">
                <div>
                    <h4 style="font-size:15px">Delivery address</h4>
                    <p style="margin:0">${esc(o.customer_name)}<br>${esc(o.phone)}<br>${esc(o.address)}${o.city ? ', ' + esc(o.city) : ''}</p>
                </div>
                <div>
                    <h4 style="font-size:15px">Payment</h4>
                    <p style="margin:0">${esc(o.payment_method_label)}${o.payment_channel ? ` (${esc(o.payment_channel)})` : ''}${o.transaction_id ? `<br>TrxID: ${esc(o.transaction_id)}` : ''}<br>
                    Status: ${o.payment_status === 'paid' ? '<span class="stock-in">Paid</span>' : esc(o.payment_status_label || o.payment_status)}</p>
                </div>
            </div>
        </div>`;
    }

    return { render };
})();
