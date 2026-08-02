<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

type Props = {
    class?: HTMLAttributes['class'];
};

const props = defineProps<Props>();
</script>

<template>
    <!--
        The client's own logo. Nothing else in the storefront renders the mark,
        so a replacement asset is a change to this file alone.

        Callers size it by height and let the width follow, and the intrinsic
        dimensions ride on the tag so the header holds the space open rather
        than reflowing once the file lands.
    -->
    <img
        src="/images/brand/keisove-logo.png"
        alt="Keisove"
        width="399"
        height="199"
        :class="cn('block h-8 w-auto select-none', props.class)"
    />
</template>
