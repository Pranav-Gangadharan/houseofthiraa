/*
 * The pixel field.
 *
 * A fixed canvas of square "stitches" behind every page. Four quiet moods of
 * cloth (wave, bell, drape, pair) ease into each other when a category is hovered.
 * On the home page the stitches also assemble into the stamp's silhouette.
 * Pure canvas, no libraries. Honors prefers-reduced-motion by drawing one still frame.
 */

const SIZE = [0, 0.3, 0.55, 0.82]; // square edge as a share of the cell, per level
const ALPHA = [0, 0.09, 0.16, 0.26];
const FRAME_MS = 1000 / 30;
const REVEAL_MS = 2800;

const clamp = (x, a = 0, b = 1) => Math.min(b, Math.max(a, x));
const smooth = (a, b, x) => {
    const t = clamp((x - a) / (b - a));
    return t * t * (3 - 2 * t);
};
const lerp = (a, b, t) => a + (b - a) * t;

// Stable per-cell randomness so the pattern never shimmers between frames.
const hash = (i, j) => {
    let h = Math.imul(i, 374761393) + Math.imul(j, 668265263);
    h = Math.imul(h ^ (h >>> 13), 1274126177);
    return ((h ^ (h >>> 16)) >>> 0) / 4294967295;
};

// Each field returns 0..1 for a cell. `c` carries the grid size, i/j are cell coords.
const FIELDS = {
    // default: slow sinuous ripple, like the wavy band of the stamp
    wave: (i, j, t) => {
        const a = Math.sin(i * 0.13 + Math.sin(j * 0.07 + t * 0.5) * 2.2 + t * 0.4);
        const b = Math.sin(j * 0.11 - i * 0.05 - t * 0.3);
        return (a * 0.6 + b * 0.4) * 0.5 + 0.5;
    },
    // midi: rings swelling out from below, the swing of a skirt
    bell: (i, j, t, c) => {
        const d = Math.hypot((i - c.cols / 2) * 0.85, j - c.rows * 1.15);
        return Math.sin(d * 0.3 - t * 0.9) * 0.5 + 0.5;
    },
    // maxi: long vertical folds falling to the floor
    drape: (i, j, t) => {
        const fold = Math.sin(i * 0.42 + Math.sin(i * 0.11) * 3);
        const flow = Math.sin(j * 0.045 - t * 0.6 + i * 0.25);
        return (fold * 0.5 + 0.5) * 0.6 + (flow * 0.5 + 0.5) * 0.4;
    },
    // co-ord: two blocks, one above the other, offset by a seam
    pair: (i, j, t, c) => {
        const seam = c.rows * 0.5 + Math.sin(t * 0.4) * 2;
        const block = (Math.floor(i / 4) + Math.floor(j / 4) + (j > seam ? 1 : 0)) & 1;
        const grain = Math.sin(i * 0.9) * Math.sin(j * 0.9) * 0.12;
        return block ? 0.78 + grain : 0.3 + grain;
    },
};

const MOODS = Object.keys(FIELDS);

