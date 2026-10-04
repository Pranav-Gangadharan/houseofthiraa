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
        let shown = 0;
        cards.forEach((card) => {
            card.hidden = !all && card.dataset.category !== category;
            card.classList.remove('pop');
            if (card.hidden) return;
            card.style.setProperty('--i', String(shown++ % 8));
            void card.offsetWidth;
            card.classList.add('pop');
        });
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

/**
 * Home banner slider. The progress bar on the current dot is a CSS animation; when it ends we
 * move on, so pausing the animation (hover, focus, the pause button, a hidden tab) pauses the
 * slideshow too. Reduced motion: no autoplay, slides only change on request.
 */
export function mountSlider() {
    const root = document.querySelector('[data-slider]');
    if (!root) return;

    const slides = [...root.querySelectorAll('.slide')];
    const dots = [...root.querySelectorAll('[data-dot]')];
    const toggle = root.querySelector('[data-slider-toggle]');
    if (slides.length < 2) return;

    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let index = Math.max(0, slides.findIndex((s) => s.hasAttribute('data-active')));
    let stopped = reduced;

    const go = (next) => {
        index = (next + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            const on = i === index;
            slide.toggleAttribute('data-active', on);
            slide.toggleAttribute('inert', !on);
            slide.setAttribute('aria-hidden', String(!on));
        });
        dots.forEach((dot, i) => {
            dot.toggleAttribute('aria-current', i === index);
            // restart the progress animation on the new dot
            const bar = dot.firstElementChild;
            bar.style.animation = 'none';
            void bar.offsetWidth;
            bar.style.animation = '';
        });
    };

    const setStopped = (value) => {
        stopped = value;
        root.classList.toggle('is-stopped', stopped);
        toggle?.setAttribute('aria-label', stopped ? 'Play slideshow' : 'Pause slideshow');
    };

    dots.forEach((dot, i) => {
        dot.addEventListener('click', () => go(i));
        dot.firstElementChild.addEventListener('animationend', () => {
            if (!stopped && i === index) go(index + 1);
        });
    });
    root.querySelector('[data-slider-prev]')?.addEventListener('click', () => go(index - 1));
    root.querySelector('[data-slider-next]')?.addEventListener('click', () => go(index + 1));
    toggle?.addEventListener('click', () => setStopped(!stopped));

    root.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') go(index - 1);
        if (e.key === 'ArrowRight') go(index + 1);
    });

    // swipe on touch screens; vertical scrolling still belongs to the page
    let startX = null;
    let startY = 0;
    root.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'mouse') return;
        startX = e.clientX;
        startY = e.clientY;
    }, { passive: true });
    root.addEventListener('pointerup', (e) => {
        if (startX === null) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(e.clientY - startY)) go(index + (dx < 0 ? 1 : -1));
        startX = null;
    }, { passive: true });
    root.addEventListener('pointercancel', () => (startX = null));

    setStopped(stopped);
    go(index);
}

