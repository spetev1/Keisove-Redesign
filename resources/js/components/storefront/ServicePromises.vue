<script setup lang="ts">
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';

/**
 * The two figures the store is judged on after the sale: how long delivery
 * takes and how long there is to change your mind. Both are the terms the live
 * store publishes, so nothing here is a number invented for the pitch.
 *
 * The figure leads and the detail follows it, because "14 дни" is what a
 * shopper is scanning for - the sentence under it is what they read once the
 * figure has caught them.
 */
type ServicePromise = {
    figure: string;
    label: string;
    detail: string;
};

const promises: ServicePromise[] = [
    {
        figure: '2-5 дни',
        label: 'Доставка със Speedy',
        detail: 'Работни дни, до адрес или до офис в цялата страна.',
    },
    {
        figure: '14 дни',
        label: 'Срок за връщане',
        detail: 'Календарни дни, считано от датата на доставката.',
    },
];
</script>

<template>
    <!--
        Full bleed and in the hero's ink, so it closes the page the way the
        brand strip opens it rather than reading as one more card in the column
        of white panels above.
    -->
    <section class="bg-brand-ink text-brand-ink-foreground">
        <StorefrontContainer class="py-10 sm:py-12">
            <ul class="grid gap-8 sm:grid-cols-2 sm:gap-12">
                <li v-for="promise in promises" :key="promise.label">
                    <p
                        class="text-3xl font-bold tracking-tight text-brand-highlight sm:text-4xl"
                    >
                        {{ promise.figure }}
                    </p>
                    <p
                        class="mt-2 text-xs font-semibold tracking-wide text-white/55 uppercase"
                    >
                        {{ promise.label }}
                    </p>
                    <p class="mt-3 max-w-sm text-sm text-white/60">
                        {{ promise.detail }}
                    </p>
                </li>
            </ul>
        </StorefrontContainer>
    </section>
</template>
