<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    href: string;
};

const props = defineProps<Props>();

/**
 * `#` marks a destination the demo does not build yet, and a link carrying a
 * hash is asking the browser to move to a section rather than to fetch a page.
 * Routing either through Inertia would fire a visit that lands back where it
 * started, so both stay plain anchors and every real page gets a client-side
 * visit.
 */
const isPlaceholder = computed(() => props.href.includes('#'));
</script>

<template>
    <a v-if="isPlaceholder" :href="href">
        <slot />
    </a>
    <Link v-else :href="href">
        <slot />
    </Link>
</template>
