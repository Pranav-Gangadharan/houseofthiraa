// Turns houseofthira.png (red on white) into transparent, brand-red assets:
//   public/brand/logo.png        the full stamp
//   public/brand/silhouette.png  the bust only, used as the pixel mask on the home page
// Run: npm run brand
import sharp from 'sharp';

const SRC = 'houseofthira.png';
const { data, info } = await sharp(SRC).ensureAlpha().raw().toBuffer({ resolveWithObject: true });

const probe = (700 * info.width + 640) * 4; // a point inside the silhouette
const [R, G, B] = [data[probe], data[probe + 1], data[probe + 2]];
console.log(`brand red: rgb(${R}, ${G}, ${B})`);

const out = Buffer.alloc(data.length);
for (let p = 0; p < data.length; p += 4) {
    const coverage = Math.min(1, Math.max(0, (255 - data[p + 1]) / (255 - G)));
    out[p] = R;
    out[p + 1] = G;
    out[p + 2] = B;
    out[p + 3] = Math.round(coverage * 255);
}

const full = sharp(out, { raw: { width: info.width, height: info.height, channels: 4 } });
await full.clone().png({ compressionLevel: 9 }).toFile('public/brand/logo.png');

// Bust crop, with the frame's inner corners erased
const crop = { left: 385, top: 200, width: 490, height: 620 };
const bust = await full.clone().extract(crop).raw().toBuffer();
for (let y = 0; y < crop.height; y++) {
    for (let x = 0; x < crop.width; x++) {
        if (y < 45 && (x < 100 || x > 400)) bust[(y * crop.width + x) * 4 + 3] = 0;
    }
}
await sharp(bust, { raw: { width: crop.width, height: crop.height, channels: 4 } })
    .png({ compressionLevel: 9 })
    .toFile('public/brand/silhouette.png');

// Favicon: the bust on a transparent square
await sharp('public/brand/silhouette.png')
    .resize(180, 180, { fit: 'contain', background: { r: 0, g: 0, b: 0, alpha: 0 } })
    .png({ compressionLevel: 9 })
    .toFile('public/brand/favicon.png');

// Frame: the stamp without the bust, cropped to the stamp's edges. The home page lays it over
// the pixel field, which stitches the bust back into the hole.
const stamp = Buffer.from(out);
for (let y = 0; y < crop.height; y++) {
    for (let x = 0; x < crop.width; x++) {
        const a = bust[(y * crop.width + x) * 4 + 3];
        const p = ((crop.top + y) * info.width + crop.left + x) * 4 + 3;
        stamp[p] = Math.max(0, stamp[p] - a);
    }
}
let [minX, minY, maxX, maxY] = [info.width, info.height, 0, 0];
for (let y = 0; y < info.height; y++) {
    for (let x = 0; x < info.width; x++) {
        if (stamp[(y * info.width + x) * 4 + 3] > 24) {
            minX = Math.min(minX, x); maxX = Math.max(maxX, x);
            minY = Math.min(minY, y); maxY = Math.max(maxY, y);
        }
    }
}
const box = { left: minX, top: minY, width: maxX - minX + 1, height: maxY - minY + 1 };
await sharp(stamp, { raw: { width: info.width, height: info.height, channels: 4 } })
    .extract(box)
    .png({ compressionLevel: 9 })
    .toFile('public/brand/frame.png');
console.log('frame box', box, 'bust inside frame', { left: crop.left - box.left, top: crop.top - box.top, width: crop.width, height: crop.height });

// Header logo: the full stamp cropped tight to its edges, small enough for the header.
await sharp('public/brand/logo.png')
    .extract(box)
    .resize({ height: 240 })
    .png({ compressionLevel: 9 })
    .toFile('public/brand/logo-mark.png');
