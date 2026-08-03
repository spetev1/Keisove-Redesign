import { onBeforeUnmount, onMounted, readonly, ref } from 'vue';
import type { Ref } from 'vue';

/**
 * A boolean that turns on once the page is scrolled past `enterPx` and only
 * turns off again above `exitPx`.
 *
 * The two thresholds are what stop it flickering. Anything driven off this
 * changes the height of the page - a header that shrinks, a bar that folds -
 * and the browser answers that by correcting the scroll position to hold the
 * view steady. With a single threshold the correction lands back across it, the
 * state flips, the page resizes again, and the two oscillate. Keeping the
 * release well below the trigger means the corrected position can never reach
 * it.
 *
 * @param enterPx  How far down the state switches on.
 * @param exitPx   How far back up it switches off. Must be well under `enterPx`.
 */
export function useScrollHysteresis(
    enterPx: number,
    exitPx: number,
): Readonly<Ref<boolean>> {
    const isPast = ref(false);

    function syncToScroll(): void {
        const offset = window.scrollY;

        if (!isPast.value && offset > enterPx) {
            isPast.value = true;
        } else if (isPast.value && offset <= exitPx) {
            isPast.value = false;
        }
    }

    onMounted(() => {
        // A reload can restore a scrolled position, so settle the state up front
        // rather than waiting for the first scroll event.
        syncToScroll();

        window.addEventListener('scroll', syncToScroll, { passive: true });
    });

    onBeforeUnmount(() => window.removeEventListener('scroll', syncToScroll));

    return readonly(isPast);
}
