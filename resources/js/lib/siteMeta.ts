/**
 * The two halves of every tab title. Tabs read "{page} | {site}", and the
 * homepage puts the tagline where a page name would otherwise go.
 *
 * Hard-coded for the demo, like the rest of the store-wide copy; in the real
 * store these become editable content rather than frontend constants.
 */
export const SITE_NAME = 'Keisove.net';

export const SITE_TAGLINE = 'Онлайн магазин за аксесоари за мобилни телефони';

/**
 * The fixed rate Bulgaria adopted the euro at, printed in the footer beside the
 * VAT note. Not demo copy - it is a statutory constant, and it mirrors
 * App\Support\Price::EUR_RATE, which is what actually converts the prices.
 */
export const EUR_RATE = '1.95583';
