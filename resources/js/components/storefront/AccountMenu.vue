<script setup lang="ts">
import { Form, Link, usePage } from '@inertiajs/vue3';
import { User } from '@lucide/vue';
import type { HTMLAttributes } from 'vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { dashboard, logout, register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

type Props = {
    /**
     * The header owns the button's height, because it shrinks along with the
     * rest of the bar as the page scrolls.
     */
    triggerClass?: HTMLAttributes['class'];
};

const props = defineProps<Props>();

const OPEN_DELAY_MS = 90;
const CLOSE_DELAY_MS = 200;

const page = usePage();

const currentUser = computed(() => page.props.auth?.user ?? null);

const isOpen = ref(false);
const root = ref<HTMLElement | null>(null);

let openTimer: ReturnType<typeof setTimeout> | undefined;
let closeTimer: ReturnType<typeof setTimeout> | undefined;

function clearTimers(): void {
    clearTimeout(openTimer);
    clearTimeout(closeTimer);
}

function openSoon(): void {
    clearTimers();
    openTimer = setTimeout(() => (isOpen.value = true), OPEN_DELAY_MS);
}

/**
 * Closing on mouseleave would yank the form away from someone who is typing in
 * it, so a focused field keeps the panel open regardless of the pointer.
 */
function closeSoon(): void {
    clearTimers();
    closeTimer = setTimeout(() => {
        if (root.value?.contains(document.activeElement)) {
            return;
        }

        isOpen.value = false;
    }, CLOSE_DELAY_MS);
}

function openNow(): void {
    clearTimers();
    isOpen.value = true;
}

function closeNow(): void {
    clearTimers();
    isOpen.value = false;
}

function handleFocusOut(event: FocusEvent): void {
    const next = event.relatedTarget as Node | null;

    if (!next || !root.value?.contains(next)) {
        closeNow();
    }
}

function handleEscape(): void {
    closeNow();
    root.value?.querySelector('button')?.focus();
}

onBeforeUnmount(clearTimers);
</script>

<template>
    <div
        ref="root"
        class="relative flex"
        @mouseenter="openSoon"
        @mouseleave="closeSoon"
        @focusin="openNow"
        @focusout="handleFocusOut"
        @keydown.esc="handleEscape"
    >
        <!--
            The label is what the design puts here rather than an icon alone, so
            the icon drops away on a phone and the word carries it from `sm` up -
            "Профил" is shorter than the pictogram is wide once it has a border
            around it.
        -->
        <button
            type="button"
            :class="
                cn(
                    'flex min-w-11 items-center justify-center gap-1.5 rounded-xl border border-border px-3 text-[13px] font-bold text-secondary-foreground transition-colors hover:border-brand-accent hover:text-brand-accent-ink focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none',
                    props.triggerClass,
                )
            "
            aria-haspopup="true"
            :aria-expanded="isOpen"
            aria-label="Моят профил"
            @click="isOpen ? closeNow() : openNow()"
        >
            <User class="size-4 sm:hidden" />
            <span class="hidden sm:inline">Профил</span>
        </button>

        <!--
            Kept mounted rather than toggled with v-if so it can animate both
            ways; `invisible` takes the form out of the tab order while closed.
        -->
        <div
            :class="[
                'absolute top-full right-0 z-50 w-80 pt-3',
                isOpen ? 'visible' : 'invisible',
            ]"
        >
            <div
                :class="[
                    'grid transition-[grid-template-rows,opacity] duration-300 ease-out motion-reduce:transition-none',
                    isOpen
                        ? 'grid-rows-[1fr] opacity-100'
                        : 'grid-rows-[0fr] opacity-0',
                ]"
            >
                <div class="overflow-hidden">
                    <div
                        class="rounded-xl border border-border bg-brand-surface p-4 shadow-lg"
                    >
                        <template v-if="currentUser">
                            <p class="text-sm font-semibold text-foreground">
                                {{ currentUser.name }}
                            </p>
                            <p
                                class="mt-1 truncate text-xs text-muted-foreground"
                            >
                                {{ currentUser.email }}
                            </p>

                            <div class="mt-4 flex flex-col gap-2">
                                <Link
                                    :href="dashboard()"
                                    class="text-sm text-foreground transition-colors hover:text-primary"
                                    @click="closeNow"
                                >
                                    Табло
                                </Link>
                                <Link
                                    :href="logout()"
                                    as="button"
                                    class="text-left text-sm text-foreground transition-colors hover:text-primary"
                                >
                                    Изход
                                </Link>
                            </div>
                        </template>

                        <template v-else>
                            <h2 class="text-sm font-semibold text-foreground">
                                Вход в профила
                            </h2>

                            <Form
                                v-bind="store.form()"
                                :reset-on-success="['password']"
                                v-slot="{ errors, processing }"
                                class="mt-3 flex flex-col gap-3"
                            >
                                <div class="grid gap-1.5">
                                    <Label
                                        for="account-email"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Имейл
                                    </Label>
                                    <Input
                                        id="account-email"
                                        type="email"
                                        name="email"
                                        required
                                        autocomplete="email"
                                        placeholder="твоят имейл"
                                        class="h-9 bg-background"
                                    />
                                    <InputError :message="errors.email" />
                                </div>

                                <div class="grid gap-1.5">
                                    <Label
                                        for="account-password"
                                        class="text-xs text-muted-foreground"
                                    >
                                        Парола
                                    </Label>
                                    <PasswordInput
                                        id="account-password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="паролата ти"
                                        class="h-9 bg-background"
                                    />
                                    <InputError :message="errors.password" />
                                </div>

                                <Button
                                    type="submit"
                                    class="mt-1 w-full"
                                    :disabled="processing"
                                >
                                    <Spinner v-if="processing" />
                                    Вход
                                </Button>
                            </Form>

                            <div
                                class="mt-4 border-t border-border pt-3 text-xs text-muted-foreground"
                            >
                                <p>
                                    Нямаш профил?
                                    <Link
                                        :href="register()"
                                        class="font-medium text-primary transition-colors hover:underline"
                                        @click="closeNow"
                                    >
                                        Регистрирай се
                                    </Link>
                                </p>
                                <Link
                                    :href="request()"
                                    class="mt-1 inline-block transition-colors hover:text-primary"
                                    @click="closeNow"
                                >
                                    Забравена парола?
                                </Link>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
