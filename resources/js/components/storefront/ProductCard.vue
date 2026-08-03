<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Heart } from '@lucide/vue';
import ProductImage from '@/components/storefront/ProductImage.vue';
import { NEW_PRODUCTS_COPY } from '@/lib/demoCopy';
import { show as productShow } from '@/routes/product';
import type { StorefrontProduct } from '@/types';

type Props = {
    product: StorefrontProduct;
};

defineProps<Props>();
</script>

<template>
    <article
        class="group relative flex flex-col gap-3 rounded-[20px] border border-border bg-card p-3.5 transition-[border-color,box-shadow] hover:border-brand-accent hover:shadow-[0_10px_30px_-18px_var(--brand-ink)]"
    >
        <div class="relative">
            <ProductImage
                :src="product.imageUrl"
                :alt="product.name"
                class="aspect-square w-full rounded-[14px] p-2.5"
            />

            <!--
                The saving leads where there is one, because a struck price is
                what the design puts a badge on. A product that is new but not
                discounted still says so rather than carrying nothing.
            -->
            <span
                v-if="product.discountPercentage"
                class="absolute top-2.5 left-2.5 rounded-[7px] bg-brand-sale px-2 py-1 text-xs font-extrabold text-white"
            >
                -{{ product.discountPercentage }}%
            </span>
            <span
                v-else-if="product.isNew"
                class="absolute top-2.5 left-2.5 rounded-[7px] bg-brand-accent px-2 py-1 text-xs font-extrabold text-brand-accent-foreground"
            >
                Ново
            </span>

            <!-- z-10 keeps it above the title's stretched hit area, which
                 otherwise covers the whole card. -->
            <button
                type="button"
                class="absolute top-2.5 right-2.5 z-10 grid size-[34px] place-items-center rounded-full border border-border bg-card/90 text-muted-foreground backdrop-blur transition-colors hover:text-brand-sale"
                :aria-label="`Добави ${product.name} в любими`"
            >
                <Heart class="size-4" />
            </button>
        </div>

        <p
            v-if="product.deviceFamilyLabel"
            class="text-[11.5px] font-extrabold tracking-[0.07em] text-brand-highlight uppercase"
        >
            {{ product.deviceFamilyLabel }}
        </p>

        <!-- Held to a fixed depth so a two-line name never shunts the price row
             out of line with the cards beside it. -->
        <h3
            class="line-clamp-3 min-h-[58px] text-[14.5px] leading-snug font-bold"
        >
            <!-- The `after` pseudo-element stretches this link over the whole
                 card, so the entire tile is clickable while the accessible name
                 stays just the product name. -->
            <Link
                :href="productShow(product)"
                class="transition-colors after:absolute after:inset-0 hover:text-brand-accent-ink"
            >
                {{ product.name }}
            </Link>
        </h3>

        <div class="mt-auto flex flex-wrap items-baseline gap-2">
            <span class="text-xl font-extrabold tracking-tight">
                {{ product.price }}
            </span>
            <span
                v-if="product.compareAtPrice"
                class="text-[13.5px] text-muted-foreground line-through"
            >
                {{ product.compareAtPrice }}
            </span>
        </div>

        <!-- The euro line the changeover asks for, carrying both figures so it
             mirrors the lev row above it. -->
        <p class="-mt-1.5 text-[12.5px] text-muted-foreground">
            {{ product.priceInEur }}
            <template v-if="product.compareAtPriceInEur">
                / {{ product.compareAtPriceInEur }}
            </template>
        </p>

        <button
            type="button"
            class="relative z-10 h-[46px] w-full rounded-xl bg-brand-accent text-[14.5px] font-extrabold text-brand-accent-foreground transition-colors hover:bg-brand-accent-strong"
            :aria-label="`Добави ${product.name} в кошницата`"
        >
            {{ NEW_PRODUCTS_COPY.buyLabel }}
        </button>

        <p
            class="flex items-center gap-1.5 text-[12.5px] font-semibold text-brand-accent-ink"
        >
            <span
                class="size-[7px] rounded-full bg-brand-accent"
                aria-hidden="true"
            />
            {{ NEW_PRODUCTS_COPY.stockLabel }}
        </p>
    </article>
</template>
