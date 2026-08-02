<script setup lang="ts">
import { ChevronLeft, ChevronRight } from '@lucide/vue';
import { onMounted, ref } from 'vue';
import ProductCard from '@/components/storefront/ProductCard.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import type { StorefrontProduct } from '@/types';

type Props = {
    heading: string;
    products: StorefrontProduct[];
    /** Left off where the row has no page of its own to send anyone to. */
    viewAllHref?: string;
};

defineProps<Props>();

const track = ref<HTMLElement | null>(null);
const canScrollBack = ref(false);
const canScrollForward = ref(false);

/**
 * The arrows only earn their place when there is actually something off-screen
 * in that direction, so an under-filled carousel shows none at all.
 */
function syncScrollAffordances(): void {
    const element = track.value;

    if (!element) {
        return;
    }

    const maxScrollLeft = element.scrollWidth - element.clientWidth;

    canScrollBack.value = element.scrollLeft > 8;
    canScrollForward.value = element.scrollLeft < maxScrollLeft - 8;
}

function scrollByPage(direction: 1 | -1): void {
    track.value?.scrollBy({
        left: direction * (track.value.clientWidth * 0.8),
        behavior: 'smooth',
    });
}

onMounted(syncScrollAffordances);
</script>

<template>
    <!--
        `min-w-0` is load-bearing. As a grid item this section's automatic
        minimum size is its content width, so without it the card strip widens
        the column to fit every card instead of scrolling, and takes the whole
        page into horizontal overflow with it.
    -->
    <section class="flex min-w-0 flex-col">
        <div class="mb-4 flex items-baseline justify-between gap-4">
            <h2 class="text-lg font-bold text-foreground">{{ heading }}</h2>
            <StorefrontNavLink
                v-if="viewAllHref"
                :href="viewAllHref"
                class="text-sm font-medium text-primary transition-colors hover:underline"
            >
                Виж всички
            </StorefrontNavLink>
        </div>

        <div class="relative">
            <div
                ref="track"
                class="scrollbar-none flex snap-x snap-mandatory gap-3 overflow-x-auto scroll-smooth pb-1"
                @scroll="syncScrollAffordances"
            >
                <ProductCard
                    v-for="product in products"
                    :key="product.id"
                    :product="product"
                    class="w-[46%] shrink-0 snap-start sm:w-[31%] lg:w-[23.5%] xl:w-[19%]"
                />
            </div>

            <button
                v-show="canScrollBack"
                type="button"
                class="absolute top-1/2 -left-3 flex size-8 -translate-y-1/2 items-center justify-center rounded-full border border-border bg-background text-foreground shadow-md transition-colors hover:bg-accent"
                aria-label="Предишни продукти"
                @click="scrollByPage(-1)"
            >
                <ChevronLeft class="size-4" />
            </button>
            <button
                v-show="canScrollForward"
                type="button"
                class="absolute top-1/2 -right-3 flex size-8 -translate-y-1/2 items-center justify-center rounded-full border border-border bg-background text-foreground shadow-md transition-colors hover:bg-accent"
                aria-label="Следващи продукти"
                @click="scrollByPage(1)"
            >
                <ChevronRight class="size-4" />
            </button>
        </div>
    </section>
</template>
