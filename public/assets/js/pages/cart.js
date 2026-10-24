App.ready(async () => {
    const { esc, money, imgSrc, emptyBox, Cart } = App;
    const content = document.getElementById('cartContent');
    const notice = document.getElementById('cartNotice');

    function render() {
        const items = Cart.items();
        if (!items.length) {
            content.innerHTML = emptyBox('fa-basket-shopping', 'Your cart is currently empty.', 'Browse our products and add something delicious!',
                '<a href="shop.html" class="btn" style="margin-top:10px">Return to shop</a>');
            return;
        }

        const s = App.settings;
        content.innerHTML = `
        <div class="cart-layout">
            <div class="box" style="padding:20px">
                <table class="cart-table">
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody>
                        ${items.map((i) => `
                        <tr data-id="${i.id}">
                            <td class="c-product"><div class="p-cell">
                                <a href="product.html?slug=${encodeURIComponent(i.slug)}"><img src="${esc(imgSrc(i.image))}" alt=""></a>
                                <div><a href="product.html?slug=${encodeURIComponent(i.slug)}">${esc(i.name)}</a>${i.unit ? `<div class="text-muted" style="font-size:12px">${esc(i.unit)}</div>` : ''}</div>
                            </div></td>
                            <td>${money(i.price)}</td>
                            <td>
                                <div class="qty sm">
                                    <button type="button" data-step="-1" aria-label="Decrease">−</button>
                                    <input type="number" value="${i.qty}" min="1" max="${i.stock || 99}" data-qty aria-label="Quantity">
                                    <button type="button" data-step="1" aria-label="Increase">+</button>
                                </div>
                            </td>
                            <td><strong>${money(i.price * i.qty)}</strong></td>
                            <td><button class="remove" data-remove title="Remove"><i class="fa-regular fa-trash-can"></i></button></td>
                        </tr>`).join('')}
                    </tbody>
                </table>
                <div style="display:flex;justify-content:space-between;margin-top:18px;gap:10px;flex-wrap:wrap">
                    <a href="shop.html" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Continue shopping</a>
                    <button class="btn btn-outline" id="clearCart"><i class="fa-regular fa-trash-can"></i> Clear cart</button>
                </div>
            </div>
            <div class="box summary-box">
                <h3>Cart totals</h3>
                <div class="summary-row"><span>Subtotal</span><span>${money(Cart.subtotal())}</span></div>
                <div class="summary-row"><span>Delivery</span>${Cart.freeShipping() ? '<span>Free</span>' : `<span class="text-muted" style="text-align:right;font-size:13px">Inside Dhaka ${money(s.shipping_inside_dhaka)}<br>Outside Dhaka ${money(s.shipping_outside_dhaka)}</span>`}</div>
                <div class="summary-row total"><span>Total</span><span>${money(Cart.subtotal())}</span></div>
                ${Cart.freeShipping() ? '<div style="height:16px"></div>' : '<p class="text-muted" style="font-size:12px;margin:4px 0 16px">Delivery charge is added at checkout.</p>'}
                <a href="checkout.html" class="btn btn-block btn-lg">Proceed to checkout <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>`;
    }

    content.addEventListener('click', (e) => {
        const row = e.target.closest('tr[data-id]');
        if (e.target.closest('#clearCart')) {
            if (confirm('Remove all items from your cart?')) { Cart.clear(); render(); }
            return;
        }
        if (!row) return;
        const id = Number(row.dataset.id);
        if (e.target.closest('[data-remove]')) {
            Cart.remove(id);
            render();
            return;
        }
        const step = e.target.closest('[data-step]');
        if (step) {
            const item = Cart.items().find((i) => i.id === id);
            Cart.setQty(id, item.qty + Number(step.dataset.step));
            render();
        }
    });

    content.addEventListener('change', (e) => {
        if (!e.target.matches('[data-qty]')) return;
        const id = Number(e.target.closest('tr').dataset.id);
        Cart.setQty(id, Number(e.target.value) || 1);
        render();
    });

    render();

    try {
        const { changed } = await Cart.refresh();
        if (changed) {
            notice.innerHTML = '<div class="alert alert-info">Some items in your cart were updated to reflect current prices and stock.</div>';
            render();
        }
    } catch (e) { /* keep showing local cart */ }
});
