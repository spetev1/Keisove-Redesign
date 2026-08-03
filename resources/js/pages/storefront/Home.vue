<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { computed, ref } from 'vue';
import CategoryTile from '@/components/storefront/CategoryTile.vue';
import DeviceBrandStrip from '@/components/storefront/DeviceBrandStrip.vue';
import HomeHeroGrid from '@/components/storefront/HomeHeroGrid.vue';
import ProductCard from '@/components/storefront/ProductCard.vue';
import PromoCountdown from '@/components/storefront/PromoCountdown.vue';
import SectionHeading from '@/components/storefront/SectionHeading.vue';
import ServicePromises from '@/components/storefront/ServicePromises.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import Testimonials from '@/components/storefront/Testimonials.vue';
import { CATEGORIES_COPY, NEW_PRODUCTS_COPY } from '@/lib/demoCopy';
import { SITE_TAGLINE } from '@/lib/siteMeta';
import { categoryHref, deviceFilterHref } from '@/lib/storefrontNav';
import { promotions as promotionsPage } from '@/routes';
import type {
    DeviceBrand,
    HomeSpotlight,
    StorefrontCategory,
    StorefrontProduct,
} from '@/types';

type Props = {
    /** The grid's tiles, already selected and ordered by the controller. */
    featuredCategories: StorefrontCategory[];
    deviceFamilies: DeviceBrand[];
    newProducts: StorefrontProduct[];
    heroCollage: string[];
    spotlights: {
        newArrivals: HomeSpotlight;
        bestseller: HomeSpotlight;
    };
    saleEndsAt: string;
};

const props = defineProps<Props>();

/**
 * The case department the brand panel narrows by handset, and that the
 * new-products link narrows to what has just landed. The whole department, so
 * picking a handset reaches every construction of case rather than one shelf.
 */
const CASES_DEPARTMENT = 'kalafi';

/**
 * Which tile in the category row is filled with the brand violet. It is the
 * perfumes in the design too - the one department that is not a phone accessory,
 * and so the one worth pulling out of a row of them.
 */
const FEATURED_DEPARTMENT = 'parfyumi';

const page = usePage();

/**
 * Departments are shared on every page for the header's nav row, counts and all,
 * so the grid reads them from there rather than having them passed again.
 */
const departments = computed(() => page.props.storefrontCategories ?? []);

/**
 * Departments plus their subcategories. The grid shows twelve of them; this is
 * what the link beside the heading is offering, so it counts the whole taxonomy
 * rather than the row.
 */
const categoryCount = computed(() =>
    departments.value.reduce(
        (total, department) => total + 1 + (department.children?.length ?? 0),
        0,
    ),
);

/** `null` is the "Всички" pill - no department applied. */
const activeDepartment = ref<string | null>(null);

/**
 * A product names the leaf it sits on - "Карти памет", "Външни батерии" - but
 * the pills are departments, so the row needs a way from one to the other. A
 * department with no children maps to itself.
 */
const departmentOfLeaf = computed(() => {
    const index = new Map<string, string>();

    for (const department of departments.value) {
        index.set(department.slug, department.slug);

        for (const child of department.children ?? []) {
            index.set(child.slug, department.slug);
        }
    }

    return index;
});

function departmentOf(product: StorefrontProduct): string | undefined {
    return product.categorySlug
        ? departmentOfLeaf.value.get(product.categorySlug)
        : undefined;
}

/**
 * Only the departments that actually have something in the row get a pill. A
 * filter that empties the grid whatever is clicked is worse than no filter.
 */
const departmentPills = computed(() => {
    const present = new Set(
        props.newProducts
            .map(departmentOf)
            .filter((slug): slug is string => slug !== undefined),
    );

    return departments.value.filter((department) =>
        present.has(department.slug),
    );
});

const visibleProducts = computed(() =>
    activeDepartment.value === null
        ? props.newProducts
        : props.newProducts.filter(
              (product) => departmentOf(product) === activeDepartment.value,
          ),
);

const pillClasses =
    'h-10 shrink-0 rounded-[10px] px-4 text-sm transition-colors';

function pillState(slug: string | null): string {
    return activeDepartment.value === slug
        ? 'bg-brand-ink font-bold text-brand-ink-foreground'
        : 'border border-input bg-card font-semibold text-secondary-foreground hover:border-brand-accent';
}
</script>

