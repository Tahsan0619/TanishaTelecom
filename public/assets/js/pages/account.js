App.ready(async () => {
    const { api, esc, money, formatDate, Auth, toast, showFormErrors, clearFormErrors, setLoading, bindPasswordToggles } = App;
    const root = document.getElementById('accountContent');
    App.renderSidebar(document.getElementById('sidebar'));

    const pwField = (name, label, autocomplete) => `
        <div class="form-group">
            <label>${label} <span class="req">*</span></label>
            <div class="password-wrap">
                <input class="form-control" type="password" name="${name}" autocomplete="${autocomplete}" required>
                <button type="button" aria-label="Show password"><i class="fa-regular fa-eye-slash"></i></button>
            </div>
        </div>`;

    /* ---------- Guest: login / register ---------- */
    function renderGuest() {
        root.innerHTML = `
        <div class="auth-grid">
            <div id="login">
                <h2>Login</h2>
                <form class="auth-card" id="loginForm" novalidate>
                    <div data-form-alert class="hidden"></div>
                    <div class="form-group">
                        <label>Email address <span class="req">*</span></label>
                        <input class="form-control" type="email" name="email" autocomplete="email" required>
                    </div>
                    ${pwField('password', 'Password', 'current-password')}
                    <button class="btn" type="submit">Log in</button>
                </form>
            </div>
            <div>
                <h2>Register</h2>
                <form class="auth-card" id="registerForm" novalidate>
                    <div data-form-alert class="hidden"></div>
                    <div class="form-group">
                        <label>Full name <span class="req">*</span></label>
                        <input class="form-control" name="name" autocomplete="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email address <span class="req">*</span></label>
                        <input class="form-control" type="email" name="email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile number</label>
                        <input class="form-control" type="tel" name="phone" placeholder="01XXXXXXXXX" autocomplete="tel">
                    </div>
                    ${pwField('password', 'Password', 'new-password')}
                    ${pwField('password_confirmation', 'Confirm password', 'new-password')}
                    <button class="btn" type="submit">Register</button>
                </form>
            </div>
        </div>`;
        bindPasswordToggles(root);

        const handle = (formId, endpoint, successMsg) => {
            const form = document.getElementById(formId);
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearFormErrors(form);
                const btn = form.querySelector('button[type=submit]');
                setLoading(btn, true);
                try {
                    const res = await api(endpoint, { method: 'POST', body: Object.fromEntries(new FormData(form).entries()), auth: false });
                    Auth.set(res.token, res.user);
                    toast(successMsg);
                    const redirect = new URLSearchParams(location.search).get('redirect');
                    if (redirect && /^[a-z-]+\.html/.test(redirect)) location.href = redirect;
                    else renderDashboard('dashboard');
                } catch (err) {
                    showFormErrors(form, err);
                } finally {
                    setLoading(btn, false);
                }
            });
        };
        handle('loginForm', '/auth/login', 'Welcome back!');
        handle('registerForm', '/auth/register', 'Your account has been created.');

        if (location.hash === '#login') document.querySelector('#loginForm [name=email]').focus();
    }

    /* ---------- Logged in: dashboard ---------- */
    const TABS = [
        ['dashboard', 'Dashboard', 'fa-gauge'],
        ['orders', 'Orders', 'fa-bag-shopping'],
        ['details', 'Account details', 'fa-user'],
        ['password', 'Change password', 'fa-key'],
        ['logout', 'Logout', 'fa-right-from-bracket'],
    ];

    function renderDashboard(tab) {
        root.innerHTML = `
        <div class="account-layout">
            <ul class="account-nav">${TABS.map(([key, label, icon]) => `
                <li><a data-tab="${key}" class="${key === tab ? 'active' : ''}"><i class="fa-solid ${icon}"></i> ${label}</a></li>`).join('')}
            </ul>
            <div id="tabContent"></div>
        </div>`;
        root.querySelectorAll('[data-tab]').forEach((a) => a.addEventListener('click', async () => {
            if (a.dataset.tab === 'logout') {
                await Auth.logout();
                toast('You have been logged out.');
                renderGuest();
                return;
            }
            history.replaceState(null, '', '#' + a.dataset.tab);
            renderDashboard(a.dataset.tab);
        }));
        const fn = { dashboard: tabDashboard, orders: tabOrders, details: tabDetails, password: tabPassword }[tab] || tabDashboard;
        fn(document.getElementById('tabContent'));
    }

    function tabDashboard(el) {
        const u = Auth.user();
        el.innerHTML = `
            <p>Hello <strong>${esc(u.name)}</strong> (not ${esc(u.name)}? <a href="#" data-logout class="text-primary">Log out</a>)</p>
            <p>From your account dashboard you can view your <a href="#" data-go="orders" class="text-primary">recent orders</a>,
            and edit your <a href="#" data-go="details" class="text-primary">shipping address and account details</a>.</p>
            <div id="recentOrders"><p class="text-muted">Loading orders…</p></div>`;
        el.querySelectorAll('[data-go]').forEach((a) => a.addEventListener('click', (e) => { e.preventDefault(); renderDashboard(a.dataset.go); }));
        loadOrders(el.querySelector('#recentOrders'), 3);
    }

    function tabOrders(el) {
        el.innerHTML = '<div id="ordersList"><p class="text-muted">Loading orders…</p></div>';
        loadOrders(el.querySelector('#ordersList'));
    }

    async function loadOrders(el, limit) {
        try {
            const res = await api('/my/orders');
            let orders = res.data;
            if (!orders.length) {
                el.innerHTML = '<div class="alert alert-info">No order has been made yet. <a href="shop.html" class="text-primary"><strong>Browse products</strong></a></div>';
                return;
            }
            if (limit) orders = orders.slice(0, limit);
            el.innerHTML = `
                ${limit ? '<h3 style="font-size:16px;margin-top:20px">Recent orders</h3>' : ''}
                <div style="overflow-x:auto"><table class="orders-table">
                    <thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr></thead>
                    <tbody>${orders.map((o) => `
                        <tr>
                            <td><strong>${esc(o.order_number)}</strong></td>
                            <td>${formatDate(o.created_at)}</td>
                            <td><span class="status status-${esc(o.status)}">${esc(o.status_label)}</span></td>
                            <td>${money(o.total)} <span class="text-muted">for ${o.items.reduce((s, i) => s + i.quantity, 0)} item(s)</span></td>
                            <td><button class="btn btn-sm" data-view="${esc(o.order_number)}">View</button></td>
                        </tr>`).join('')}
                    </tbody>
                </table></div>`;
            el.querySelectorAll('[data-view]').forEach((b) => b.addEventListener('click', () => {
                const order = res.data.find((o) => o.order_number === b.dataset.view);
                const tab = document.getElementById('tabContent');
                tab.innerHTML = `<button class="btn btn-outline btn-sm" id="backToOrders" style="margin-bottom:14px"><i class="fa-solid fa-arrow-left"></i> Back to orders</button>${OrderView.render(order)}`;
                tab.querySelector('#backToOrders').addEventListener('click', () => renderDashboard('orders'));
            }));
        } catch (err) {
            el.innerHTML = `<div class="alert alert-error">${esc(err.message)}</div>`;
            if (err.status === 401) renderGuest();
        }
    }

    function tabDetails(el) {
        const u = Auth.user();
        el.innerHTML = `
            <form id="detailsForm" novalidate>
                <div data-form-alert class="hidden"></div>
                <div class="form-row">
                    <div class="form-group"><label>Full name <span class="req">*</span></label><input class="form-control" name="name" value="${esc(u.name)}" required></div>
                    <div class="form-group"><label>Mobile number</label><input class="form-control" name="phone" value="${esc(u.phone || '')}"></div>
                </div>
                <div class="form-group"><label>Email address <span class="req">*</span></label><input class="form-control" type="email" name="email" value="${esc(u.email)}" required></div>
                <div class="form-group"><label>Address</label><textarea class="form-control" name="address" rows="3">${esc(u.address || '')}</textarea></div>
                <div class="form-group"><label>City / District</label><input class="form-control" name="city" value="${esc(u.city || '')}"></div>
                <button class="btn" type="submit">Save changes</button>
            </form>`;
        const form = el.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFormErrors(form);
            const btn = form.querySelector('button[type=submit]');
            setLoading(btn, true, 'Saving…');
            try {
                const res = await api('/auth/profile', { method: 'PUT', body: Object.fromEntries(new FormData(form).entries()) });
                Auth.setUser(res.user);
                toast(res.message);
            } catch (err) {
                showFormErrors(form, err);
            } finally {
                setLoading(btn, false);
            }
        });
    }

    function tabPassword(el) {
        el.innerHTML = `
            <form id="passwordForm" novalidate style="max-width:440px">
                <div data-form-alert class="hidden"></div>
                ${pwField('current_password', 'Current password', 'current-password')}
                ${pwField('password', 'New password', 'new-password')}
                ${pwField('password_confirmation', 'Confirm new password', 'new-password')}
                <button class="btn" type="submit">Change password</button>
            </form>`;
        bindPasswordToggles(el);
        const form = el.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFormErrors(form);
            const btn = form.querySelector('button[type=submit]');
            setLoading(btn, true, 'Saving…');
            try {
                const res = await api('/auth/password', { method: 'PUT', body: Object.fromEntries(new FormData(form).entries()) });
                toast(res.message);
                form.reset();
            } catch (err) {
                showFormErrors(form, err);
            } finally {
                setLoading(btn, false);
            }
        });
    }

    /* ---------- Init ---------- */
    if (!Auth.check()) {
        renderGuest();
        return;
    }
    const initialTab = location.hash.replace('#', '');
    renderDashboard(TABS.some(([k]) => k === initialTab) ? initialTab : 'dashboard');
    try {
        const res = await api('/auth/me');
        Auth.setUser(res.user);
    } catch (err) {
        if (err.status === 401) renderGuest();
    }
});
