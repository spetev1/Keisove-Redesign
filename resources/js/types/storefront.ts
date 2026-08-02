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
    discountPercentage: number | null;
    isNew: boolean;
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

export type StorefrontCategory = {
    id: number;
    name: string;
    slug: string;
    tagline: string | null;
    imageUrl: string | null;
    /** Only present where the controller counted the relation. */
    productCount?: number;
    /** Handset families this department stocks; empty where none apply. */
    deviceFamilies?: { value: string; label: string }[];
};
