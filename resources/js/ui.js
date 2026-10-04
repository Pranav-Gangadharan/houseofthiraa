// Small, dependency-free behaviours for the storefront.

/** Home: filter and sort the shelf without a reload. */
export function mountShelf() {
    const shelf = document.querySelector('[data-shelf]');
    if (!shelf) return;

    const grid = shelf.querySelector('[data-grid]');
    const count = shelf.querySelector('[data-count]');
    const pills = [...shelf.querySelectorAll('[data-filter]')];
    const cards = [...shelf.querySelectorAll('[data-category]')];
    const featured = [...cards];

    const recount = () => {
        const n = cards.filter((c) => !c.hidden).length;
        if (count) count.textContent = `${n} ${n === 1 ? 'piece' : 'pieces'}`;
    };

    const apply = (category, { scroll = false } = {}) => {
        const all = category === 'all';
        cards.forEach((card) => (card.hidden = !all && card.dataset.category !== category));
        pills.forEach((p) => p.setAttribute('aria-pressed', String(p.dataset.filter === category)));
        recount();

        const url = new URL(location.href);
        all ? url.searchParams.delete('c') : url.searchParams.set('c', category);
        url.hash = scroll ? 'shop' : url.hash;
        history.replaceState(null, '', url);
        if (scroll) document.getElementById('shop')?.scrollIntoView({ behavior: 'smooth' });
    };

    pills.forEach((p) => p.addEventListener('click', () => apply(p.dataset.filter)));

    document.querySelectorAll('[data-cat]').forEach((a) => {
        a.addEventListener('click', (e) => {
            e.preventDefault();
            apply(a.dataset.cat, { scroll: true });
        });
    });

    const by = {
        featured: () => featured,
        new: () => [...cards].sort((a, b) => b.dataset.new - a.dataset.new),
        low: () => [...cards].sort((a, b) => a.dataset.price - b.dataset.price),
        high: () => [...cards].sort((a, b) => b.dataset.price - a.dataset.price),
    };
    shelf.querySelector('[data-sort]')?.addEventListener('change', (e) => {
        grid?.append(...(by[e.target.value] ?? by.featured)());
    });

    recount();
}

/** Home: arrow buttons for the horizontal "new arrivals" rail. */
export function mountRails() {
    document.querySelectorAll('[data-rail]').forEach((rail) => {
        const track = rail.querySelector('[data-rail-track]');
        const step = (dir) => track.scrollBy({ left: dir * track.clientWidth * 0.8, behavior: 'smooth' });
        rail.querySelector('[data-rail-prev]')?.addEventListener('click', () => step(-1));
        rail.querySelector('[data-rail-next]')?.addEventListener('click', () => step(1));
    });
}

/** Product: swap the main photo from the thumbnails; open the size guide; sync mobile carousel. */
export function mountProduct() {
    const main = document.querySelector('[data-main-photo]');
    document.querySelectorAll('[data-thumb]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (main) main.src = btn.dataset.thumb;
            document.querySelectorAll('[data-thumb]').forEach((b) => b.removeAttribute('aria-current'));
            btn.setAttribute('aria-current', 'true');
        });
    });

    const dialog = document.querySelector('dialog.guide');
    document.querySelector('[data-open-guide]')?.addEventListener('click', () => dialog?.showModal());
    document.querySelectorAll('[data-close-guide]').forEach((btn) => btn.addEventListener('click', () => dialog?.close()));
    dialog?.addEventListener('click', (e) => e.target === dialog && dialog.close());

    // Show the chosen size next to the label
    const sizeLabel = document.querySelector('[data-size-label]');
    document.querySelectorAll('#buy-form [name=size]').forEach((radio) => {
        radio.addEventListener('change', () => sizeLabel && (sizeLabel.textContent = ` · ${radio.value}`));
    });

    // Phone buy bar appears once the real buttons scroll out of view
    const form = document.getElementById('buy-form');
    const bar = document.querySelector('[data-buy-bar]');
    if (form && bar) {
        const update = () => bar.toggleAttribute('data-show', form.getBoundingClientRect().bottom < 0);
        addEventListener('scroll', update, { passive: true });
        update();
    }

    // Mobile swipe carousel dots
    const carousel = document.querySelector('[data-mobile-carousel]');
    const dots = [...document.querySelectorAll('[data-carousel-dot]')];
    if (carousel && dots.length > 1) {
        let scrollTimeout;
        carousel.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const width = carousel.offsetWidth || 1;
                const index = Math.round(carousel.scrollLeft / width);
                dots.forEach((dot, i) => {
                    dot.setAttribute('aria-current', String(i === index));
                });
            }, 50);
        }, { passive: true });

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.dataset.carouselDot, 10);
                const width = carousel.offsetWidth || 1;
                carousel.scrollTo({ left: idx * width, behavior: 'smooth' });
            });
        });
    }
}

