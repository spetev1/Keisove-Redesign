import { home, promotions, search } from '@/routes';
import { show as categoryShow } from '@/routes/category';

/**
 * Every storefront link lives here so that wiring a section up once its page
 * exists is a one-line change rather than a hunt through header and footer.
 *
 * `#` marks a destination the demo does not build yet.
 */
export type StorefrontLink = {
    title: string;
    href: string;
};

/**
 * Categories are seeded, so their pages are real destinations rather than
 * placeholders. Slugs are the latin transliterations used in the database.
 */
export function categoryHref(
    slug: string,
    query?: Record<string, string>,
): string {
    return categoryShow.url(slug, query ? { query } : undefined);
}

/**
 * A department narrowed to one handset family.
 *
 * The bracketed key is deliberate: the category controller reads `devices` as a
 * list, so the query string has to arrive as one rather than as a bare scalar
 * that would be dropped by the whitelist.
 */
export function deviceFilterHref(
    categorySlug: string,
    deviceValue: string,
): string {
    return categoryHref(categorySlug, { 'devices[]': deviceValue });
}

/**
 * The header search hands the term straight to the results page, so what is
 * searched for stays in the URL and can be shared or reloaded.
 */
export function searchHref(term: string): string {
    return search.url({ query: { q: term } });
}

/**
 * The nav row's leading tab. It is not a department, so it has no page of its
 * own - it opens the homepage on the section that lays every department out.
 */
export const ALL_CATEGORIES_LINK: StorefrontLink = {
    title: 'Всички категории',
    href: `${home.url()}#kategorii`,
};

/** The promotions tab, which the nav paints differently from the departments. */
export const PROMOTIONS_LINK: StorefrontLink = {
    title: 'Промоции',
    href: promotions.url(),
};

/**
 * The footer's one written column. Its "Категории" column is built from the
 * departments in the database rather than listed here, so a department added to
 * the catalogue appears in the footer without this file being touched.
 */
export const footerHelpLinks: StorefrontLink[] = [
    { title: 'Доставка и плащане', href: '#' },
    { title: 'Връщане и рекламации', href: '#' },
    { title: 'Общи условия', href: '#' },
    { title: 'Контакти', href: '#' },
];

export const contactDetails = {
    phone: '0893 66 47 99',
    email: 'support@keisove.com',
    address: 'София, България',
};

/**
 * `tel:` takes digits only, while the printed number carries spaces, so the two
 * are derived rather than written out twice.
 */
export const contactPhoneHref = `tel:${contactDetails.phone.replace(/[^\d+]/g, '')}`;
