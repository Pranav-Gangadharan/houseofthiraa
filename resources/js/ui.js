// Small, dependency-free behaviours for the storefront.

/** Home: filter the shelf by category without a reload. */
export function mountShelf() {
    const shelf = document.querySelector('[data-shelf]');
    if (!shelf) return;

    const pills = [...document.querySelectorAll('[data-filter]')];
    const cards = [...shelf.querySelectorAll('[data-category]')];

    const apply = (category, { scroll = false } = {}) => {
        const all = category === 'all';
        cards.forEach((card) => (card.hidden = !all && card.dataset.category !== category));
        pills.forEach((p) => p.setAttribute('aria-pressed', String(p.dataset.filter === category)));

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
}

/** Category words steer the pixel field's mood while hovered or focused. */
export function mountMoods() {
    const set = (mood) => document.dispatchEvent(new CustomEvent('pixels:mood', { detail: mood }));

    document.querySelectorAll('[data-mood]').forEach((el) => {
        ['mouseenter', 'focus'].forEach((ev) => el.addEventListener(ev, () => set(el.dataset.mood)));
        ['mouseleave', 'blur'].forEach((ev) => el.addEventListener(ev, () => set(document.body.dataset.mood || 'wave')));
    });
}

/** Product: swap the main photo from the thumbnails; open the size guide. */
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
    dialog?.addEventListener('click', (e) => e.target === dialog && dialog.close());
}

/** Checkout: type a pincode, get the city and state. Quietly does nothing if the lookup is unreachable. */
export function mountCheckout() {
    const pin = document.querySelector('[data-pincode]');
    if (!pin) return;

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
            /* offline or blocked: the customer just types it */
        }
    });

    const phone = document.querySelector('[name=phone]');
    phone?.addEventListener('input', () => (phone.value = phone.value.replace(/[^\d+\s-]/g, '')));
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

    button.addEventListener('click', open);
    open();
}
