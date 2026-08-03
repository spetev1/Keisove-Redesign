<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { StorefrontCategory } from '@/types';

type Props = {
    category: StorefrontCategory;
    href: string;
    /**
     * One tile in the row is filled with the brand violet. It is what stops a
     * row of identical white tiles reading as a table, and it goes to whichever
     * department the page is leading on.
     */
    isFeatured?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    isFeatured: false,
});

/**
 * The genuine count from the database. Bulgarian takes the plural from two up,
 * so a department down to its last product still reads correctly.
 */
const countLabel = computed(() => {
    const count = props.category.productCount;

    if (count === undefined) {
        return props.category.tagline ?? '';
    }

    return count === 1 ? '1 продукт' : `${count} продукта`;
});
</script>

<template>
    <!--
        The link is the tile's fixed footprint and the card inside it is what
        lifts. Putting the lift on the link itself made it flicker: hovering the
        bottom edge raised the element out from under the pointer, which ended
        the hover, which dropped it back under the pointer, over and over. The
        hover target has to hold still for the hover to be stable, so only its
        contents move.
    -->
    <Link
        :href="href"
        class="group block h-full rounded-[18px] focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
    >
        <span
            :class="[
                'flex h-full min-h-[150px] flex-col justify-between gap-3.5 rounded-[18px] border p-4.5 transition-[transform,border-color,background-color] duration-300 ease-out group-hover:-translate-y-0.5 motion-reduce:transition-none',
                props.isFeatured
                    ? 'border-brand-ink bg-brand-ink text-brand-ink-foreground group-hover:bg-brand-ink-hover'
                    : 'border-border bg-card group-hover:border-brand-accent',
            ]"
        >
            <span
                :class="[
                    'block size-13 overflow-hidden rounded-[14px]',
                    props.isFeatured ? 'bg-white/15' : 'bg-brand-surface',
                ]"
            >
                <img
                    v-if="category.imageUrl"
                    :src="category.imageUrl"
                    alt=""
                    aria-hidden="true"
                    loading="lazy"
                    class="size-full object-cover"
                />
            </span>

            <span>
                <b class="block text-[15px] tracking-tight">
                    {{ category.name }}
                </b>
                <span
                    :class="[
                        'text-[13px]',
                        props.isFeatured
                            ? 'text-brand-ink-soft'
                            : 'text-muted-foreground',
                    ]"
                >
                    {{ countLabel }}
                </span>
            </span>
        </span>
    </Link>
</template>
