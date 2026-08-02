<script setup lang="ts">
import { Search } from '@lucide/vue';
import { computed, nextTick, ref, useId, watch } from 'vue';
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

type Props = {
    placeholder?: string;
    /** Diameter in px of the closed circle. Matches the h-10 track. */
    collapsedWidth?: number;
    /** Pill width in px once the field is open. */
    expandedWidth?: number;
    /** How far left the pill slides, clearing room for the icon bubble. */
    expandedOffset?: number;
    gooeyBlur?: number;
    disabled?: boolean;
    class?: HTMLAttributes['class'];
};

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Търсете продукти...',
    collapsedWidth: 40,
    expandedWidth: 560,
    expandedOffset: 52,
    gooeyBlur: 5,
    disabled: false,
});

const emit = defineEmits<{
    search: [value: string];
}>();

const searchText = defineModel<string>({ default: '' });

const isExpanded = ref(false);
const inputRef = ref<HTMLInputElement | null>(null);

/*
  The filter is referenced by id, so two of these on one page would otherwise
  share (and fight over) a single definition.
*/
const filterId = `gooey-search-${useId().replace(/[^a-zA-Z0-9-]/g, '')}`;

/*
  The open field is clamped against the viewport as well as its own width, so
  on a narrow header it stops short of the wordmark instead of running past it.
  The reserve covers the icon bubble, the logo and the container gutters.
*/
const pillStyle = computed(() => ({
    width: isExpanded.value
        ? `min(${props.expandedWidth}px, calc(100vw - 14rem))`
        : `${props.collapsedWidth}px`,
    marginRight: `${isExpanded.value ? props.expandedOffset : 0}px`,
}));

/*
  An overshooting curve stands in for the spring the React original animates
  with, so the effect needs no animation library.
*/
const springy =
    'duration-[400ms] ease-[cubic-bezier(0.34,1.56,0.64,1)] motion-reduce:transition-none';

/*
  One flat, opaque surface. Rings and shadows smear under the goo filter, so
  the circle and the bubble carry colour only.
*/
const surface = 'bg-primary text-primary-foreground';

async function expand(): Promise<void> {
    if (props.disabled) {
        return;
    }

    isExpanded.value = true;
    await nextTick();
    inputRef.value?.focus();
}

function collapseWhenEmpty(): void {
    if (!searchText.value) {
        isExpanded.value = false;
    }
}

function submit(): void {
    if (searchText.value) {
        emit('search', searchText.value);
    }
}

function cancel(): void {
    searchText.value = '';
    isExpanded.value = false;
}

watch(isExpanded, (expanded) => {
    if (!expanded) {
        searchText.value = '';
    }
});
</script>

<template>
    <!--
        Only ever occupies the closed circle in the layout. The field grows out
        of it absolutely, so opening the search never reflows the header.
    -->
    <div :class="cn('relative z-10 size-10 shrink-0', props.class)">
        <!--
            The goo: blur the shapes together, push the alpha through a steep
            ramp so the blurred edges snap back into a solid outline, then lay
            the untouched graphic back on top so the text and icons stay sharp.
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
                        :stdDeviation="gooeyBlur"
                        result="blur"
                    />
                    <feColorMatrix
                        in="blur"
                        type="matrix"
                        values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -10"
                        result="goo"
                    />
                    <feComposite in="SourceGraphic" in2="goo" operator="atop" />
                </filter>
            </defs>
        </svg>

        <!--
            Pinned to the right edge and left free to size itself, so the pill
            grows leftwards into the header's empty middle rather than off the
            end of the page.
        -->
        <div
            class="absolute top-0 right-0 flex h-10 items-center"
            :style="{ filter: `url(#${filterId})` }"
        >
            <div
                :class="
                    cn(
                        'relative flex h-10 items-center justify-center overflow-hidden rounded-full transition-[width,margin-right]',
                        springy,
                        surface,
                    )
                "
                :style="pillStyle"
            >
                <Search
                    v-if="!isExpanded"
                    class="size-4 shrink-0"
                    aria-hidden="true"
                />

                <input
                    v-else
                    ref="inputRef"
                    v-model="searchText"
                    type="search"
                    enterkeyhint="search"
                    autocomplete="off"
                    :placeholder="placeholder"
                    :disabled="disabled"
                    aria-label="Търсене на продукти"
                    class="h-full w-full bg-transparent px-5 text-sm outline-none placeholder:text-primary-foreground/70"
                    @blur="collapseWhenEmpty"
                    @keydown.enter.prevent="submit"
                    @keydown.esc.prevent="cancel"
                />

                <!--
                    The source nests the field inside the trigger, which is
                    invalid and unreachable by keyboard. A transparent overlay
                    button does the same job while the circle is closed.
                -->
                <button
                    v-if="!isExpanded"
                    type="button"
                    :disabled="disabled"
                    class="absolute inset-0 cursor-pointer rounded-full outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-transparent disabled:pointer-events-none disabled:opacity-50"
                    aria-label="Отвори търсенето"
                    @click="expand"
                />
            </div>

            <!--
                Grows back in exactly where the closed circle sat, so opening
                reads as the circle stretching apart into two blobs rather than
                a second control appearing beside it.
            -->
            <div
                :class="
                    cn(
                        'pointer-events-none absolute top-1/2 right-0 flex size-10 -translate-y-1/2 items-center justify-center rounded-full transition-[scale,opacity]',
                        springy,
                        surface,
                        isExpanded
                            ? 'scale-100 opacity-100'
                            : 'scale-0 opacity-0',
                    )
                "
                aria-hidden="true"
            >
                <Search class="size-4" />
            </div>
        </div>
    </div>
</template>
