import { mountPixelField } from './pixels.js';
import { mountDresses } from './dress.js';
import { mountShelf, mountMoods, mountProduct, mountCheckout, mountPay } from './ui.js';

const field = document.getElementById('field');
if (field) mountPixelField(field);

mountDresses();
mountShelf();
mountMoods();
mountProduct();
mountCheckout();
addEventListener('load', mountPay);
