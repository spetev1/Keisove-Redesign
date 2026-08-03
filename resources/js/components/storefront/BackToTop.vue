<script setup lang="ts">
import { ChevronUp } from '@lucide/vue';
import { useScrollHysteresis } from '@/composables/useScrollHysteresis';

/*
  The same thresholds the header shrinks on, so the button arrives as the bar
  contracts rather than at some unrelated point of its own. Both read as one
  response to having left the top of the page.
*/
const SHOW_AFTER_PX = 110;
const HIDE_BEFORE_PX = 40;

const isScrolled = useScrollHysteresis(SHOW_AFTER_PX, HIDE_BEFORE_PX);

function scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
    <!--
        Held in the corner opposite the newsletter offer, so the two never sit on
        top of one another however long the page is.
    -->
    <Transition
        enter-active-class="transition-opacity duration-300 motion-reduce:transition-none"
        enter-from-class="opacity-0"
        leave-active-class="transition-opacity duration-300 motion-reduce:transition-none"
        leave-to-class="opacity-0"
    >
        <button
            v-if="isScrolled"
            type="button"
            class="fixed right-3 bottom-3 z-60 grid size-11 place-items-center rounded-[14px] bg-brand-ink text-brand-ink-foreground shadow-xl transition-colors hover:bg-brand-accent focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none motion-reduce:transition-none sm:right-7 sm:bottom-7"
            aria-label="Нагоре"
            @click="scrollToTop"
        >
            <ChevronUp class="size-5" />
        </button>
    </Transition>
</template>
