import { promotions, search } from '@/routes';
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
 * The header search hands the term straight to the results page, so what is
 * searched for stays in the URL and can be shared or reloaded.
 */
export function searchHref(term: string): string {
    return search.url({ query: { q: term } });
}

/**
 * Departments are not repeated here: the "Категории" menu already lists every
 * one of them, and the mobile sheet renders them from the same shared data.
 */
export const mainNavLinks: StorefrontLink[] = [
    { title: 'Промоции', href: promotions.url() },
    { title: 'За нас', href: '#' },
];

export const footerLinkColumns: {
    heading: string;
    links: StorefrontLink[];
}[] = [
    {
        heading: 'Категории',
        links: [
            { title: 'Кейсове', href: categoryHref('keisove') },
            { title: 'Парфюми', href: categoryHref('parfyumi') },
            { title: 'Аксесоари', href: categoryHref('aksesoari') },
            { title: 'Протектори', href: categoryHref('protektori') },
            { title: 'Детски играчки', href: categoryHref('detski-igrachki') },
            { title: 'Промоции', href: promotions.url() },
        ],
    },
    {
        heading: 'Информация',
        links: [
            { title: 'За нас', href: '#' },
            { title: 'Доставка и плащане', href: '#' },
            { title: 'Връщане и замяна', href: '#' },
            { title: 'Често задавани въпроси', href: '#' },
        ],
    },
    {
        heading: 'Помощ',
        links: [
            { title: 'Моят профил', href: '#' },
            { title: 'Моите поръчки', href: '#' },
            { title: 'Любими продукти', href: '#' },
            { title: 'Контакти', href: '#' },
        ],
    },
];

export const contactDetails = {
    phone: '0893/66 47 99',
    email: 'support@keisove.com',
    address: 'София, България',
};

/**
 * `tel:` takes digits only, while the printed number carries a slash and
 * spaces, so the two are derived rather than written out twice.
 */
export const contactPhoneHref = `tel:${contactDetails.phone.replace(/[^\d+]/g, '')}`;
