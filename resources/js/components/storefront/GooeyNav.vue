<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, useId } from 'vue';

/**
 * A hover track: one lavender pill follows the pointer between the items
 * placed in the default slot. Each item must carry `data-gooey-item` so the
 * track can find and measure it.
 *
 * The slot receives `activeIndex` so items can flip their own text colour once
 * the pill is under them.
 */

/** Leading blob. The trailing one is slower, which is what does the stretching. */
const LEAD_MS = 320;
const TRAIL_MS = 470;
/** Long enough for the slower blob to land before the goo is switched off. */
const SETTLE_MS = TRAIL_MS + 60;

const filterId = `gooey-nav-${useId().replace(/[^a-zA-Z0-9-]/g, '')}`;

const trackRef = ref<HTMLElement | null>(null);
const activeIndex = ref<number | null>(null);
const isMoving = ref(false);
const bounds = ref({ left: 0, width: 0 });

let settleTimer: ReturnType<typeof setTimeout> | undefined;

function items(): HTMLElement[] {
    return Array.from(
        trackRef.value?.querySelectorAll<HTMLElement>('[data-gooey-item]') ??
            [],
    );
}

/**
 * The goo is only switched on while a blob is in flight, so what settles is a
 * plain rounded pill rather than a softened blob.
 */
function startMoving(): void {
    clearTimeout(settleTimer);
    isMoving.value = true;
    settleTimer = setTimeout(() => (isMoving.value = false), SETTLE_MS);
}

/**
 * Delegated, so the track works with whatever the slot renders. `mouseover`
 * rather than `mouseenter` because only the former bubbles.
 */
function activateFrom(event: Event): void {
    const target = event.target as HTMLElement | null;
    const item = target?.closest<HTMLElement>('[data-gooey-item]');

    if (!item) {
        return;
    }

    const index = items().indexOf(item);

    // Moving around inside the current item must not restart the animation.
    if (index === -1 || index === activeIndex.value) {
        return;
    }

    bounds.value = { left: item.offsetLeft, width: item.offsetWidth };
    activeIndex.value = index;
    startMoving();
}

function deactivate(): void {
    if (activeIndex.value === null) {
        return;
    }

    activeIndex.value = null;
    startMoving();
}

function handleFocusOut(event: FocusEvent): void {
    const next = event.relatedTarget as Node | null;

    if (!next || !trackRef.value?.contains(next)) {
        deactivate();
    }
}

function remeasure(): void {
    if (activeIndex.value === null) {
        return;
    }

    const item = items()[activeIndex.value];

    if (item) {
        bounds.value = { left: item.offsetLeft, width: item.offsetWidth };
    }
}

function blobStyle(durationMs: number): Record<string, string> {
    const active = activeIndex.value !== null;

    return {
        left: `${bounds.value.left}px`,
        width: `${bounds.value.width}px`,
        opacity: active ? '1' : '0',
        scale: active ? '1' : '0.4',
        transitionDuration: `${durationMs}ms`,
    };
}

onMounted(() => window.addEventListener('resize', remeasure));

onBeforeUnmount(() => {
    clearTimeout(settleTimer);
    window.removeEventListener('resize', remeasure);
});
</script>

<template>
    <div class="relative flex items-center" @mouseleave="deactivate">
        <!--
            Blur the two blobs together, then push the alpha through a steep
            ramp so the blurred edges snap back into one solid outline.
        -->
        <svg class="absolute size-0" aria-hidden="true" focusable="false">
            <defs>
                <filter
                    :id="filterId"
                    x="-50%"
                    y="-50%"
                    width="200%"
                    height="200%"
                >
                    <feGaussianBlur
                        in="SourceGraphic"
                        stdDeviation="6"
                        result="blur"
                    />
                    <feColorMatrix
                        in="blur"
                        type="matrix"
                        values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -9"
                    />
                </filter>
            </defs>
        </svg>

        <!--
            The filter is only attached mid-flight. At rest the two blobs sit
            exactly on top of each other and read as one crisp pill.
        -->
        <span
            class="pointer-events-none absolute inset-0"
            :style="isMoving ? { filter: `url(#${filterId})` } : undefined"
            aria-hidden="true"
        >
            <span
                class="absolute top-1/2 h-9 origin-center -translate-y-1/2 rounded-full bg-brand-surface-strong transition-[left,width,opacity,scale] ease-out motion-reduce:transition-none"
                :style="blobStyle(TRAIL_MS)"
            />
            <span
                class="absolute top-1/2 h-9 origin-center -translate-y-1/2 rounded-full bg-brand-surface-strong transition-[left,width,opacity,scale] ease-out motion-reduce:transition-none"
                :style="blobStyle(LEAD_MS)"
            />
        </span>

        <div
            ref="trackRef"
            class="relative flex items-center gap-1"
            @mouseover="activateFrom"
            @focusin="activateFrom"
            @focusout="handleFocusOut"
        >
            <slot :active-index="activeIndex" />
        </div>
    </div>
</template>
