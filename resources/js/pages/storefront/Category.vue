<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { SlidersHorizontal } from '@lucide/vue';
import { computed, ref } from 'vue';
import CategoryFilters from '@/components/storefront/CategoryFilters.vue';
import ProductCard from '@/components/storefront/ProductCard.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { categoryHref } from '@/lib/storefrontNav';
import { home } from '@/routes';
import type {
    CategoryFilterState,
    CategoryPriceBounds,
    ProductFacet,
    StorefrontCategory,
    StorefrontProduct,
} from '@/types';

type Props = {
    category: StorefrontCategory;
    products: StorefrontProduct[];
    filters: CategoryFilterState;
    brands: ProductFacet[];
    deviceFamilies: ProductFacet[];
    caseTypes: ProductFacet[];
    priceBounds: CategoryPriceBounds;
};

const props = defineProps<Props>();

const isFilterSheetOpen = ref(false);

const productCountLabel = computed(() =>
    props.products.length === 1
        ? '1 продукт'
        : `${props.products.length} продукта`,
);

const activeFilterCount = computed(() => {
    const {
        q,
        brands,
        devices,
        types,
        minPrice,
        maxPrice,
        onSale,
        isNew,
        sort,
    } = props.filters;

    return [
        Boolean(q),
        brands.length > 0,
        devices.length > 0,
        types.length > 0,
        minPrice !== null,
        maxPrice !== null,
        onSale,
        isNew,
        sort !== 'default',
    ].filter(Boolean).length;
});
</script>

<template>
    <Head :title="category.name" />

    <StorefrontContainer class="py-6 sm:py-8">
        <nav
            class="flex flex-wrap items-center gap-2 text-xs text-muted-foreground"
            aria-label="Навигация"
        >
            <Link :href="home()" class="transition-colors hover:text-primary">
                Начало
            </Link>
            <span aria-hidden="true">/</span>
            <!-- A child names its department on the way past, so there is a step
                 back up rather than only a jump to the top. -->
            <template v-if="category.parent">
                <Link
                    :href="categoryHref(category.parent.slug)"
                    class="transition-colors hover:text-primary"
                >
                    {{ category.parent.name }}
                </Link>
                <span aria-hidden="true">/</span>
            </template>
            <span class="text-foreground">{{ category.name }}</span>
        </nav>

        <header class="mt-4 mb-8">
            <h1 class="text-2xl font-bold text-foreground sm:text-3xl">
                {{ category.name }}
            </h1>
            <p
                v-if="category.tagline"
                class="mt-2 text-sm text-muted-foreground"
            >
                {{ category.tagline }}
            </p>

            <!--
                A department's children, as chips. This is the only way into a
                subcategory: the header's nav row carries departments alone,
                because eleven of them plus their children would be a menu
                rather than a row.
            -->
            <ul
                v-if="category.children?.length"
                class="mt-5 flex flex-wrap gap-2"
            >
                <li v-for="child in category.children" :key="child.slug">
                    <Link
                        :href="categoryHref(child.slug)"
                        class="inline-flex items-center gap-2 rounded-full border border-border bg-card px-4 py-2 text-sm font-semibold transition-colors hover:border-brand-accent hover:text-brand-accent-ink"
                    >
                        {{ child.name }}
                        <span
                            v-if="child.productCount !== undefined"
                            class="text-xs font-medium text-muted-foreground"
                        >
                            {{ child.productCount }}
                        </span>
                    </Link>
                </li>
            </ul>
        </header>

        <div class="lg:grid lg:grid-cols-[15rem_1fr] lg:items-start lg:gap-10">
            <!-- Sticks to the viewport so the controls stay reachable however
                 far the grid scrolls. -->
            <aside class="hidden lg:sticky lg:top-24 lg:block">
                <CategoryFilters
                    :key="category.slug"
                    :category-slug="category.slug"
                    :filters="filters"
                    :brands="brands"
                    :device-families="deviceFamilies"
                    :case-types="caseTypes"
                    :price-bounds="priceBounds"
                />
            </aside>

            <div class="min-w-0">
                <div
                    class="mb-5 flex items-center justify-between gap-3 border-b border-border pb-4"
                >
                    <p class="text-sm text-muted-foreground">
                        {{ productCountLabel }}
                    </p>

                    <Sheet v-model:open="isFilterSheetOpen">
                        <SheetTrigger as-child>
                            <Button
                                variant="outline"
                                size="sm"
                                class="gap-2 lg:hidden"
                            >
                                <SlidersHorizontal class="size-4" />
                                Филтри
                                <span
                                    v-if="activeFilterCount"
                                    class="flex size-5 items-center justify-center rounded-full bg-primary text-[11px] font-semibold text-primary-foreground"
                                >
                                    {{ activeFilterCount }}
                                </span>
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="left"
                            class="w-80 overflow-y-auto p-6"
                        >
                            <SheetHeader class="p-0">
                                <SheetTitle class="text-left">
                                    {{ category.name }}
                                </SheetTitle>
                            </SheetHeader>
                            <div class="mt-6">
                                <CategoryFilters
                                    :key="category.slug"
                                    :category-slug="category.slug"
                                    :filters="filters"
                                    :brands="brands"
                                    :device-families="deviceFamilies"
                                    :case-types="caseTypes"
                                    :price-bounds="priceBounds"
                                    @applied="isFilterSheetOpen = false"
                                />
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <div
                    v-if="products.length"
                    class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                >
                    <ProductCard
                        v-for="product in products"
                        :key="product.id"
                        :product="product"
                    />
                </div>
                <p
                    v-else
                    class="rounded-xl border border-border bg-brand-surface p-10 text-center text-sm text-muted-foreground"
                >
                    Няма продукти, които отговарят на избраните филтри.
                </p>
            </div>
        </div>
    </StorefrontContainer>
</template>
