App.ready(() => {
    const { api, qs, showFormErrors, clearFormErrors, setLoading } = App;
    const form = document.getElementById('trackForm');
    const result = document.getElementById('orderResult');
    const trackBox = document.getElementById('trackBox');
    const isNew = qs('new') === '1';
    // Set by the server after returning from shurjoPay: paid | pending | cancelled | failed
    const payment = qs('payment');

    async function track(orderNumber, phone, btn) {
        clearFormErrors(form);
        setLoading(btn, true, 'Searching…');
        try {
            const res = await api(`/orders/track?order_number=${encodeURIComponent(orderNumber)}&phone=${encodeURIComponent(phone)}`, { auth: false });
            result.innerHTML = OrderView.render(res.data, { isNew, payment })
                + (isNew ? '<div class="text-center" style="margin-bottom:24px"><a href="shop.html" class="btn">Continue shopping</a></div>' : '');
            if (isNew) trackBox.classList.add('hidden');
            history.replaceState(null, '', `track-order.html?order=${encodeURIComponent(orderNumber)}&phone=${encodeURIComponent(phone)}${isNew ? '&new=1' : ''}`);
        } catch (err) {
            result.innerHTML = '';
            showFormErrors(form, err);
        } finally {
            setLoading(btn, false);
        }
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const order = form.order_number.value.trim();
        const phone = form.phone.value.trim();
        if (!order || !phone) {
            showFormErrors(form, { errors: {
                ...(order ? {} : { order_number: ['Please enter your order number.'] }),
                ...(phone ? {} : { phone: ['Please enter your mobile number.'] }),
            } });
            return;
        }
        track(order, phone, form.querySelector('button[type=submit]'));
    });

    const order = qs('order');
    const phone = qs('phone');
    if (order && phone) {
        form.order_number.value = order;
        form.phone.value = phone;
        track(order, phone, form.querySelector('button[type=submit]'));
    } else if (payment === 'failed') {
        result.innerHTML = '<div class="alert alert-error">We could not confirm your payment. Please track your order below or contact us.</div>';
    }
});
