import { mountPixelField } from './pixels.js';
import { mountDresses } from './dress.js';
import {
    mountShelf,
    mountSlider,
    mountMoods,
    mountReveal,
    mountRails,
    mountProduct,
    mountCheckout,
    mountPay,
    mountTicker,
    mountHeader,
    mountMobileDrawer,
    mountQuickAdd,
    mountQuickOrder,
} from './ui.js';

const field = document.getElementById('field');
if (field) mountPixelField(field);

mountDresses();
mountShelf();
mountSlider();
mountMoods();
mountReveal();
mountRails();
mountProduct();
mountCheckout();
mountTicker();
mountHeader();
mountMobileDrawer();
mountQuickAdd();
mountQuickOrder();

addEventListener('load', mountPay);
