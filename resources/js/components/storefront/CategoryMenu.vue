<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
import { computed, onBeforeUnmount, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { categoryHref } from '@/lib/storefrontNav';
import type { StorefrontCategory } from '@/types';

/**
 * Opening on hover is a pointer nicety, so it is deliberately delayed a little
 * to survive a cursor crossing the trigger on its way somewhere else, and it
 * lingers on the way out so a diagonal move into the panel does not close it.
 */
const OPEN_DELAY_MS = 90;
const CLOSE_DELAY_MS = 180;

type CardLink = {
    label: string;
    href: string;
};

const page = usePage();

const categories = computed(() => page.props.storefrontCategories ?? []);

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

function closeSoon(): void {
    clearTimers();
    closeTimer = setTimeout(() => (isOpen.value = false), CLOSE_DELAY_MS);
}

function openNow(): void {
    clearTimers();
    isOpen.value = true;
}

function closeNow(): void {
    clearTimers();
    isOpen.value = false;
}

/**
 * Keyboard users tab into the trigger and then through the links, so the menu
 * only closes once focus has left the whole component.
 */
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

function filteredHref(slug: string, key: string, value: string): string {
    const params = new URLSearchParams();
    params.append(key, value);

    return `${categoryHref(slug)}?${params.toString()}`;
}

/**
 * A department that stocks handsets offers those; anything else falls back to
 * the views that apply everywhere. Either way every link lands somewhere real.
 */
function linksFor(category: StorefrontCategory): CardLink[] {
    const families = category.deviceFamilies ?? [];

    if (families.length) {
        return families.slice(0, 4).map((family) => ({
            label: family.label,
            href: filteredHref(category.slug, 'devices[]', family.value),
        }));
    }

    return [
        { label: 'Всички', href: categoryHref(category.slug) },
        { label: 'Намалени', href: filteredHref(category.slug, 'sale', '1') },
        { label: 'Нови', href: filteredHref(category.slug, 'new', '1') },
    ];
}

function productCountLabel(category: StorefrontCategory): string {
    const count = category.productCount ?? 0;

    return count === 1 ? '1 продукт' : `${count} продукта`;
}

onBeforeUnmount(clearTimers);
</script>

<template>
    <div
        ref="root"
        class="relative hidden md:block"
        @mouseenter="openSoon"
        @mouseleave="closeSoon"
        @focusin="openNow"
        @focusout="handleFocusOut"
        @keydown.esc="handleEscape"
    >
        <Button
            class="shrink-0 gap-2"
            aria-haspopup="true"
            :aria-expanded="isOpen"
            @click="isOpen ? closeNow() : openNow()"
        >
            <!-- Folds into a cross while the panel is open. -->
            <span
                class="flex h-4 w-5 flex-col justify-center gap-1.5"
                aria-hidden="true"
            >
                <span
                    class="h-0.5 w-5 origin-center bg-current transition-transform duration-300 motion-reduce:transition-none"
                    :class="isOpen && 'translate-y-[4px] rotate-45'"
                />
                <span
                    class="h-0.5 w-5 origin-center bg-current transition-transform duration-300 motion-reduce:transition-none"
                    :class="isOpen && '-translate-y-[4px] -rotate-45'"
                />
            </span>
            Категории
        </Button>

        <div
            :class="[
                'absolute top-full left-0 z-50 w-[min(90vw,34rem)] pt-3 lg:w-[min(64rem,calc(100vw-14rem))]',
                isOpen ? 'visible' : 'invisible',
            ]"
        >
            <!--
                A grid row animating between 0fr and 1fr gives the panel its
                open-to-content-height slide without measuring anything, which
                is what the original needed an animation library for.
            -->
            <div
                :class="[
                    'grid transition-[grid-template-rows,opacity] duration-[400ms] ease-out motion-reduce:transition-none',
                    isOpen
                        ? 'grid-rows-[1fr] opacity-100'
                        : 'grid-rows-[0fr] opacity-0',
                ]"
            >
                <div class="overflow-hidden">
                    <!-- The panel takes the light lavender wash and the cards
                         sit a shade deeper on top of it. -->
                    <div
                        class="rounded-xl border border-border bg-brand-surface p-3 shadow-lg"
                    >
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div
                                v-for="(category, index) in categories"
                                :key="category.id"
                                :class="[
                                    'flex min-h-[8.5rem] flex-col gap-2 rounded-lg border border-transparent bg-brand-surface-strong p-4 transition-[opacity,transform,border-color,box-shadow] duration-[400ms] ease-out hover:border-primary/40 hover:shadow-sm motion-reduce:transition-none',
                                    isOpen
                                        ? 'translate-y-0 opacity-100'
                                        : 'translate-y-3 opacity-0',
                                ]"
                                :style="{
                                    transitionDelay: isOpen
                                        ? `${80 + index * 70}ms`
                                        : '0ms',
                                }"
                            >
                                <Link
                                    :href="categoryHref(category.slug)"
                                    class="text-lg font-semibold tracking-tight text-foreground transition-colors hover:text-primary"
                                    @click="closeNow"
                                >
                                    {{ category.name }}
                                </Link>
                                <p class="text-xs text-muted-foreground">
                                    {{ productCountLabel(category) }}
                                </p>

                                <div class="mt-auto flex flex-col gap-1 pt-2">
                                    <Link
                                        v-for="link in linksFor(category)"
                                        :key="link.href"
                                        :href="link.href"
                                        class="group inline-flex items-center gap-1.5 text-sm text-foreground transition-colors hover:text-primary"
                                        @click="closeNow"
                                    >
                                        <ArrowUpRight
                                            class="size-4 shrink-0 text-primary transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 motion-reduce:transition-none"
                                        />
                                        {{ link.label }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