export function mountPixelField(canvas) {
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const red = getComputedStyle(document.documentElement).getPropertyValue('--red').trim() || '#9d0b1b';

    const grid = { W: 0, H: 0, cell: 10, cols: 0, rows: 0, dpr: 1 };
    const weights = Object.fromEntries(MOODS.map((m) => [m, m === 'wave' ? 1 : 0]));
    let target = MOODS.includes(document.body.dataset.mood) ? document.body.dataset.mood : 'wave';
    if (target !== 'wave') Object.assign(weights, { wave: 0, [target]: 1 });

    const pointer = { x: -1e4, y: -1e4, tx: -1e4, ty: -1e4 };
    let time = 0;
    let last = 0;
    let raf = 0;

    // --- the silhouette ---------------------------------------------------
    const bust = { el: document.querySelector('[data-bust]'), img: null, alpha: null, w: 0, h: 0, startedAt: null };

    function sampleBust(rect) {
        const w = Math.max(1, Math.round(rect.width / grid.cell));
        const h = Math.max(1, Math.round(rect.height / grid.cell));
        if (bust.alpha && bust.w === w && bust.h === h) return;

        // Shrinking the image to one pixel per cell gives a soft coverage value for free.
        const off = document.createElement('canvas');
        off.width = w;
        off.height = h;
        const octx = off.getContext('2d', { willReadFrequently: true });
        octx.drawImage(bust.img, 0, 0, w, h);
        const data = octx.getImageData(0, 0, w, h).data;

        bust.alpha = new Float32Array(w * h);
        for (let k = 0; k < w * h; k++) bust.alpha[k] = data[k * 4 + 3] / 255;
        bust.w = w;
        bust.h = h;
    }

    function initBust() {
        if (!bust.el) return;
        const img = bust.el.querySelector('img');
        if (!img) return;

        const ready = () => {
            bust.img = img;
            document.documentElement.classList.add('has-bust');
            bust.startedAt = reduced ? -REVEAL_MS : performance.now() + 350;
            schedule();
        };
        img.complete && img.naturalWidth ? ready() : img.addEventListener('load', ready, { once: true });
    }

    // --- sizing -----------------------------------------------------------
    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        const W = canvas.clientWidth;
        const H = canvas.clientHeight;
        grid.cell = W < 640 ? 7 : 9;
        Object.assign(grid, { W, H, dpr, cols: Math.ceil(W / grid.cell) + 1, rows: Math.ceil(H / grid.cell) + 1 });
        canvas.width = Math.round(W * dpr);
        canvas.height = Math.round(H * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        bust.alpha = null;
        schedule();
    }

    // --- drawing ----------------------------------------------------------
    function frame(now) {
        raf = 0;
        if (document.hidden) return;

        if (!reduced) {
            if (now - last < FRAME_MS) return loop();
            time += Math.min((now - last) / 1000, 0.1);
            last = now;

            for (const m of MOODS) weights[m] += ((m === target ? 1 : 0) - weights[m]) * 0.07;
            pointer.x += (pointer.tx - pointer.x) * 0.2;
            pointer.y += (pointer.ty - pointer.y) * 0.2;
        }

        draw(now);
        if (!reduced) loop();
    }

    function draw(now) {
        const { W, H, cell, cols, rows } = grid;
        ctx.clearRect(0, 0, W, H);
        ctx.fillStyle = red;

        const narrow = W < 720;
        const scroll = (window.scrollY * 0.22) / cell; // the cloth drifts a little as you scroll
        const active = MOODS.filter((m) => weights[m] > 0.01);
        const weightSum = active.reduce((s, m) => s + weights[m], 0);
        const ctxInfo = { cols, rows };

        // silhouette placement (viewport cells)
        let bustOn = false;
        let ox = 0;
        let oy = 0;
        let progress = 0;
        if (bust.img && bust.el) {
            const rect = bust.el.getBoundingClientRect();
            if (rect.bottom > 0 && rect.top < H && rect.width > 0) {
                sampleBust(rect);
                ox = Math.round(rect.left / cell);
                oy = Math.round(rect.top / cell);
                progress = clamp((now - bust.startedAt) / REVEAL_MS);
                progress = progress * progress * (3 - 2 * progress);
                bustOn = true;
            }
        }

        const reach = 130;
        let lastAlpha = -1;

        for (let j = 0; j < rows; j++) {
            for (let i = 0; i < cols; i++) {
                const cx = i * cell + cell / 2;
                const cy = j * cell + cell / 2;

                // base cloth
                let v = 0;
                for (const m of active) v += FIELDS[m](i, j + scroll, time, ctxInfo) * weights[m];
                v /= weightSum;

                // thicker at the margins, calmer behind the content
                const edge = smooth(0.06, 0.5, Math.abs(cx / W - 0.5) * 2);
                v *= (narrow ? 0.5 : 0.32) + (narrow ? 0.4 : 0.68) * edge;

                // the cursor lifts stitches around it
                const dx = cx - pointer.x;
                const dy = cy - pointer.y;
                const pd = Math.hypot(dx, dy) / reach;
                const swell = pd < 1 ? (1 - pd) * (1 - pd) : 0;
                v += swell * 0.7;

                let level = v > 0.8 ? 3 : v > 0.64 ? 2 : v > 0.5 ? 1 : 0;
                let size = SIZE[level] * cell;
                let alpha = ALPHA[level];

                // the silhouette
                if (bustOn) {
                    const bi = i - ox;
                    const bj = j - oy;
                    if (bi >= 0 && bj >= 0 && bi < bust.w && bj < bust.h) {
                        const a = bust.alpha[bj * bust.w + bi];
                        if (a > 0.2) {
                            const born = clamp((progress * 1.4 - (bj / bust.h) * 0.3 - hash(i, j) * 0.55) * 5);
                            const body = lerp(SIZE[3] * 0.9, 1.02, clamp((a - 0.2) / 0.6)) * cell;
                            const breathe = 0.97 + 0.03 * Math.sin(time * 1.4 + hash(j, i) * 6.28);
                            const parted = 1 - 0.85 * swell;
                            size = lerp(size, body * breathe * parted, born);
                            alpha = lerp(alpha, 1, born * (1 - 0.6 * swell));
                        }
                    }
                }

                if (size < 1 || alpha <= 0) continue;

                if (alpha !== lastAlpha) {
                    ctx.globalAlpha = alpha;
                    lastAlpha = alpha;
                }
                const s = Math.round(size);
                ctx.fillRect(Math.round(cx - s / 2), Math.round(cy - s / 2), s, s);
            }
        }
        ctx.globalAlpha = 1;
    }

    // --- scheduling -------------------------------------------------------
    function loop() {
        if (!raf) raf = requestAnimationFrame(frame);
    }
    function schedule() {
        if (!raf) raf = requestAnimationFrame(frame);
    }

    // --- wiring -----------------------------------------------------------
    new ResizeObserver(resize).observe(canvas);

    if (!reduced) {
        addEventListener('pointermove', (e) => {
            if (e.pointerType === 'touch') return;
            pointer.tx = e.clientX;
            pointer.ty = e.clientY;
            if (pointer.x < -1000) Object.assign(pointer, { x: e.clientX, y: e.clientY });
        }, { passive: true });
        document.documentElement.addEventListener('mouseleave', () => Object.assign(pointer, { tx: -1e4, ty: -1e4 }));
        document.addEventListener('visibilitychange', () => { if (!document.hidden) { last = performance.now(); schedule(); } });
    } else {
        addEventListener('scroll', schedule, { passive: true });
    }

    document.addEventListener('pixels:mood', (e) => {
        target = MOODS.includes(e.detail) ? e.detail : 'wave';
        if (reduced) {
            for (const m of MOODS) weights[m] = m === target ? 1 : 0;
            schedule();
        }
    });

    initBust();
    resize();
}
