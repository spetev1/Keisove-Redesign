<script setup lang="ts">
import { computed } from 'vue';
import HeroSpotlightCard from '@/components/storefront/HeroSpotlightCard.vue';
import HomeHero from '@/components/storefront/HomeHero.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { SPOTLIGHT_COPY } from '@/lib/demoCopy';
import type { HomeSpotlight } from '@/types';

type Props = {
    collage: string[];
    spotlights: {
        newArrivals: HomeSpotlight;
        bestseller: HomeSpotlight;
    };
    promotionsHref: string;
    brandsHref: string;
};

const props = defineProps<Props>();

/**
 * "Аксесоари за" and then whichever handset the store has most recently added
 * cases for, so the card names something that genuinely just landed.
 */
const newArrivalsTitle = computed(() =>
    [
        SPOTLIGHT_COPY.newArrivals.titlePrefix,
        props.spotlights.newArrivals.subject,
    ].filter(Boolean),
);

/**
 * The department, then its genuine cheapest bottle. A department with nothing
 * priced in it drops the second line rather than printing "от" and no figure.
 */
const bestsellerTitle = computed(() => {
    const { subject, fromPrice } = props.spotlights.bestseller;

    return [
        subject,
        fromPrice ? `${SPOTLIGHT_COPY.bestseller.fromLabel} ${fromPrice}` : '',
    ].filter(Boolean);
});
</script>

<template>
    <StorefrontContainer class="pt-5 sm:pt-7 lg:pt-8">
        <!--
            Three columns with the hero across two of them. The cards stack in
            the third and stretch to meet the hero's height, so the row closes on
            one line rather than leaving the hero taller than the column beside
            it.
        -->
        <div class="grid items-stretch gap-4 lg:grid-cols-3">
            <HomeHero
                :collage="collage"
                :promotions-href="promotionsHref"
                :brands-href="brandsHref"
                class="lg:col-span-2"
            />

            <div class="flex flex-col gap-4">
                <HeroSpotlightCard
                    :eyebrow="SPOTLIGHT_COPY.newArrivals.eyebrow"
                    :title-lines="newArrivalsTitle"
                    :cta="SPOTLIGHT_COPY.newArrivals.cta"
                    :href="spotlights.newArrivals.href"
                    :image-url="spotlights.newArrivals.imageUrl"
                />
                <HeroSpotlightCard
                    :eyebrow="SPOTLIGHT_COPY.bestseller.eyebrow"
                    :title-lines="bestsellerTitle"
                    :cta="SPOTLIGHT_COPY.bestseller.cta"
                    :href="spotlights.bestseller.href"
                    :image-url="spotlights.bestseller.imageUrl"
                    tone="accent"
                />
            </div>
        </div>
    </StorefrontContainer>
</template>