<template>
    <!-- The homepage has no page name to give, so the tagline takes that place
         and the template appends the site as it does everywhere else. -->
    <Head :title="SITE_TAGLINE" />

    <!--
        The order is the design's, and it is an argument: what is running out
        (the countdown), what is on offer (the hero), why buy here (the
        promises), then the catalogue by department and by handset, and reviews
        last.
    -->
    <PromoCountdown :ends-at="saleEndsAt" />

    <HomeHeroGrid
        :collage="heroCollage"
        :spotlights="spotlights"
        :promotions-href="promotionsPage.url()"
        brands-href="#marki"
    />

    <ServicePromises />

    <!-- `id` is what the header's leading nav tab opens. -->
    <StorefrontContainer id="kategorii" class="pt-9 sm:pt-12 lg:pt-14">
        <SectionHeading
            :heading="CATEGORIES_COPY.heading"
            :subheading="CATEGORIES_COPY.subheading"
        >
            <template #trailing>
                <!--
                    The demo has no page listing every department, so this is
                    marked as a destination still to be built rather than
                    pointed at something that is not one.
                -->
                <StorefrontNavLink
                    href="#"
                    class="inline-flex items-center gap-1.5 text-[14.5px] font-bold text-brand-highlight transition-colors hover:text-brand-ink"
                >
                    {{ CATEGORIES_COPY.allCategoriesPrefix }}
                    {{ categoryCount }}
                    {{ CATEGORIES_COPY.allCategoriesSuffix }}
                    <ArrowRight class="size-4" />
                </StorefrontNavLink>
            </template>
        </SectionHeading>

        <!--
            Seven across from `xl`, which is what puts the eighth tile - the
            perfumes - at the start of the second row, bottom left, where the
            design draws the violet one.
        -->
        <div
            class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-7"
        >
            <CategoryTile
                v-for="category in featuredCategories"
                :key="category.slug"
                :category="category"
                :href="categoryHref(category.slug)"
                :is-featured="category.slug === FEATURED_DEPARTMENT"
            />
        </div>
    </StorefrontContainer>

    <StorefrontContainer id="novi" class="pt-9 sm:pt-12 lg:pt-14">
        <SectionHeading
            :heading="NEW_PRODUCTS_COPY.heading"
            :subheading="NEW_PRODUCTS_COPY.subheading"
        >
            <template #trailing>
                <!--
                    Filtered here rather than by a visit: the row is already on
                    the page in full, so narrowing it is a matter of hiding
                    cards, not of asking the server for them again.
                -->
                <div
                    v-if="departmentPills.length > 1"
                    class="scrollbar-none flex max-w-full gap-2 overflow-x-auto"
                >
                    <button
                        type="button"
                        :class="[pillClasses, pillState(null)]"
                        @click="activeDepartment = null"
                    >
                        {{ NEW_PRODUCTS_COPY.allFilterLabel }}
                    </button>
                    <button
                        v-for="department in departmentPills"
                        :key="department.slug"
                        type="button"
                        :class="[pillClasses, pillState(department.slug)]"
                        @click="activeDepartment = department.slug"
                    >
                        {{ department.name }}
                    </button>
                </div>
            </template>
        </SectionHeading>

        <div
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
        >
            <ProductCard
                v-for="product in visibleProducts"
                :key="product.id"
                :product="product"
            />
        </div>

        <div class="mt-6 flex justify-center">
            <!--
                Points at the cases department narrowed to what is new, which is
                a real view of real rows. A single page gathering new arrivals
                across every department is still to be built.
            -->
            <StorefrontNavLink
                :href="categoryHref(CASES_DEPARTMENT, { new: '1' })"
                class="inline-flex h-13 items-center rounded-[13px] border-[1.5px] border-brand-ink px-7 text-[15.5px] font-extrabold text-brand-ink transition-colors hover:bg-brand-ink hover:text-brand-ink-foreground"
            >
                {{ NEW_PRODUCTS_COPY.allProductsLabel }}
            </StorefrontNavLink>
        </div>
    </StorefrontContainer>

    <DeviceBrandStrip
        :families="deviceFamilies"
        :all-href="categoryHref(CASES_DEPARTMENT)"
        :href-for="(family) => deviceFilterHref(CASES_DEPARTMENT, family.value)"
    />

    <Testimonials />

    <!-- The footer's own top margin closes the page, so the last section does
         not need one of its own. -->
</template>
