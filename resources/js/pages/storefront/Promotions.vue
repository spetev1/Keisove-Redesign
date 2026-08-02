<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProductCard from '@/components/storefront/ProductCard.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { home } from '@/routes';
import type { StorefrontProduct } from '@/types';

type Props = {
    products: StorefrontProduct[];
};

const props = defineProps<Props>();

const productCountLabel = computed(() =>
    props.products.length === 1
        ? '1 продукт'
        : `${props.products.length} продукта`,
);
</script>

<template>
    <Head title="Промоции" />

    <StorefrontContainer class="py-6 sm:py-8">
        <nav
            class="flex items-center gap-2 text-xs text-muted-foreground"
            aria-label="Навигация"
        >
            <Link :href="home()" class="transition-colors hover:text-primary">
                Начало
            </Link>
            <span aria-hidden="true">/</span>
            <span class="text-foreground">Промоции</span>
        </nav>

        <header class="mt-4 mb-8">
            <h1 class="text-2xl font-bold text-foreground sm:text-3xl">
                Промоции
            </h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Всичко с намалена цена, от всички категории.
            </p>
            <p class="mt-1 text-xs text-muted-foreground">
                {{ productCountLabel }}
            </p>
        </header>

        <div
            v-if="products.length"
            class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
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
            В момента няма активни промоции.
        </p>
    </StorefrontContainer>
</template>
