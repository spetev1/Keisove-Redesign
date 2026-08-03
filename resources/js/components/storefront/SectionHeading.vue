<script setup lang="ts">
type Props = {
    heading: string;
    subheading?: string;
    /**
     * `lg` opens a section of the page; `md` heads a panel that already sits
     * inside one, where the same size would compete with the sections above it.
     */
    size?: 'lg' | 'md';
};

const props = withDefaults(defineProps<Props>(), {
    subheading: undefined,
    size: 'lg',
});
</script>

<template>
    <!--
        Baseline-aligned along the bottom rather than the top, so a trailing link
        sits on the same line as the heading it belongs to however many lines the
        heading runs to.
    -->
    <div class="mb-4.5 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2
                :class="[
                    'font-extrabold tracking-tight',
                    props.size === 'lg'
                        ? 'text-[23px] sm:text-2xl lg:text-[32px]'
                        : 'text-[19px] sm:text-xl lg:text-2xl',
                ]"
            >
                {{ heading }}
            </h2>
            <p
                v-if="subheading"
                class="mt-1.5 text-[15px] text-muted-foreground"
            >
                {{ subheading }}
            </p>
        </div>

        <slot name="trailing" />
    </div>
</template>
