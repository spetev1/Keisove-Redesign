<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { CSSProperties } from 'vue';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { HERO_COPY } from '@/lib/demoCopy';

type Props = {
    /** Product photography for the collage behind the copy. */
    collage: string[];
    promotionsHref: string;
    /** Where "Намери по модел" goes - the brand panel further down the page. */
    brandsHref: string;
};

const props = defineProps<Props>();

/**
 * How many times each column repeats its photographs.
 *
 * Two is the tempting answer - slide by half and you land on the copy - but it
 * leaves nothing in reserve: at the end of a cycle the only content below the
 * window is the one set being scrolled into it, so the tail runs dry and the
 * restart reads as a jump back to the top. A third copy means a full set is
 * always still queued below, whatever the hero's height.
 */
const COLLAGE_COPIES = 3;

/**
 * The collage runs as two columns travelling in opposite directions, each with
 * its own share of the photographs.
 */
const columns = computed<string[][]>(() => {
    const half = Math.ceil(props.collage.length / 2);

    return [props.collage.slice(0, half), props.collage.slice(half)].filter(
        (column) => column.length > 0,
    );
});

/** One column's worth of shots, repeated, as the track actually renders it. */
function trackOf(column: string[]): string[] {
    return Array.from({ length: COLLAGE_COPIES }, () => column).flat();
}

const collageRef = ref<HTMLElement | null>(null);

/** How far each column travels in one cycle, in pixels. 0 until measured. */
const columnShifts = ref<number[]>([]);

/**
 * Measures one repeating unit per column, rather than expressing it as a
 * percentage.
 *
 * A percentage would be the obvious choice and is the wrong one. `translateY(%)`
 * resolves against the element's *own* box, and these columns are flex children
 * of a wrapper pinned `top-0 bottom-0` - so they stretch to the hero's height
 * while their content runs far past it. A third of that box is a third of the
 * hero, not a third of the track, and the loop lands hundreds of pixels short of
 * where it started. That is the jump.
 *
 * So the distance is taken from the layout: the offset between a shot and the
 * same shot one copy later is exactly one repeating unit, margins and all. It is
 * read from `offsetTop` rather than a bounding rect because the collage is
 * rotated and scaled, and a rect would come back multiplied by that scale.
 */
function measureColumns(): void {
    const columns =
        collageRef.value?.querySelectorAll<HTMLElement>(
            '[data-collage-column]',
        ) ?? [];

    columnShifts.value = Array.from(columns).map((column) => {
        const shots = column.querySelectorAll<HTMLElement>('img');
        const perCopy = Math.floor(shots.length / COLLAGE_COPIES);

        if (perCopy < 1 || shots.length <= perCopy) {
            return 0;
        }

        return shots[perCopy].offsetTop - shots[0].offsetTop;
    });
}

/**
 * The shot height follows the column width, which is a clamp on the viewport, so
 * a resize changes the unit and it has to be measured again.
 */
onMounted(() => {
    measureColumns();
    window.addEventListener('resize', measureColumns);
});

onBeforeUnmount(() => window.removeEventListener('resize', measureColumns));

/**
 * Nothing animates until its distance is known - a column that started travelling
 * on the CSS fallback would visibly correct itself once measured.
 */
function columnStyle(index: number): CSSProperties {
    const shift = columnShifts.value[index] ?? 0;

    return shift > 0
        ? ({ '--collage-shift': `-${shift}px` } as CSSProperties)
        : {};
}

function isMeasured(index: number): boolean {
    return (columnShifts.value[index] ?? 0) > 0;
}

/**
 * Fades the collage out at the top and bottom rather than letting it stop at a
 * hard edge. Written as a style rather than a class so the WebKit prefix comes
 * with it - Safari still needs its own property here.
 */
const collageMask: CSSProperties = {
    maskImage:
        'linear-gradient(to bottom, transparent, #000 14%, #000 86%, transparent)',
    WebkitMaskImage:
        'linear-gradient(to bottom, transparent, #000 14%, #000 86%, transparent)',
};
</script>

