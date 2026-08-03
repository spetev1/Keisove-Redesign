<script setup lang="ts">
import { X } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { NEWSLETTER_COPY } from '@/lib/demoCopy';

type Props = {
    /**
     * How far down the page to let someone get before asking for anything.
     * Scroll rather than a timer: someone who has travelled this far has chosen
     * to look around, where a timer fires at whoever is still reading the hero.
     */
    offerAfterPx?: number;
};

const props = withDefaults(defineProps<Props>(), {
    offerAfterPx: 300,
});

/**
 * Session rather than local storage: closing it should hold for the visit, but
 * a fresh tab offers again, which is what a demo needs.
 */
const STORAGE_KEY = 'keisove:newsletter-popup';

type Mode = 'hidden' | 'open' | 'collapsed';

const mode = ref<Mode>('hidden');
const hasSubscribed = ref(false);
const email = ref('');

let collapseTimer: ReturnType<typeof setTimeout> | undefined;

function readState(): string | null {
    try {
        return sessionStorage.getItem(STORAGE_KEY);
    } catch {
        // Private browsing can refuse storage; the popup still works without it.
        return null;
    }
}

function writeState(value: string): void {
    try {
        sessionStorage.setItem(STORAGE_KEY, value);
    } catch {
        // Nothing to do; it will simply offer again next time.
    }
}

/** Closing tucks the offer away rather than destroying it. */
function collapse(): void {
    clearTimeout(collapseTimer);
    mode.value = 'collapsed';
    writeState(hasSubscribed.value ? 'subscribed' : 'collapsed');
}

function expand(): void {
    clearTimeout(collapseTimer);
    mode.value = 'open';
}

/**
 * The demo captures the address but has nowhere to send it yet, so the form
 * acknowledges locally rather than posting to an endpoint that does not exist.
 */
function subscribe(): void {
    if (email.value.trim() === '') {
        return;
    }

    hasSubscribed.value = true;
    email.value = '';
    writeState('subscribed');

    collapseTimer = setTimeout(collapse, 3500);
}

/**
 * Detached the moment it fires: the offer is made once a visit, so after that
 * there is nothing left for it to decide.
 */
function offerOnScroll(): void {
    if (window.scrollY <= props.offerAfterPx) {
        return;
    }

    mode.value = 'open';
    window.removeEventListener('scroll', offerOnScroll);
}

onMounted(() => {
    const stored = readState();

    if (stored === 'subscribed' || stored === 'collapsed') {
        hasSubscribed.value = stored === 'subscribed';
        mode.value = 'collapsed';

        return;
    }

    window.addEventListener('scroll', offerOnScroll, { passive: true });
});

onBeforeUnmount(() => {
    clearTimeout(collapseTimer);
    window.removeEventListener('scroll', offerOnScroll);
});
</script>

<template>
    <!--
        Both states are fixed to the same corner and scale from it, so closing
        reads as the panel folding down into the badge rather than two separate
        things swapping places. The wrapper is inert: it takes no space because
        neither child is in flow.
    -->
    <div>
        <Transition
            enter-active-class="transition duration-[450ms] ease-out motion-reduce:transition-none"
            enter-from-class="translate-y-6 scale-95 opacity-0"
            enter-to-class="translate-y-0 scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
            leave-from-class="translate-y-0 scale-100 opacity-100"
            leave-to-class="translate-y-6 scale-95 opacity-0"
        >
            <aside
                v-if="mode === 'open'"
                class="fixed bottom-3 left-3 z-60 w-[min(365px,calc(100vw-1.5rem))] origin-bottom-left rounded-[18px] bg-brand-ink p-4 text-brand-ink-foreground shadow-2xl sm:bottom-7 sm:left-7"
                aria-label="Оферта за нови клиенти"
            >
                <button
                    type="button"
                    class="absolute top-2.5 right-2.5 flex size-[30px] items-center justify-center rounded-full bg-white/15 transition-colors hover:bg-white/30"
                    aria-label="Затвори"
                    @click="collapse"
                >
                    <X class="size-4" />
                </button>

                <p
                    class="text-[11.5px] font-extrabold tracking-[0.08em] text-brand-accent-on-ink uppercase"
                >
                    {{ NEWSLETTER_COPY.eyebrow }}
                </p>

                <h2
                    class="mt-1.5 pr-8 text-[17.5px] leading-snug font-extrabold tracking-tight"
                >
                    {{ NEWSLETTER_COPY.headline }}
                </h2>

                <p
                    class="mt-1 mb-3 max-w-[34ch] text-[12.5px] leading-snug text-brand-ink-muted"
                >
                    {{
                        hasSubscribed
                            ? NEWSLETTER_COPY.sent
                            : NEWSLETTER_COPY.body
                    }}
                </p>

                <form class="flex flex-wrap gap-2" @submit.prevent="subscribe">
                    <label for="newsletter-popup-email" class="sr-only">
                        Твоят имейл
                    </label>
                    <input
                        id="newsletter-popup-email"
                        v-model="email"
                        type="email"
                        required
                        placeholder="твоят имейл"
                        class="h-[42px] min-w-[120px] flex-1 rounded-[11px] border-0 bg-card px-3 text-[13.5px] text-foreground outline-none placeholder:text-muted-foreground"
                    />
                    <button
                        type="submit"
                        class="h-[42px] shrink-0 rounded-[11px] bg-brand-accent px-4 text-[13.5px] font-extrabold whitespace-nowrap text-brand-accent-foreground transition-colors hover:bg-brand-accent-hover"
                    >
                        {{
                            hasSubscribed
                                ? NEWSLETTER_COPY.sentCta
                                : NEWSLETTER_COPY.cta
                        }}
                    </button>
                </form>
            </aside>
        </Transition>

        <Transition
            enter-active-class="transition duration-[350ms] ease-out motion-reduce:transition-none"
            enter-from-class="translate-y-2.5 scale-90 opacity-0"
            enter-to-class="translate-y-0 scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
            leave-from-class="translate-y-0 scale-100 opacity-100"
            leave-to-class="translate-y-2.5 scale-90 opacity-0"
        >
            <!-- A pill rather than a disc, because it keeps the figure legible
                 at the size a tucked-away offer is allowed to be. -->
            <button
                v-if="mode === 'collapsed'"
                type="button"
                class="fixed bottom-3 left-3 z-60 flex h-[46px] origin-bottom-left items-center gap-2 rounded-full bg-brand-ink px-4 text-sm font-extrabold tracking-tight text-brand-ink-foreground shadow-xl transition-colors hover:bg-brand-ink-hover focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none motion-reduce:transition-none sm:bottom-7 sm:left-7"
                :aria-label="
                    hasSubscribed
                        ? 'Отвори офертата за нови клиенти'
                        : 'Вземи -10% на първата поръчка'
                "
                aria-expanded="false"
                @click="expand"
            >
                <span
                    class="size-2 rounded-full bg-brand-accent-hover"
                    aria-hidden="true"
                />
                {{ NEWSLETTER_COPY.badge }}
            </button>
        </Transition>
    </div>
</template>
