/*
 * Pixel dresses: stand-ins for product photography.
 * Each piece is drawn as a little cross-stitch chart in its own colour, so the
 * storefront looks finished before a single photo is uploaded. Real photos
 * replace these automatically (see the product form in /admin).
 */

const COLS = 30;
const ROWS = 40;

const hash = (i, j, seed) => {
    let h = Math.imul(i + seed * 31, 374761393) + Math.imul(j + seed * 17, 668265263);
    h = Math.imul(h ^ (h >>> 13), 1274126177);
    return ((h ^ (h >>> 16)) >>> 0) / 4294967295;
};

const lerp = (a, b, t) => a + (b - a) * t;
const prog = (v, a, b) => Math.min(1, Math.max(0, (v - a) / (b - a)));

// Shapes are described in unit space: u = 0..1 across, v = 0..1 down. Return true where fabric is.
const SHAPES = {
    midi: (u, v) => {
        const d = Math.abs(u - 0.5);
        if (v < 0.22) return v > 0.1 && Math.abs(d - 0.115) < 0.02; // straps
        if (v < 0.4) {
            const hw = lerp(0.14, 0.095, prog(v, 0.22, 0.4));
            return d < hw && !(v < 0.29 && d < 0.055 * (1 - prog(v, 0.22, 0.29))); // sweetheart neckline
        }
        const hem = 0.8 + 0.012 * Math.sin(u * 34);
        return v < hem && d < lerp(0.095, 0.31, prog(v, 0.4, 0.8));
    },
    maxi: (u, v) => {
        const d = Math.abs(u - 0.5);
        if (v < 0.22) return v > 0.1 && Math.abs(d - 0.115) < 0.02;
        if (v < 0.4) return d < lerp(0.14, 0.09, prog(v, 0.22, 0.4)) && !(v < 0.28 && d < 0.05);
        const hem = 0.95 + 0.012 * Math.sin(u * 34);
        return v < hem && d < lerp(0.09, 0.4, Math.pow(prog(v, 0.4, 0.95), 0.85));
    },
    coord: (u, v) => {
        const d = Math.abs(u - 0.5);
        // short-sleeve boxy top
        if (v >= 0.14 && v < 0.36) {
            const sleeve = v < 0.26 ? 0.25 : 0;
            return d < Math.max(sleeve, 0.17) && !(v < 0.17 && d < 0.06);
        }
        // wide-leg trousers with a waistband
        if (v >= 0.42 && v < 0.9) {
            if (v < 0.46) return d < 0.135;
            const leg = lerp(0.085, 0.14, prog(v, 0.46, 0.9));
            return Math.abs(d - lerp(0.075, 0.13, prog(v, 0.46, 0.9))) < leg * 0.62 && d > 0.008;
        }
        return false;
    },
};

const toRgb = (hex) => {
    const n = parseInt(hex.slice(1), 16);
    return [n >> 16, (n >> 8) & 255, n & 255];
};

// Blend an [r,g,b] toward white (255) or black (0) and return a canvas colour.
const tint = (rgb, toward, t) => `rgb(${rgb.map((x) => Math.round(lerp(x, toward, t))).join(',')})`;

function fabric(i, j, seed) {
    // four weaves, chosen by the piece's seed. Returns how much lighter the stitch is.
    switch (seed % 4) {
        case 0: return (i + j) % 7 === 0 ? 0.3 : 0; // fine diagonal
        case 1: return i % 6 === 0 ? 0.28 : 0; // pinstripe
        case 2: return Math.abs((i % 6) - 2) + Math.abs((j % 6) - 2) === 1 ? 0.45 : 0; // tiny florals
        default: return (Math.floor(i / 3) + Math.floor(j / 3)) % 2 ? 0.18 : 0; // gingham
    }
}

function paint(el) {
    const canvas = el.querySelector('canvas');
    el.dataset.painted = '1';
    const { width, height } = el.getBoundingClientRect();
    if (!width) return;

    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    canvas.width = Math.round(width * dpr);
    canvas.height = Math.round(height * dpr);
    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);

    const kind = SHAPES[el.dataset.kind] ? el.dataset.kind : 'midi';
    const color = /^#[0-9a-f]{6}$/i.test(el.dataset.color) ? el.dataset.color : '#9d0b1b';
    const seed = parseInt(el.dataset.seed, 10) || 0;
    const shape = SHAPES[kind];

    const cw = width / COLS;
    const ch = height / ROWS;

    // faint graph paper so the chart reads as stitches even where there is no fabric
    ctx.fillStyle = 'rgb(157 11 27 / 0.06)';
    for (let j = 0; j < ROWS; j++) {
        for (let i = 0; i < COLS; i++) {
            ctx.fillRect(Math.round(i * cw + cw * 0.42), Math.round(j * ch + ch * 0.42), Math.max(1, cw * 0.16), Math.max(1, ch * 0.16));
        }
    }

    for (let j = 0; j < ROWS; j++) {
        for (let i = 0; i < COLS; i++) {
            const u = (i + 0.5) / COLS;
            const v = (j + 0.5) / ROWS;
            if (!shape(0.5 + (u - 0.5) * 0.78, v)) continue; // widen the silhouette to fill the tile

            const edge = Math.abs(u - 0.5) * 2; // darker toward the sides, like folds
            const grain = hash(i, j, seed) * 0.1 - 0.05;
            const lift = fabric(i, j, seed);
            const base = lift > 0 ? toRgb(color).map((x) => lerp(x, 255, lift)) : toRgb(color);
            ctx.fillStyle = tint(base, 0, Math.max(0, edge * 0.14 + grain));

            const x0 = Math.round(i * cw);
            const y0 = Math.round(j * ch);
            ctx.fillRect(x0, y0, Math.round((i + 1) * cw) - x0 - 1, Math.round((j + 1) * ch) - y0 - 1);
        }
    }
}

export function mountDresses(root = document) {
    const els = [...root.querySelectorAll('[data-dress]')];
    if (!els.length) return;

    const io = new IntersectionObserver((entries) => {
        for (const e of entries) {
            if (e.isIntersecting) {
                paint(e.target);
                io.unobserve(e.target);
            }
        }
    }, { rootMargin: '200px' });

    const ro = new ResizeObserver((entries) => {
        for (const e of entries) if (e.target.dataset.painted) paint(e.target);
    });

    for (const el of els) {
        io.observe(el);
        ro.observe(el);
    }
}
