<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

type Props = {
    class?: HTMLAttributes['class'];
};

const props = defineProps<Props>();
</script>

<template>
    <div
        :class="
            cn(
                'mx-auto w-full max-w-[1400px] px-4 sm:px-6 lg:px-8',
                props.class,
            )
        "
    >
        <slot />
    </div>
</template>
