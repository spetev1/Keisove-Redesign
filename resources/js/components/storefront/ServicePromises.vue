<script setup lang="ts">
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { SERVICE_PROMISES } from '@/lib/demoCopy';

/**
 * The four terms a shopper checks before buying, in one panel directly under the
 * hero. The glyph stands in for an icon because each of these is a figure rather
 * than a thing - "6.95 лв." and "24ч" say more than a van and a clock would.
 */
</script>

<template>
    <StorefrontContainer class="pt-4 sm:pt-5 lg:pt-6">
        <!--
            The panel's own padding is a hair rather than a gutter, so the four
            cells divide its full width and the row reads as one strip rather
            than four boxes.
        -->
        <ul
            class="grid gap-0 rounded-[18px] border border-border bg-card p-1.5 sm:grid-cols-2 lg:grid-cols-4"
        >
            <li
                v-for="promise in SERVICE_PROMISES"
                :key="promise.label"
                class="flex items-center gap-3 px-4.5 py-4"
            >
                <span
                    :class="[
                        'grid size-[34px] shrink-0 place-items-center rounded-[10px] text-[13px] font-extrabold tracking-tight',
                        promise.tone === 'accent'
                            ? 'bg-brand-accent-surface text-brand-accent-ink'
                            : 'bg-brand-ink-surface text-brand-ink-surface-foreground',
                    ]"
                    aria-hidden="true"
                >
                    {{ promise.glyph }}
                </span>
                <span class="leading-tight">
                    <b class="block text-[14.5px]">{{ promise.label }}</b>
                    <span class="text-[13px] text-muted-foreground">
                        {{ promise.detail }}
                    </span>
                </span>
            </li>
        </ul>
    </StorefrontContainer>
</template>
