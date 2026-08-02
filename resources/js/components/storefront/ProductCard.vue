<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Heart, ShoppingCart } from '@lucide/vue';
import ProductImage from '@/components/storefront/ProductImage.vue';
import { show as productShow } from '@/routes/product';
import type { StorefrontProduct } from '@/types';

type Props = {
    product: StorefrontProduct;
};

defineProps<Props>();
</script>

<template>
    <article
        class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-md"
    >
        <div class="relative">
            <ProductImage
                :src="product.imageUrl"
                :alt="product.name"
                class="aspect-4/5 w-full"
            />

            <span
                v-if="product.isNew"
                class="absolute top-2 left-2 rounded-md bg-success px-2 py-0.5 text-[10px] font-bold tracking-wide text-success-foreground uppercase"
            >
                Ново
            </span>
            <span
                v-else-if="product.discountPercentage"
                class="absolute top-2 left-2 rounded-md bg-primary px-2 py-0.5 text-[10px] font-bold text-primary-foreground"
            >
                -{{ product.discountPercentage }}%
            </span>

            <!-- z-10 keeps the icon buttons above the title's stretched hit
                 area, which otherwise covers the whole card. -->
            <button
                type="button"
                class="absolute top-2 right-2 z-10 flex size-7 items-center justify-center rounded-full bg-background/80 text-muted-foreground backdrop-blur transition-colors hover:text-primary"
                :aria-label="`Добави ${product.name} в любими`"
            >
                <Heart class="size-4" />
            </button>
        </div>

        <div class="flex flex-1 flex-col gap-1 p-3">
            <!-- Clamped so a two-line name never shunts the price row out of
                 line with the cards beside it. -->
            <h3
                class="line-clamp-2 text-sm leading-snug font-semibold text-card-foreground"
            >
                <!-- The `after` pseudo-element stretches this link over the
                     whole card, so the entire tile is clickable while the
                     accessible name stays just the product name. -->
                <Link
                    :href="productShow(product)"
                    class="transition-colors after:absolute after:inset-0 hover:text-primary"
                >
                    {{ product.name }}
                </Link>
            </h3>
            <p v-if="product.subtitle" class="text-xs text-muted-foreground">
                {{ product.subtitle }}
            </p>

            <div class="mt-auto flex items-end justify-between gap-2 pt-3">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-card-foreground">
                        {{ product.price }}
                    </span>
                    <span
                        v-if="product.compareAtPrice"
                        class="text-xs text-muted-foreground line-through"
                    >
                        {{ product.compareAtPrice }}
                    </span>
                </div>

                <button
                    type="button"
                    class="relative z-10 flex size-8 shrink-0 items-center justify-center bg-secondary text-primary transition-colors hover:bg-primary hover:text-primary-foreground"
                    :aria-label="`Добави ${product.name} в кошницата`"
                >
                    <ShoppingCart class="size-4" />
                </button>
            </div>
        </div>
    </article>
</template>