<template>
    <!--
        The hero is a panel rather than a full-bleed band: it sits in the page
        grid beside its two spotlight cards, which is what stops the top of the
        homepage being one wide photograph and nothing else.
    -->
    <section
        class="relative flex min-h-[380px] flex-col justify-center gap-4 overflow-hidden rounded-3xl bg-brand-ink p-6 text-brand-ink-foreground sm:gap-[18px] sm:p-9 lg:p-11"
    >
        <!-- A wash of the accent green bled in from the corner, so the violet is
             not one flat field behind the copy. -->
        <div
            class="absolute -top-20 -right-20 size-80 rounded-full bg-brand-accent/28"
            aria-hidden="true"
        />

        <div
            v-if="columns.length > 0"
            ref="collageRef"
            class="absolute top-0 right-0 bottom-0 flex w-[clamp(150px,34%,380px)] scale-[1.18] -rotate-7 items-start justify-end gap-3.5 overflow-hidden px-2.5 sm:px-4 lg:px-6"
            :style="collageMask"
            aria-hidden="true"
        >
            <!--
                `items-start` matters: as flex children of a wrapper pinned top to
                bottom, these columns would otherwise stretch to the hero's height
                while their content ran well past it, which is what made a
                percentage shift meaningless. Sized to their content, the track is
                the track.

                No `gap` down the column either - the spacing is each shot's own
                bottom margin, so every repeating unit is one whole
                shot-and-margin and the measured distance covers it exactly.

                Different speeds per column, so the two never travel as one sheet
                sliding past.
            -->
            <div
                v-for="(column, index) in columns"
                :key="index"
                data-collage-column
                :class="[
                    'flex shrink-0 grow-0 basis-[clamp(96px,42%,150px)] flex-col',
                    isMeasured(index) &&
                        (index % 2 === 0
                            ? 'animate-collage-up'
                            : 'animate-collage-down'),
                    'motion-reduce:animate-none',
                ]"
                :style="columnStyle(index)"
            >
                <img
                    v-for="(image, position) in trackOf(column)"
                    :key="`${image}-${position}`"
                    :src="image"
                    alt=""
                    loading="lazy"
                    class="mb-3.5 aspect-square w-full rounded-[18px] bg-white/15 object-cover brightness-110 saturate-110"
                />
            </div>
        </div>

        <!--
            Holds the copy legible over the collage: opaque across the left side,
            easing off only once past where the longest line ends.
        -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-brand-ink from-42% via-brand-ink/55 via-60% to-brand-ink/12"
            aria-hidden="true"
        />

        <div class="relative flex max-w-[560px] flex-col gap-4 sm:gap-[18px]">
            <span
                class="self-start rounded-lg bg-brand-accent px-3 py-1.5 text-[12.5px] font-extrabold tracking-[0.08em] text-brand-accent-foreground uppercase"
            >
                {{ HERO_COPY.badge }}
            </span>

            <h1
                class="text-3xl leading-[1.04] font-extrabold tracking-tighter text-balance sm:text-4xl lg:text-[54px]"
            >
                {{ HERO_COPY.headline }}<br />{{ HERO_COPY.headlineSecondLine }}
            </h1>

            <p
                class="max-w-[44ch] text-[15px] leading-relaxed text-pretty text-brand-ink-muted lg:text-lg"
            >
                {{ HERO_COPY.body }}
            </p>

            <div class="mt-1 flex flex-wrap gap-3">
                <Link
                    :href="promotionsHref"
                    class="inline-flex h-13 items-center rounded-[13px] bg-brand-accent px-6 text-base font-extrabold text-brand-accent-foreground transition-colors hover:bg-brand-accent-hover"
                >
                    {{ HERO_COPY.promotionsCta }}
                </Link>
                <!-- An in-page jump rather than a visit, so it stays a plain
                     anchor: the brand panel it opens is further down this page. -->
                <a
                    :href="brandsHref"
                    class="inline-flex h-13 items-center rounded-[13px] border-[1.5px] border-white/35 px-6 text-base font-bold transition-colors hover:border-white hover:bg-white/10"
                >
                    {{ HERO_COPY.browseCta }}
                </a>
            </div>

            <div
                class="mt-2.5 flex flex-wrap gap-x-6 gap-y-2 text-[13.5px] font-semibold text-brand-ink-muted"
            >
                <span>
                    <span class="text-brand-star" aria-hidden="true">
                        ★★★★★
                    </span>
                    {{ HERO_COPY.rating }}
                </span>
                <span>{{ HERO_COPY.returns }}</span>
            </div>
        </div>
    </section>
</template>
