<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import { categoryHref } from '@/lib/storefrontNav';
import { promotions } from '@/routes';
import type { StorefrontCategory, StorefrontProduct } from '@/types';

type Props = {
    categories: StorefrontCategory[];
    /** Used to borrow a representative shot for the handset tiles. */
    caseProducts: StorefrontProduct[];
};

const props = defineProps<Props>();

function categoryImage(slug: string): string | null {
    return props.categories.find((c) => c.slug === slug)?.imageUrl ?? null;
}

/**
 * The handset tiles are filtered views rather than departments, so they take
 * their artwork from the first matching product instead of a category record.
 */
function caseImageFor(term: string): string | null {
    const match = props.caseProducts.find((product) =>
        `${product.name} ${product.subtitle ?? ''}`
            .toLowerCase()
            .includes(term.toLowerCase()),
    );

    return match?.imageUrl ?? null;
}

// The iPhone tile carries the lifestyle photograph, so only Samsung borrows a
// product shot.
const samsungImage = computed(() => caseImageFor('Samsung'));

const smallTileClasses =
    'group relative flex min-h-[180px] flex-col justify-between overflow-hidden rounded-2xl p-5 transition-shadow duration-300 hover:shadow-lg lg:min-h-[212px]';
</script>

<template>
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!--
            The feature tile keeps the lifestyle photograph the single hero used
            to carry, so the grid still opens on something atmospheric rather
            than five cut-outs on flat colour.
        -->
        <Link
            :href="categoryHref('keisove', { q: 'iPhone' })"
            class="group relative flex min-h-[280px] flex-col justify-end overflow-hidden rounded-2xl bg-brand-ink p-6 text-brand-ink-foreground sm:col-span-2 sm:min-h-[340px] lg:row-span-2 lg:min-h-[440px] lg:p-8"
        >
            <img
                src="/images/hero/home-hero.webp"
                alt=""
                fetchpriority="high"
                aria-hidden="true"
                class="absolute inset-0 size-full object-cover object-bottom transition-transform duration-700 ease-out group-hover:scale-105 motion-reduce:transition-none"
            />
            <!-- The photograph runs bright at the bottom, where the copy sits. -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/40 to-transparent"
            />

            <div class="relative">
                <span
                    class="inline-flex rounded-md bg-white/15 px-3 py-1 text-[11px] font-semibold tracking-wide uppercase backdrop-blur"
                >
                    Нова колекция
                </span>
                <h2
                    class="mt-4 text-3xl leading-[1.1] font-bold tracking-tight sm:text-4xl"
                >
                    Кейсове за
                    <span class="text-brand-highlight">iPhone</span>
                </h2>
                <p class="mt-2 max-w-sm text-sm text-white/70">
                    Силикон, кожа и удароустойчиви модели за всяко поколение.
                </p>
                <span
                    class="mt-5 inline-flex items-center gap-2 text-sm font-semibold"
                >
                    Разгледай
                    <ArrowRight
                        class="size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
                    />
                </span>
            </div>
        </Link>

        <Link
            :href="categoryHref('keisove', { q: 'Samsung' })"
            :class="[smallTileClasses, 'bg-brand-surface']"
        >
            <div class="relative z-10 max-w-[60%]">
                <h2 class="text-lg font-bold text-foreground">
                    Кейсове за Samsung
                </h2>
                <p class="mt-1 text-xs text-muted-foreground">
                    Galaxy S и A серия
                </p>
            </div>
            <span
                class="relative z-10 inline-flex items-center gap-2 text-sm font-semibold text-primary"
            >
                Разгледай
                <ArrowRight
                    class="size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>
            <img
                v-if="samsungImage"
                :src="samsungImage"
                alt=""
                aria-hidden="true"
                loading="lazy"
                class="absolute -right-3 -bottom-3 size-28 object-contain transition-transform duration-500 ease-out group-hover:scale-110 motion-reduce:transition-none lg:size-32"
            />
        </Link>

        <!--
            Every struck price in the catalogue is a half-price promotion, so
            the tile can state the number outright.
        -->
        <Link
            :href="promotions()"
            :class="[smallTileClasses, 'bg-primary text-primary-foreground']"
        >
            <div class="relative z-10">
                <h2 class="text-lg font-bold">На промоция</h2>
                <p class="mt-1 text-xs text-primary-foreground/75">
                    Избрани модели с намаление
                </p>
            </div>
            <span
                class="relative z-10 inline-flex items-center gap-2 text-sm font-semibold"
            >
                Виж всички
                <ArrowRight
                    class="size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>
            <span
                aria-hidden="true"
                class="pointer-events-none absolute -right-4 -bottom-6 text-[7rem] leading-none font-black text-primary-foreground/15 transition-transform duration-500 ease-out group-hover:scale-110 motion-reduce:transition-none"
            >
                -50%
            </span>
        </Link>

        <Link
            :href="categoryHref('parfyumi')"
            :class="[smallTileClasses, 'bg-secondary']"
        >
            <div class="relative z-10 max-w-[60%]">
                <h2 class="text-lg font-bold text-secondary-foreground">
                    Парфюми
                </h2>
                <p class="mt-1 text-xs text-muted-foreground">
                    Арабски и нишови аромати
                </p>
            </div>
            <span
                class="relative z-10 inline-flex items-center gap-2 text-sm font-semibold text-primary"
            >
                Разгледай
                <ArrowRight
                    class="size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>
            <img
                v-if="categoryImage('parfyumi')"
                :src="categoryImage('parfyumi') ?? undefined"
                alt=""
                aria-hidden="true"
                loading="lazy"
                class="absolute -right-2 -bottom-2 size-28 object-contain transition-transform duration-500 ease-out group-hover:scale-110 motion-reduce:transition-none lg:size-32"
            />
        </Link>

        <Link
            :href="categoryHref('aksesoari')"
            :class="[smallTileClasses, 'border border-border bg-card']"
        >
            <div class="relative z-10 max-w-[60%]">
                <h2 class="text-lg font-bold text-card-foreground">
                    Аксесоари
                </h2>
                <p class="mt-1 text-xs text-muted-foreground">
                    Зарядни, батерии и звук
                </p>
            </div>
            <span
                class="relative z-10 inline-flex items-center gap-2 text-sm font-semibold text-primary"
            >
                Разгледай
                <ArrowRight
                    class="size-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>
            <img
                v-if="categoryImage('aksesoari')"
                :src="categoryImage('aksesoari') ?? undefined"
                alt=""
                aria-hidden="true"
                loading="lazy"
                class="absolute -right-3 -bottom-3 size-28 object-contain transition-transform duration-500 ease-out group-hover:scale-110 motion-reduce:transition-none lg:size-32"
            />
        </Link>
    </section>
</template>
