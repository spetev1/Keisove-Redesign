/**
 * Shapes emitted by App\Http\Resources - keep these in step with the PHP
 * resources rather than with the database columns.
 */
export type StorefrontProduct = {
    id: number;
    name: string;
    slug: string;
    subtitle: string | null;
    imageUrl: string | null;
    price: string;
    compareAtPrice: string | null;
    /** The same two figures in euro, for the changeover line under the price. */
    priceInEur: string;
    compareAtPriceInEur: string | null;
    discountPercentage: number | null;
    isNew: boolean;
    /** The handset family printed as the card's eyebrow, where one applies. */
    deviceFamilyLabel: string | null;
    categorySlug?: string;
};

/**
 * The card shape plus the copy only the product page shows.
 */
export type StorefrontProductDetail = StorefrontProduct & {
    description: string | null;
    brandName?: string | null;
    categoryName?: string;
};

/**
 * One selectable option in a filter group, with how many products carry it.
 * `value` is the enum slug the query string uses.
 */
export type ProductFacet = {
    value: string;
    label: string;
    count: number;
};

/** A handset family the store cuts cases for, without a count behind it. */
export type DeviceBrand = {
    value: string;
    label: string;
};

export type CategorySortOption =
    'default' | 'price_asc' | 'price_desc' | 'newest';

/** Mirrors the filter array the category controller echoes back. */
export type CategoryFilterState = {
    q: string | null;
    /** Brand slugs. The one facet every department can offer. */
    brands: string[];
    /** Handset families, as DeviceFamily slugs. */
    devices: string[];
    /** Case constructions, as CaseType slugs. */
    types: string[];
    minPrice: number | null;
    maxPrice: number | null;
    onSale: boolean;
    isNew: boolean;
    sort: CategorySortOption;
};

export type CategoryPriceBounds = {
    min: number;
    max: number;
};

/**
 * The taxonomy is two levels deep. A department carries `children`; a child
 * carries `parent`. Neither is always sent - it depends on what the page needs.
 */
export type StorefrontCategory = {
    id: number;
    name: string;
    slug: string;
    tagline: string | null;
    imageUrl: string | null;
    /**
     * Only present where the controller counted it. On a department this is the
     * whole subtree, because a department holds no products of its own.
     */
    productCount?: number;
    /** Handset families stocked anywhere beneath it; empty where none apply. */
    deviceFamilies?: { value: string; label: string }[];
    children?: StorefrontCategory[];
    parent?: { name: string; slug: string } | null;
};

/**
 * One of the two cards standing beside the hero. Both are real destinations
 * with a real photograph behind them; only the label copy is written.
 */
export type HomeSpotlight = {
    /** Completes the card's written title, e.g. the handset family's name. */
    subject: string;
    href: string;
    imageUrl: string | null;
    /** The bestseller card prints the department's genuine lowest price. */
    fromPrice?: string | null;
};
