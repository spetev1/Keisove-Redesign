import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import type { CSSProperties } from 'vue';

/**
 * A marquee loops by sliding its track along by exactly one copy of its
 * contents, which lands on an identical frame. For that to be invisible, what
 * is left behind the shifted copy has to still cover the run - so the track
 * needs one copy more than it takes to fill it.
 *
 * The count is measured rather than assumed: a fixed two copies only looks
 * seamless while the contents happen to be wider than the space they run in,
 * and on a wider screen the track runs out mid-cycle and leaves a gap.
 *
 * The track is expected to hold identical copies as its element children, and
 * to carry `trackStyle` so the keyframes know how far one copy is.
 */
export function useMarqueeTrack(minimumCopies = 2) {
    const trackRef = ref<HTMLElement | null>(null);
    const copies = ref(minimumCopies);

    const trackStyle = computed<CSSProperties>(() => ({
        '--marquee-shift': `${-100 / copies.value}%`,
    }));

    /**
     * Measured off the first copy, whose own width does not depend on how many
     * follow it, against the width of whatever clips the track.
     */
    function fitTrack(): void {
        const track = trackRef.value;
        const copy = track?.firstElementChild;

        if (!(copy instanceof HTMLElement) || copy.offsetWidth <= 0) {
            return;
        }

        const run = track?.parentElement?.clientWidth ?? window.innerWidth;

        copies.value = Math.max(
            minimumCopies,
            Math.ceil(run / copy.offsetWidth) + 1,
        );
    }

    onMounted(() => {
        fitTrack();

        // Marquee contents are set in a webfont: measured before it lands, a
        // copy comes out the wrong width and the count with it.
        document.fonts?.ready.then(fitTrack);

        window.addEventListener('resize', fitTrack);
    });

    onBeforeUnmount(() => window.removeEventListener('resize', fitTrack));

    return { trackRef, copies, trackStyle, fitTrack };
}
