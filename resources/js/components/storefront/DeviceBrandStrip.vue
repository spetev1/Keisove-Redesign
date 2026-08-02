<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import type { CSSProperties } from 'vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import type { DeviceBrand } from '@/types';

/**
 * The handsets the store cuts cases for, standing in fixed slots beside a
 * label. The row itself never travels: a set of four stands long enough to be
 * read, then hands over to the next set one slot at a time, each handset
 * blurring out as its replacement blurs in.
 *
 * They come from the same enum the category filters are built from, so the
 * strip cannot end up promising a handset the filters do not offer.
 *
 * The marks come from Simple Icons, whose icon files are released CC0 - the
 * trademarks themselves stay with the manufacturers, and what each one permits
 * of a retailer is the client's to confirm. They are single-path and monochrome,
 * so they are painted as masks rather than dropped in as pictures: eight logos
 * in their own house colours would fight each other and the band they sit on,
 * where one ink reads as a set.
 */
type Props = {
    families: DeviceBrand[];
    label: string;
};

const props = defineProps<Props>();

/** How many stand on screen at once. */
const SLOT_COUNT = 4;

/** The gap between one slot turning over and the next - the "one by one". */
const STAGGER_MS = 180;

/** How long a full set stands before the next one starts arriving. */
const DWELL_MS = 4200;

/**
 * Only whole sets of four are dealt: a part-filled set would hand over to a row
 * that was mostly the same handsets, which reads as nothing having happened.
 */
const sets = computed<DeviceBrand[][]>(() => {
    const whole = Math.floor(props.families.length / SLOT_COUNT);

    return Array.from({ length: whole }, (_, set) =>
        props.families.slice(set * SLOT_COUNT, (set + 1) * SLOT_COUNT),
    );
});

const slots = ref<DeviceBrand[]>(
    sets.value[0] ?? props.families.slice(0, SLOT_COUNT),
);
const isPaused = ref(false);

let currentSet = 0;
let dwellTimer: ReturnType<typeof setInterval> | undefined;
let handovers: ReturnType<typeof setTimeout>[] = [];

function cancelHandover(): void {
    handovers.forEach((timer) => clearTimeout(timer));
    handovers = [];
}

/**
 * The set is not swapped wholesale - each slot is given its own delay, so the
 * row turns over left to right rather than blinking from one row to another.
 */
function handOver(): void {
    if (isPaused.value || sets.value.length < 2) {
        return;
    }

    cancelHandover();
    currentSet = (currentSet + 1) % sets.value.length;

    sets.value[currentSet].forEach((family, slot) => {
        handovers.push(
            setTimeout(() => {
                slots.value = slots.value.map((current, index) =>
                    index === slot ? family : current,
                );
            }, slot * STAGGER_MS),
        );
    });
}

onMounted(() => {
    // Content that changes on its own is the thing reduced motion is asking us
    // not to do, so it settles on the first set and stays there.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    dwellTimer = setInterval(handOver, DWELL_MS);
});

onBeforeUnmount(() => {
    clearInterval(dwellTimer);
    cancelHandover();
});

/** How tall a mark is drawn, in pixels, where it needs to differ. */
const MARK_SIZES: Record<string, number> = {
    samsung: 44,
    honor: 40,
};

const DEFAULT_MARK_SIZE = 28;

/**
 * The mark is painted rather than placed: the element carries the colour and
 * the file only says which shape of it to keep. Named after the handset's own
 * slug, so a family and its mark cannot drift apart.
 *
 * Every mark is drawn on the same square canvas, so a wide wordmark takes up a
 * thin band of it and comes out far lighter than a symbol filling the square.
 * The two that need it are drawn larger *within* the slot rather than being
 * given a larger slot - the slots have to stay one size, or a taller one
 * centres its own contents and drops its name below the rest of the row.
 */
function markStyle(family: DeviceBrand): CSSProperties {
    const size = MARK_SIZES[family.value] ?? DEFAULT_MARK_SIZE;
    const mask = `url(/images/brands/${family.value}.svg) center / ${size}px ${size}px no-repeat`;

    return { mask, WebkitMask: mask };
}
</script>

<template>
    <div
        class="border-t border-white/10 bg-brand-ink text-brand-ink-foreground"
    >
        <StorefrontContainer
            class="flex flex-col gap-4 py-3 sm:flex-row sm:items-center sm:gap-10"
        >
            <p
                class="shrink-0 text-xs font-semibold tracking-wide text-white/55 uppercase"
            >
                {{ label }}
            </p>

            <!-- Hovering holds the row still, which is the only way to read a
                 handset that is about to hand over. -->
            <ul
                class="grid flex-1 grid-cols-2 items-center gap-y-4 sm:grid-cols-4"
                @mouseenter="isPaused = true"
                @mouseleave="isPaused = false"
            >
                <!--
                    A fixed height, so a slot taking a taller mark than the one
                    it replaces cannot shove the row about mid-handover.
                -->
                <li
                    v-for="(family, slot) in slots"
                    :key="slot"
                    class="flex h-15 items-center justify-center"
                >
                    <!--
                        Keyed on the handset rather than the slot, so replacing
                        one is a leave and an enter rather than a text change -
                        which is what gives the swap something to animate.
                    -->
                    <Transition
                        mode="out-in"
                        enter-active-class="transition duration-400 ease-out motion-reduce:transition-none"
                        enter-from-class="opacity-0 blur-md"
                        leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
                        leave-to-class="opacity-0 blur-md"
                    >
                        <!--
                            The name sits under the mark rather than beside it:
                            the marks are all different proportions, and a name
                            alongside would leave the row ragged where a wide
                            wordmark met a compact symbol. The mark is hidden
                            from screen readers because the name below it is
                            already saying the same thing.
                        -->
                        <span
                            :key="family.value"
                            class="flex flex-col items-center gap-1"
                        >
                            <span
                                class="block h-11 w-full bg-white/70"
                                :style="markStyle(family)"
                                aria-hidden="true"
                            />
                            <span
                                class="text-[11px] leading-none font-medium tracking-wide text-white/45 uppercase"
                            >
                                {{ family.label }}
                            </span>
                        </span>
                    </Transition>
                </li>
            </ul>
        </StorefrontContainer>
    </div>
</template>
