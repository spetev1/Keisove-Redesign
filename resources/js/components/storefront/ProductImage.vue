<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import { cn } from '@/lib/utils';

type Props = {
    src: string | null;
    alt: string;
    class?: HTMLAttributes['class'];
};

const props = defineProps<Props>();
</script>

<template>
    <div
        :class="
            cn(
                'relative flex items-center justify-center overflow-hidden bg-brand-surface',
                props.class,
            )
        "
    >
        <img
            v-if="props.src"
            :src="props.src"
            :alt="props.alt"
            loading="lazy"
            class="size-full object-contain"
        />
        <!--
            Product photography is still outstanding. Until it lands this reads
            as a deliberate branded panel rather than a broken image.
        -->
        <BrandWordmark v-else class="h-10 opacity-25" />
    </div>
</template>
