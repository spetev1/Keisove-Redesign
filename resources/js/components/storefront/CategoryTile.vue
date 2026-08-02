<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import type { StorefrontCategory } from '@/types';

type Props = {
    category: StorefrontCategory;
    ctaLabel: string;
    href?: string;
};

const props = withDefaults(defineProps<Props>(), {
    href: '#',
});

/**
 * `#` marks a destination the demo does not build yet; routing that through
 * Inertia would fire a visit at the current page.
 */
const cardComponent = computed(() => (props.href === '#' ? 'a' : Link));
</script>

<template>
    <!--
        The photograph is the card rather than something framed inside it, so
        the copy is laid over the picture and the whole tile is the link. That
        rules out a button for the call to action - an anchor nested in an
        anchor - so it is a span the card's own hover drives instead.
    -->
    <component
        :is="cardComponent"
        :href="href"
        class="group relative flex aspect-[4/3] items-end overflow-hidden rounded-2xl bg-brand-surface"
    >
        <img
            v-if="category.imageUrl"
            :src="category.imageUrl"
            :alt="category.name"
            loading="lazy"
            class="absolute inset-0 size-full object-cover transition-transform duration-500 ease-out group-hover:scale-105 motion-reduce:transition-none"
        />

        <!--
            Department photography is shot light, and the copy over it is white,
            so the foot of the card is carried down far enough to hold a line of
            text while the top of the picture is left alone.
        -->
        <div
            class="absolute inset-0 bg-gradient-to-t from-brand-ink/90 via-brand-ink/45 via-45% to-transparent to-75%"
            aria-hidden="true"
        />

        <div class="relative w-full p-6">
            <h2 class="text-xl font-bold text-white sm:text-2xl">
                {{ category.name }}
            </h2>
            <p
                v-if="category.tagline"
                class="mt-1 max-w-[18rem] text-sm text-white/70"
            >
                {{ category.tagline }}
            </p>
            <span
                class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-white"
            >
                {{ ctaLabel }}
                <ArrowRight
                    class="size-4 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>
        </div>
    </component>
</template>