/** Checkout: type a pincode, get the city and state. Quietly does nothing if the lookup is unreachable. */
export function mountCheckout() {
    const pin = document.querySelector('[data-pincode]');
    if (pin) {
        const city = document.querySelector('[name=city]');
        const state = document.querySelector('[name=state]');
        let lastLookup = '';

        pin.addEventListener('input', async () => {
            const value = pin.value.replace(/\D/g, '').slice(0, 6);
            pin.value = value;
            if (value.length !== 6 || value === lastLookup) return;
            lastLookup = value;

            try {
                const res = await fetch(`https://api.postalpincode.in/pincode/${value}`);
                const [body] = await res.json();
                const office = body?.PostOffice?.[0];
                if (!office || pin.value !== value) return;

                if (!city.value) city.value = office.District;
                const match = [...state.options].find((o) => o.value.toLowerCase() === office.State.toLowerCase());
                if (match && !state.value) state.value = match.value;
            } catch {
                /* offline or blocked: customer types manually */
            }
        });
    }

    const phone = document.querySelector('[name=phone]');
    phone?.addEventListener('input', () => (phone.value = phone.value.replace(/[^\d+\s-]/g, '')));

    // Mobile order summary collapsible
    const summaryToggle = document.querySelector('[data-summary-toggle]');
    const summaryContent = document.querySelector('[data-summary-content]');
    if (summaryToggle && summaryContent) {
        summaryToggle.addEventListener('click', () => {
            const isExpanded = summaryToggle.getAttribute('aria-expanded') === 'true';
            summaryToggle.setAttribute('aria-expanded', String(!isExpanded));
            summaryContent.hidden = isExpanded;
            const icon = summaryToggle.querySelector('[data-summary-icon]');
            if (icon) {
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        });
    }
}

/** Pay page: open Razorpay straight away, keep a button for when it is dismissed. */
export function mountPay() {
    const root = document.querySelector('[data-razorpay]');
    if (!root || typeof Razorpay === 'undefined') return;

    const cfg = JSON.parse(root.dataset.razorpay);
    const form = document.querySelector('[data-verify]');
    const button = document.querySelector('[data-pay]');

    const open = () => {
        const rzp = new Razorpay({
            key: cfg.key,
            amount: cfg.amount,
            currency: 'INR',
            order_id: cfg.order_id,
            name: 'House of Thiraa',
            description: cfg.description,
            prefill: cfg.prefill,
            theme: { color: '#9d0b1b' },
            handler: (response) => {
                form.razorpay_payment_id.value = response.razorpay_payment_id;
                form.razorpay_order_id.value = response.razorpay_order_id;
                form.razorpay_signature.value = response.razorpay_signature;
                form.submit();
            },
        });
        rzp.open();
    };

    button?.addEventListener('click', open);
    open();
}

/** Announcement bar: on narrow screens show one promise at a time. */
export function mountTicker() {
    const list = document.querySelector('[data-ticker]');
    if (!list || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const items = [...list.children];
    let index = 0;
    const show = () => items.forEach((li, i) => li.toggleAttribute('data-active', i === index));
    show();
    setInterval(() => {
        index = (index + 1) % items.length;
        show();
    }, 3500);
}

/** Header: a hairline shadow once the page scrolls; phone search row toggle. */
export function mountHeader() {
    const header = document.querySelector('[data-site-header]');
    if (!header) return;

    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', update, { passive: true });
    update();

    const toggle = header.querySelector('[data-toggle-search]');
    const row = header.querySelector('[data-search-row]');
    toggle?.addEventListener('click', () => {
        row.hidden = !row.hidden;
        toggle.setAttribute('aria-expanded', String(!row.hidden));
        if (!row.hidden) row.querySelector('input')?.focus();
    });
}

/** Mobile navigation drawer */
export function mountMobileDrawer() {
    const openBtn = document.querySelector('[data-open-drawer]');
    const drawer = document.querySelector('[data-mobile-drawer]');
    const backdrop = document.querySelector('[data-drawer-backdrop]');
    const closeBtn = document.querySelector('[data-close-drawer]');

    if (!drawer || !backdrop) return;

    const open = () => {
        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        openBtn?.setAttribute('aria-expanded', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeBtn?.focus();
    };

    const close = () => {
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        openBtn?.setAttribute('aria-expanded', 'false');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        openBtn?.focus();
    };

    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
            close();
        }
    });

    // Close when clicking category links inside drawer
    drawer.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            close();
        });
    });
}

/** Quick add posts and redirects back; land where the shopper was instead of at the top. */
export function mountQuickAdd() {
    const key = 'thiraa_scroll';
    try {
        const saved = JSON.parse(sessionStorage.getItem(key) ?? 'null');
        sessionStorage.removeItem(key);
        if (saved?.path === location.pathname) {
            document.documentElement.style.scrollBehavior = 'auto';
            scrollTo(0, saved.y);
            document.documentElement.style.scrollBehavior = '';
        }
    } catch {
        /* storage blocked: the page simply opens at the top */
    }

    document.querySelectorAll('.quick-add').forEach((form) => {
        form.addEventListener('submit', () => {
            try {
                sessionStorage.setItem(key, JSON.stringify({ path: location.pathname, y: scrollY }));
            } catch {
                /* ignore */
            }
        });
    });
}
