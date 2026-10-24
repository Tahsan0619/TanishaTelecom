/* Content pages (About, Refund policy, Terms). Content is edited in Admin → Pages. */
App.ready(async () => {
    const { api, esc } = App;
    const slug = document.body.dataset.slug;
    const title = document.getElementById('pageTitle');
    const body = document.getElementById('pageContent');

    App.renderSidebar(document.getElementById('sidebar'));

    try {
        const res = await api('/pages/' + encodeURIComponent(slug), { auth: false });
        title.textContent = res.data.title;
        body.innerHTML = res.data.content || '';
        document.title = `${res.data.title} | ${App.settings.site_name}`;
    } catch (err) {
        title.textContent = 'Page not available';
        body.innerHTML = `<p class="text-muted">${esc(err.status === 404 ? 'This page has not been published yet.' : err.message)}</p>`;
    }
});
