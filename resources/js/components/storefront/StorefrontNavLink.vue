<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    href: string;
};

const props = defineProps<Props>();

/**
 * `#` marks a destination the demo does not build yet. Routing those through
 * Inertia would fire a visit at the current page, so they stay plain anchors
 * and every real destination gets a client-side visit.
 */
const isPlaceholder = computed(() => props.href === '#');
</script>

<template>
    <a v-if="isPlaceholder" :href="href">
        <slot />
    </a>
    <Link v-else :href="href">
        <slot />
    </Link>
</template>
