<script setup lang="ts">
import { X } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Props = {
    /** How long to let someone look around before asking for anything. */
    delayMs?: number;
};

const props = withDefaults(defineProps<Props>(), {
    delayMs: 6000,
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

let openTimer: ReturnType<typeof setTimeout> | undefined;
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

onMounted(() => {
    const stored = readState();

    if (stored === 'subscribed') {
        hasSubscribed.value = true;
        mode.value = 'collapsed';

        return;
    }

    if (stored === 'collapsed') {
        mode.value = 'collapsed';

        return;
    }

    openTimer = setTimeout(() => (mode.value = 'open'), props.delayMs);
});

onBeforeUnmount(() => {
    clearTimeout(openTimer);
    clearTimeout(collapseTimer);
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
            enter-active-class="transition duration-[400ms] ease-out motion-reduce:transition-none"
            enter-from-class="translate-y-6 scale-95 opacity-0"
            enter-to-class="translate-y-0 scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
            leave-from-class="translate-y-0 scale-100 opacity-100"
            leave-to-class="translate-y-6 scale-95 opacity-0"
        >
            <aside
                v-if="mode === 'open'"
                class="fixed bottom-4 left-4 z-50 w-[calc(100vw-2rem)] origin-bottom-left rounded-xl bg-primary p-5 text-primary-foreground shadow-xl sm:right-auto sm:w-[22rem]"
                aria-label="Оферта за нови клиенти"
            >
                <button
                    type="button"
                    class="absolute top-3 right-3 flex size-7 items-center justify-center rounded-full bg-primary-foreground/15 text-primary-foreground transition-colors hover:bg-primary-foreground/25"
                    aria-label="Скрий офертата"
                    @click="collapse"
                >
                    <X class="size-4" />
                </button>

                <p
                    class="text-[11px] font-semibold tracking-wide text-primary-foreground/80 uppercase"
                >
                    Само за нови клиенти
                </p>

                <h2 class="mt-2 pr-8 text-lg leading-snug font-bold">
                    Вземи -10% на първата поръчка
                </h2>

                <p class="mt-2 text-sm text-primary-foreground/75">
                    Абонирай се и получавай промоциите преди всички. Без спам.
                </p>

                <p v-if="hasSubscribed" class="mt-4 text-sm font-medium">
                    Благодарим! Кодът пътува към пощата ти.
                </p>
                <form
                    v-else
                    class="mt-4 flex gap-2"
                    @submit.prevent="subscribe"
                >
                    <label for="newsletter-popup-email" class="sr-only">
                        Твоят имейл
                    </label>
                    <Input
                        id="newsletter-popup-email"
                        v-model="email"
                        type="email"
                        required
                        placeholder="твоят имейл"
                        class="h-10 border-transparent bg-background text-foreground"
                    />
                    <Button
                        type="submit"
                        class="h-10 shrink-0 bg-success text-success-foreground hover:bg-success/90"
                    >
                        Абонирай се
                    </Button>
                </form>
            </aside>
        </Transition>

        <Transition
            enter-active-class="transition duration-[400ms] ease-out motion-reduce:transition-none"
            enter-from-class="scale-0 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in motion-reduce:transition-none"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-0 opacity-0"
        >
            <button
                v-if="mode === 'collapsed'"
                type="button"
                class="fixed bottom-4 left-4 z-50 flex size-14 origin-bottom-left items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground shadow-xl transition-transform duration-300 hover:scale-105 focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background motion-reduce:transition-none"
                :aria-label="
                    hasSubscribed
                        ? 'Отвори офертата за нови клиенти'
                        : 'Вземи -10% на първата поръчка'
                "
                aria-expanded="false"
                @click="expand"
            >
                -10%
            </button>
        </Transition>
    </div>
</template>
