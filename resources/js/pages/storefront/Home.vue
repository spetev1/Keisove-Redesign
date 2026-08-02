<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import CategoryTile from '@/components/storefront/CategoryTile.vue';
import DeviceBrandStrip from '@/components/storefront/DeviceBrandStrip.vue';
import HomeHero from '@/components/storefront/HomeHero.vue';
import ProductCarousel from '@/components/storefront/ProductCarousel.vue';
import ServicePromises from '@/components/storefront/ServicePromises.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { SITE_TAGLINE } from '@/lib/siteMeta';
import { categoryHref } from '@/lib/storefrontNav';
import { promotions as promotionsPage } from '@/routes';
import type {
    DeviceBrand,
    StorefrontCategory,
    StorefrontProduct,
} from '@/types';

type Props = {
    categories: StorefrontCategory[];
    deviceFamilies: DeviceBrand[];
    promotions: StorefrontProduct[];
    accessories: StorefrontProduct[];
};

const props = defineProps<Props>();

/**
 * The departments the homepage leads on, in the order the design sets them out.
 * Anything else in the catalogue lives behind the nav rather than here.
 */
const FEATURED_SLUGS = ['keisove', 'protektori', 'parfyumi'];

const featuredCategories = computed(() =>
    FEATURED_SLUGS.map((slug) =>
        props.categories.find((category) => category.slug === slug),
    ).filter(
        (category): category is StorefrontCategory => category !== undefined,
    ),
);
</script>

<template>
    <!-- The homepage has no page name to give, so the tagline takes that place
         and the template appends the site as it does everywhere else. -->
    <Head :title="SITE_TAGLINE" />

    <!-- The hero runs full bleed, so it sits outside the content container and
         aligns its own copy from the inside. The brand strip carries the same
         ink and sits flush under it, reading as the foot of the hero. -->
    <HomeHero />
    <!--
        Lifted into the foot of the hero rather than sitting under it. It needs
        to be positioned to do that: the hero's photograph is absolute, and an
        unpositioned band would be painted over by it however late it comes in
        the markup.

        The lift is what fixes where the band's *bottom* edge lands, since the
        hero above it is a fixed height: pull up by more and the whole band, foot
        included, rises with it. So the figures are not free - each is the lift
        its width needs for that edge to land where the design puts it, and
        slimming the band means giving the same number of pixels back here.

        A phone gets no lift at all. The band is taller there, because the label
        stacks above the row rather than sitting beside it, and the same overlap
        that reads as a seam on a wide screen ate the foot of the case on a
        narrow one. Flush is as low as it goes: both bands carry the same ink and
        only read as one because of it, so a positive margin would open a white
        line between them.
    -->
    <DeviceBrandStrip
        :families="deviceFamilies"
        label="Кейсове за всеки телефон"
        class="relative sm:-mt-[10px] lg:-mt-[18px]"
    />

    <StorefrontContainer class="py-6">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <CategoryTile
                v-for="category in featuredCategories"
                :key="category.slug"
                :category="category"
                cta-label="Разгледай"
                :href="categoryHref(category.slug)"
            />
        </div>

        <!--
            One row to a subject, each the full width of the page: what is on
            offer, and the department that sells alongside everything else. Both
            have a page of their own to open, so neither row ends on a heading
            that leads nowhere.
        -->
        <div class="mt-12 flex flex-col gap-12">
            <ProductCarousel
                heading="Промоции"
                :products="promotions"
                :view-all-href="promotionsPage.url()"
            />
            <ProductCarousel
                heading="Аксесоари"
                :products="accessories"
                :view-all-href="categoryHref('aksesoari')"
            />
        </div>
    </StorefrontContainer>

    <!--
        Runs full bleed like the brand strip, so it sits outside the content
        container rather than inside it. The margin is its own rather than the
        container's, which stops at the last carousel.
    -->
    <ServicePromises class="mt-10" />
</template>
