import { mountDresses } from './dress.js';
import {
    mountShelf,
    mountRails,
    mountProduct,
    mountCheckout,
    mountPay,
    mountTicker,
    mountHeader,
    mountMobileDrawer,
    mountQuickAdd,
} from './ui.js';

mountDresses();
mountShelf();
mountRails();
mountProduct();
mountCheckout();
mountTicker();
mountHeader();
mountMobileDrawer();
mountQuickAdd();

addEventListener('load', mountPay);
