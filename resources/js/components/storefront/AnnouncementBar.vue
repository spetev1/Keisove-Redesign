<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useMarqueeTrack } from '@/composables/useMarqueeTrack';

/**
 * Store-wide promises that scroll past the top of every page. Hard-coded for
 * the demo - in the real store these become editable content rather than a
 * frontend constant.
 */
const announcements: string[] = [
    'Над 12 000 продукта в наличност',
    'Доставка до 24 часа · 6.95 лв.',
    'Безплатна доставка над 60 лв.',
    'Промоция -50% на избрани калъфи',
];

/*
  Collapsing takes the bar's height out of the page above the viewport, and the
  browser holds the view steady by pulling the scroll position up by that same
  amount. So the two thresholds have to sit further apart than the bar is tall:
  any closer and the corrected position lands back across the other boundary,
  which toggles the bar, which moves the scroll again, and the two oscillate.

  The hide threshold is therefore measured from the bar rather than guessed, so
  restyling it cannot quietly reintroduce the loop.
*/
const SHOW_BEFORE_PX = 8;
const SAFETY_MARGIN_PX = 24;

const { trackRef, copies, trackStyle } = useMarqueeTrack();

const isHidden = ref(false);
const contentRef = ref<HTMLElement | null>(null);
const hideAfterPx = ref(96);

function calibrate(): void {
    const height = contentRef.value?.offsetHeight ?? 0;

    if (height > 0) {
        hideAfterPx.value = SHOW_BEFORE_PX + height + SAFETY_MARGIN_PX;
    }
}

function syncToScroll(): void {
    const offset = window.scrollY;

    if (!isHidden.value && offset > hideAfterPx.value) {
        isHidden.value = true;
    } else if (isHidden.value && offset < SHOW_BEFORE_PX) {
        isHidden.value = false;
    }
}

onMounted(() => {
    // Measured while the bar is still open, before the first collapse. The
    // track measures itself; this is only the bar's own height.
    calibrate();

    // A reload can restore a scrolled position, so settle the state up front.
    syncToScroll();

    window.addEventListener('scroll', syncToScroll, { passive: true });
    window.addEventListener('resize', calibrate);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', syncToScroll);
    window.removeEventListener('resize', calibrate);
});
</script>

<template>
    <!--
        Collapsing a grid row from 1fr to 0fr animates to the content's own
        height without measuring it. Because the height is transitioned rather
        than dropped, the page below slides up with the bar instead of jumping.

        The header below carries the same ink, so a hairline is what keeps the
        two from reading as one block. It is light rather than black because
        black on near-black is invisible, and it fades out with the bar so no
        stray line is left behind.
    -->
    <div
        :class="[
            'grid border-b bg-brand-ink text-brand-ink-foreground transition-[grid-template-rows,border-color] duration-300 ease-out [overflow-anchor:none] motion-reduce:transition-none',
            isHidden
                ? 'grid-rows-[0fr] border-transparent'
                : 'grid-rows-[1fr] border-white/15',
        ]"
    >
        <div ref="contentRef" class="overflow-hidden">
            <!--
                The track carries as many copies of the messages as the screen
                needs, and slides by exactly one of them, so the loop lands on
                an identical frame with no seam and no tail. Hovering pauses it,
                which is the only way to finish reading a long message.
            -->
            <div
                ref="trackRef"
                :class="[
                    'flex w-max animate-marquee transition-opacity duration-300 ease-out hover:[animation-play-state:paused] motion-reduce:animate-none motion-reduce:transition-none',
                    isHidden ? 'opacity-0' : 'opacity-100',
                ]"
                :style="trackStyle"
            >
                <ul
                    v-for="track in copies"
                    :key="track"
                    class="flex shrink-0 items-center"
                    :aria-hidden="track === 1 ? undefined : 'true'"
                >
                    <li
                        v-for="announcement in announcements"
                        :key="announcement"
                        class="flex items-center gap-8 py-2 pr-8 text-xs whitespace-nowrap text-white/85 sm:gap-12 sm:pr-12"
                    >
                        {{ announcement }}
                        <span
                            class="size-1 rounded-full bg-brand-highlight"
                            aria-hidden="true"
                        />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
