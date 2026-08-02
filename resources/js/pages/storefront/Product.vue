<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Check, Heart, Minus, Plus, ShoppingCart, Truck } from '@lucide/vue';
import { computed, ref } from 'vue';
import ProductCarousel from '@/components/storefront/ProductCarousel.vue';
import ProductImage from '@/components/storefront/ProductImage.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { Button } from '@/components/ui/button';
import { categoryHref } from '@/lib/storefrontNav';
import { home } from '@/routes';
import type { StorefrontProduct, StorefrontProductDetail } from '@/types';

type Props = {
    product: StorefrontProductDetail;
    relatedProducts: StorefrontProduct[];
};

const props = defineProps<Props>();

/**
 * A product carries one photograph today. The rail is written against a list
 * regardless, so the day products have galleries it is a change to where the
 * pictures come from rather than to this page.
 */
const images = computed<string[]>(() =>
    props.product.imageUrl ? [props.product.imageUrl] : [],
);

const activeImage = ref(0);
const quantity = ref(1);

function step(by: number): void {
    quantity.value = Math.max(1, quantity.value + by);
}
</script>

<template>
    <Head :title="product.name" />

    <StorefrontContainer class="py-6 sm:py-8">
        <nav
            class="flex flex-wrap items-center gap-2 text-xs text-muted-foreground"
            aria-label="Навигация"
        >
            <Link :href="home()" class="transition-colors hover:text-primary">
                Начало
            </Link>
            <span aria-hidden="true">/</span>
            <Link
                v-if="product.categorySlug && product.categoryName"
                :href="categoryHref(product.categorySlug)"
                class="transition-colors hover:text-primary"
            >
                {{ product.categoryName }}
            </Link>
            <span v-if="product.categorySlug" aria-hidden="true">/</span>
            <span class="text-foreground">{{ product.name }}</span>
        </nav>

        <!--
            The picture is held to a fixed column rather than taking whatever is
            left: given the run of the page it grew to most of a screen, and a
            phone case does not need it.
        -->
        <div
            class="mt-6 grid gap-8 lg:grid-cols-[34rem_minmax(0,1fr)] lg:gap-12"
        >
            <div class="flex gap-4">
                <!-- The rail stands even at one picture: it is where the rest
                     go, and its absence would move the photograph on the day a
                     second one arrives. -->
                <div
                    v-if="images.length"
                    class="flex w-16 shrink-0 flex-col gap-3 sm:w-20"
                >
                    <button
                        v-for="(image, index) in images"
                        :key="image"
                        type="button"
                        class="overflow-hidden rounded-xl border transition-colors"
                        :class="
                            index === activeImage
                                ? 'border-primary'
                                : 'border-border hover:border-primary/40'
                        "
                        :aria-current="index === activeImage"
                        :aria-label="`Снимка ${index + 1}`"
                        @click="activeImage = index"
                    >
                        <ProductImage
                            :src="image"
                            :alt="product.name"
                            class="aspect-square w-full"
                        />
                    </button>
                </div>

                <div class="relative min-w-0 flex-1">
                    <ProductImage
                        :src="images[activeImage] ?? product.imageUrl"
                        :alt="product.name"
                        class="aspect-square w-full rounded-2xl"
                    />

                    <span
                        v-if="product.isNew"
                        class="absolute top-4 left-4 rounded-md bg-success px-2.5 py-1 text-xs font-bold tracking-wide text-success-foreground uppercase"
                    >
                        Ново
                    </span>
                    <span
                        v-else-if="product.discountPercentage"
                        class="absolute top-4 left-4 rounded-md bg-primary px-2.5 py-1 text-xs font-bold text-primary-foreground"
                    >
                        -{{ product.discountPercentage }}%
                    </span>
                </div>
            </div>

            <div class="flex max-w-2xl flex-col">
                <h1 class="text-2xl font-bold text-foreground sm:text-3xl">
                    {{ product.name }}
                </h1>
                <p
                    v-if="product.subtitle"
                    class="mt-2 text-sm text-muted-foreground"
                >
                    {{ product.subtitle }}
                </p>

                <hr class="mt-5 border-border" />

                <div class="mt-5 flex items-end gap-3">
                    <span class="text-3xl font-bold text-primary">
                        {{ product.price }}
                    </span>
                    <span
                        v-if="product.compareAtPrice"
                        class="pb-1 text-base text-muted-foreground line-through"
                    >
                        {{ product.compareAtPrice }}
                    </span>
                </div>

                <!--
                    Stock is not modelled yet, so the line is the design's
                    rather than the catalogue's and says the same for every
                    product. The delivery price beside it is the store's real
                    one. Both come right the day stock lands on the product.
                -->
                <div
                    class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm"
                >
                    <span class="inline-flex items-center gap-2 text-success">
                        <Check class="size-4" />
                        В наличност
                    </span>
                    <span
                        class="inline-flex items-center gap-2 text-muted-foreground"
                    >
                        <Truck class="size-4" />
                        Доставка 6.95 лв.
                    </span>
                </div>

                <!-- No basket yet: the count is real, what it would buy is not,
                     and these stay inert until the cart lands. -->
                <div class="mt-6 flex items-stretch gap-3">
                    <div
                        class="flex items-center rounded-xl border border-border"
                    >
                        <button
                            type="button"
                            class="flex size-10 items-center justify-center text-muted-foreground transition-colors hover:text-foreground"
                            aria-label="По-малко"
                            @click="step(-1)"
                        >
                            <Minus class="size-4" />
                        </button>
                        <span
                            class="w-8 text-center text-sm font-semibold tabular-nums"
                        >
                            {{ quantity }}
                        </span>
                        <button
                            type="button"
                            class="flex size-10 items-center justify-center text-muted-foreground transition-colors hover:text-foreground"
                            aria-label="Повече"
                            @click="step(1)"
                        >
                            <Plus class="size-4" />
                        </button>
                    </div>

                    <Button size="lg" class="flex-1 gap-2">
                        <ShoppingCart class="size-4" />
                        Купи
                    </Button>
                </div>

                <button
                    type="button"
                    class="mt-3 flex items-center gap-3 rounded-xl border border-border px-4 py-3 text-left transition-colors hover:border-primary/40 hover:bg-brand-surface"
                >
                    <Heart class="size-5 shrink-0 text-primary" />
                    <span class="flex flex-col">
                        <span class="text-sm font-semibold text-foreground">
                            Добави в любими
                        </span>
                        <span class="text-xs text-muted-foreground">
                            Твоите любими продукти
                        </span>
                    </span>
                </button>

                <template v-if="product.brandName">
                    <hr class="mt-6 border-border" />
                    <p
                        class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-foreground"
                    >
                        <Check class="size-4 text-primary" />
                        {{ product.brandName }}
                    </p>
                </template>
            </div>
        </div>

        <!--
            The copy runs the width of the page under both columns rather than
            sharing one of them: it is a spec list, so it reads across in short
            lines instead of stacking into a narrow ribbon, and beside the price
            it pushed the buying controls down the column.
        -->
        <div v-if="product.description" class="mt-14">
            <h2 class="text-base font-bold text-foreground">Описание</h2>
            <p
                class="mt-3 text-sm leading-relaxed whitespace-pre-line text-muted-foreground"
            >
                {{ product.description }}
            </p>
        </div>

        <div v-if="relatedProducts.length" class="mt-16">
            <ProductCarousel
                heading="Подобни продукти"
                :products="relatedProducts"
                :view-all-href="
                    product.categorySlug
                        ? categoryHref(product.categorySlug)
                        : undefined
                "
            />
        </div>
    </StorefrontContainer>
</template>
