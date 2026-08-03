<script setup lang="ts">
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { TESTIMONIALS } from '@/lib/demoCopy';

/**
 * Three reviews, closing the page on what other people got rather than on
 * another row of things to buy.
 *
 * The copy is written for the design document - see demoCopy - so these are the
 * first strings to replace once the store has reviews of its own.
 */
</script>

<template>
    <StorefrontContainer class="pt-9 sm:pt-12 lg:pt-14">
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li
                v-for="testimonial in TESTIMONIALS"
                :key="testimonial.author"
                class="flex flex-col gap-3 rounded-[20px] border border-border bg-card p-6"
            >
                <!-- Decorative: the quote underneath is what carries the point,
                     and five identical stars read as noise to a screen reader. -->
                <p class="tracking-[2px] text-brand-star" aria-hidden="true">
                    ★★★★★
                </p>
                <blockquote
                    class="text-[15.5px] leading-relaxed text-pretty text-foreground/85"
                >
                    {{ testimonial.quote }}
                </blockquote>
                <p
                    class="mt-auto text-[13.5px] font-bold text-muted-foreground"
                >
                    {{ testimonial.author }} · {{ testimonial.city }}
                </p>
            </li>
        </ul>
    </StorefrontContainer>
</template>