/** Product cards: the bag button opens the quick order panel on touch screens. */
export function mountQuickOrder() {
    const cards = [...document.querySelectorAll('[data-qo-toggle]')].map((btn) => btn.closest('.card'));
    if (!cards.length) return;

    const close = (except) => cards.forEach((card) => {
        if (card === except) return;
        card.classList.remove('is-open');
        card.querySelector('[data-qo-toggle]').setAttribute('aria-expanded', 'false');
    });

    cards.forEach((card) => {
        const toggle = card.querySelector('[data-qo-toggle]');
        toggle.addEventListener('click', () => {
            close(card);
            const open = card.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
            if (open) card.querySelector('.qo input:not(:disabled)')?.focus({ preventScroll: true });
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.card.is-open')) close();
    });
    document.addEventListener('keydown', (e) => e.key === 'Escape' && close());
}

/**
 * Shop page filters. On wide screens a change applies straight away; in the phone drawer the
 * shopper picks several and taps "Show results". Empty fields are left out of the URL.
 */
export function mountFilters() {
    const root = document.querySelector('[data-catalogue]');
    const form = root?.querySelector('[data-filters]');
    if (!form) return;

    const drawer = matchMedia('(max-width: 64rem)');
    const toggle = root.querySelector('[data-filters-open]');

    const submit = () => {
        [...form.elements].forEach((el) => {
            if (el.name && el.value === '' && el.type !== 'checkbox' && el.type !== 'radio') el.disabled = true;
            if (el.type === 'radio' && el.checked && el.value === '') el.disabled = true;
        });
        // the sort select lives outside the form (form="filters"), drop it when it is the default
        const sort = document.querySelector('select[form="filters"]');
        if (sort && sort.value === 'featured') sort.disabled = true;
        root.classList.add('is-loading');
        form.submit();
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        submit();
    });
    form.addEventListener('change', (e) => {
        if (e.target.name === 'q') return;
        if (!drawer.matches) submit();
    });
    document.querySelector('select[form="filters"]')?.addEventListener('change', submit);

    const open = (on) => {
        document.documentElement.classList.toggle('filters-open', on);
        toggle?.setAttribute('aria-expanded', String(on));
        document.body.style.overflow = on ? 'hidden' : '';
    };
    toggle?.addEventListener('click', () => open(true));
    root.querySelectorAll('[data-filters-close]').forEach((el) => el.addEventListener('click', () => open(false)));
    document.addEventListener('keydown', (e) => e.key === 'Escape' && open(false));
}

/** Category links steer the pixel field's mood while hovered or focused. */
export function mountMoods() {
    const set = (mood) => document.dispatchEvent(new CustomEvent('pixels:mood', { detail: mood }));

    document.querySelectorAll('[data-mood]:not(body)').forEach((el) => {
        ['mouseenter', 'focus'].forEach((ev) => el.addEventListener(ev, () => set(el.dataset.mood)));
        ['mouseleave', 'blur'].forEach((ev) => el.addEventListener(ev, () => set(document.body.dataset.mood || 'wave')));
    });
}

/** Sections ease in the first time they scroll into view. */
export function mountReveal() {
    document.querySelectorAll('.grid > .card').forEach((card, i) => {
        card.classList.add('reveal');
        card.style.setProperty('--i', String(i % 4));
    });
    const els = document.querySelectorAll('.reveal');
    if (!els.length) return;
    if (!('IntersectionObserver' in window) || matchMedia('(prefers-reduced-motion: reduce)').matches) {
        els.forEach((el) => el.classList.add('is-in'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        for (const e of entries) {
            if (e.isIntersecting) {
                e.target.classList.add('is-in');
                io.unobserve(e.target);
            }
        }
    }, { rootMargin: '0px 0px -8% 0px' });
    els.forEach((el) => io.observe(el));
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
    const header = document.querySelector('[data-head], [data-site-header]');
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
    const drawer = document.querySelector('[data-mobile-drawer]');
    const backdrop = document.querySelector('[data-drawer-backdrop]');
    if (!drawer || !backdrop) return;

    const openers = [...document.querySelectorAll('[data-open-drawer]')];
    const search = drawer.querySelector('[data-drawer-search]');
    let returnTo = null;

    const open = (opener) => {
        returnTo = opener;
        drawer.inert = false;
        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        openers.forEach((btn) => btn.setAttribute('aria-expanded', 'true'));
        document.body.style.overflow = 'hidden';
        const target = opener?.dataset.openDrawer === 'search' ? search : drawer.querySelector('[data-close-drawer]');
        setTimeout(() => target?.focus(), 50);
    };

    const close = () => {
        if (!drawer.classList.contains('is-open')) return;
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        drawer.inert = true;
        openers.forEach((btn) => btn.setAttribute('aria-expanded', 'false'));
        document.body.style.overflow = '';
        returnTo?.focus();
    };

    openers.forEach((btn) => btn.addEventListener('click', () => open(btn)));
    drawer.querySelector('[data-close-drawer]')?.addEventListener('click', close);
    backdrop.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if (!drawer.classList.contains('is-open')) return;
        if (e.key === 'Escape') close();
        if (e.key !== 'Tab') return;

        // keep keyboard focus inside the open menu
        const focusable = [...drawer.querySelectorAll('a, button, input')];
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    });

    // the drawer only exists on small screens; tidy up if the window grows while it is open
    matchMedia('(min-width: 52.01rem)').addEventListener('change', (e) => e.matches && close());
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
