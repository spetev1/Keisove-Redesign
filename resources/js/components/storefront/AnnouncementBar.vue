<script setup lang="ts">
import { useMarqueeTrack } from '@/composables/useMarqueeTrack';
import { ANNOUNCEMENTS } from '@/lib/demoCopy';

/**
 * The store's promises, travelling across the top of every page in the brand
 * violet. It scrolls away with the page rather than pinning itself: the header
 * below is the part that stays, and it shrinks as it goes, so the band a
 * shopper keeps is the one with the search field in it.
 */
const { trackRef, copies, trackStyle } = useMarqueeTrack();
</script>

<template>
    <div class="overflow-hidden bg-brand-ink text-brand-ink-foreground">
        <!--
            The track carries as many copies of the messages as the screen
            needs, and slides by exactly one of them, so the loop lands on an
            identical frame with no seam and no tail. Hovering pauses it, which
            is the only way to finish reading a long message.
        -->
        <div
            ref="trackRef"
            class="flex w-max animate-marquee py-2.5 hover:[animation-play-state:paused] motion-reduce:animate-none"
            :style="trackStyle"
        >
            <ul
                v-for="track in copies"
                :key="track"
                class="flex shrink-0 items-center"
                :aria-hidden="track === 1 ? undefined : 'true'"
            >
                <li
                    v-for="announcement in ANNOUNCEMENTS"
                    :key="announcement"
                    class="flex items-center gap-8 pr-8 text-[13px] font-semibold whitespace-nowrap sm:gap-12 sm:pr-12"
                >
                    {{ announcement }}
                    <span
                        class="size-1 rounded-full bg-brand-accent-on-ink"
                        aria-hidden="true"
                    />
                </li>
            </ul>
        </div>
    </div>
</template>
