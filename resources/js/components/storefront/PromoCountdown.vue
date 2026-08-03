<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { COUNTDOWN_COPY } from '@/lib/demoCopy';

type Props = {
    /** When the promotion closes, as an ISO 8601 string from the controller. */
    endsAt: string;
};

const props = defineProps<Props>();

const SECOND_MS = 1000;
const MINUTE_S = 60;
const HOUR_S = 60 * MINUTE_S;
const DAY_S = 24 * HOUR_S;

function secondsRemaining(): number {
    const remaining = (Date.parse(props.endsAt) - Date.now()) / SECOND_MS;

    /*
      Never negative. An expired promotion showing minus figures would be worse
      than one showing zeroes, and the band is only on the page while there is
      something to count down to.
    */
    return Number.isFinite(remaining) ? Math.max(0, Math.floor(remaining)) : 0;
}

// Seeded at setup rather than on mount, so the first paint already carries the
// real figures instead of flashing zeroes.
const remaining = ref(secondsRemaining());

let ticker: ReturnType<typeof setInterval> | undefined;

onMounted(() => {
    ticker = setInterval(
        () => (remaining.value = secondsRemaining()),
        SECOND_MS,
    );
});

onBeforeUnmount(() => clearInterval(ticker));

/** Two digits throughout, so the row does not jump as figures roll over. */
function padded(value: number): string {
    return String(value).padStart(2, '0');
}

const units = computed(() => [
    {
        value: padded(Math.floor(remaining.value / DAY_S)),
        label: COUNTDOWN_COPY.units.days,
    },
    {
        value: padded(Math.floor(remaining.value / HOUR_S) % 24),
        label: COUNTDOWN_COPY.units.hours,
    },
    {
        value: padded(Math.floor(remaining.value / MINUTE_S) % 60),
        label: COUNTDOWN_COPY.units.minutes,
    },
    {
        value: padded(remaining.value % 60),
        label: COUNTDOWN_COPY.units.seconds,
    },
]);
</script>

<template>
    <StorefrontContainer class="pt-5 sm:pt-7">
        <div
            class="flex flex-wrap items-center justify-between gap-5 rounded-3xl border border-border bg-card p-5 sm:gap-8 sm:p-8"
        >
            <div class="min-w-[260px] flex-1 basis-[300px]">
                <p
                    class="text-xs font-extrabold tracking-[0.08em] text-brand-sale uppercase"
                >
                    {{ COUNTDOWN_COPY.eyebrow }}
                </p>
                <h2
                    class="mt-2 mb-1.5 text-[22px] font-extrabold tracking-tight text-balance sm:text-2xl lg:text-3xl"
                >
                    {{ COUNTDOWN_COPY.headline }}
                </h2>
                <p
                    class="max-w-[46ch] text-[15px] text-pretty text-muted-foreground"
                >
                    {{ COUNTDOWN_COPY.body }}
                </p>
            </div>

            <!--
                The seconds tile is the filled one: it is the only figure moving
                fast enough to be worth looking at, and colouring it is what
                stops the row reading as a static date.
            -->
            <!--
                Two by two on a phone rather than a wrapping row, which left the
                seconds stranded on a line of their own under the other three.
            -->
            <ul class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                <li
                    v-for="(unit, index) in units"
                    :key="unit.label"
                    :class="[
                        'min-w-[78px] rounded-[14px] px-2.5 py-3.5 text-center',
                        index === units.length - 1
                            ? 'bg-brand-ink text-brand-ink-foreground'
                            : 'bg-brand-surface-strong',
                    ]"
                >
                    <p
                        class="text-[26px] leading-none font-extrabold tracking-tighter tabular-nums sm:text-3xl lg:text-[34px]"
                    >
                        {{ unit.value }}
                    </p>
                    <p
                        :class="[
                            'mt-1 text-[11.5px] font-bold tracking-[0.06em] uppercase',
                            index === units.length - 1
                                ? 'text-brand-ink-soft'
                                : 'text-muted-foreground',
                        ]"
                    >
                        {{ unit.label }}
                    </p>
                </li>
            </ul>
        </div>
    </StorefrontContainer>
</template>
