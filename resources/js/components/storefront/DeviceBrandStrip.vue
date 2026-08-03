<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import type { CSSProperties } from 'vue';
import SectionHeading from '@/components/storefront/SectionHeading.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { BRANDS_COPY } from '@/lib/demoCopy';
import type { DeviceBrand } from '@/types';

/**
 * Every handset the store cuts cases for, as a panel of chips that each open the
 * cases department already narrowed to that family.
 *
 * They come from the same enum the category filters are built from, so the panel
 * cannot end up offering a handset the filters do not.
 *
 * The marks come from Simple Icons, whose icon files are released CC0 - the
 * trademarks themselves stay with the manufacturers, and what each one permits
 * of a retailer is the client's to confirm. They are single-path and monochrome,
 * so they are painted as masks rather than dropped in as pictures: eight logos
 * in their own house colours would fight each other and the panel they sit on,
 * where one ink reads as a set.
 */
type Props = {
    families: DeviceBrand[];
    /** Where the trailing chip goes - the department with no family applied. */
    allHref: string;
    /** Built by the caller, so the panel does not need to know the department. */
    hrefFor: (family: DeviceBrand) => string;
};

const props = defineProps<Props>();

/** How tall a mark is drawn, in pixels, where it needs to differ. */
const MARK_SIZES: Record<string, number> = {
    samsung: 44,
    honor: 40,
};

const DEFAULT_MARK_SIZE = 28;

/**
 * The mark is painted rather than placed: the element carries the colour and the
 * file only says which shape of it to keep. Named after the handset's own slug,
 * so a family and its mark cannot drift apart.
 *
 * Every mark is drawn on the same square canvas, so a wide wordmark takes up a
 * thin band of it and comes out far lighter than a symbol filling the square.
 * The two that need it are drawn larger *within* the chip rather than being
 * given a larger chip - the chips have to stay one size, or a taller one drops
 * its name below the rest of the row.
 *
 * The sizes above are what a mark is drawn at full size; the chip sets
 * `--mark-scale` and every mark shrinks by the same proportion, which keeps the
 * relationship between them - Samsung stays the wider one - at any size.
 */
function markStyle(family: DeviceBrand): CSSProperties {
    const size = MARK_SIZES[family.value] ?? DEFAULT_MARK_SIZE;
    const drawn = `calc(${size}px * var(--mark-scale, 1))`;
    const mask = `url(/images/brands/${family.value}.svg) center / ${drawn} ${drawn} no-repeat`;

    return { mask, WebkitMask: mask };
}

const chipClasses =
    'flex h-[62px] flex-col items-center justify-center gap-1 rounded-[13px] border border-border transition-colors';
</script>

<template>
    <!--
        `id` is the target the hero's "Намери по модел" jumps to, so the button
        and this panel have to keep the same name.
    -->
    <StorefrontContainer id="marki" class="pt-9 sm:pt-12 lg:pt-14">
        <div class="rounded-3xl border border-border bg-card p-5 sm:p-6 lg:p-7">
            <SectionHeading :heading="BRANDS_COPY.heading" size="md">
                <template #trailing>
                    <span class="text-sm text-muted-foreground">
                        {{ BRANDS_COPY.supportedCount }}
                    </span>
                </template>
            </SectionHeading>

            <ul
                class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 lg:grid-cols-6 xl:grid-cols-7"
            >
                <li v-for="family in props.families" :key="family.value">
                    <Link
                        :href="props.hrefFor(family)"
                        :class="[
                            chipClasses,
                            'group text-secondary-foreground hover:border-brand-accent hover:bg-brand-accent-surface-faint',
                        ]"
                    >
                        <!--
                            The mark is hidden from screen readers because the
                            name below it is already saying the same thing.
                        -->
                        <span
                            class="block h-7 w-full bg-muted-foreground transition-colors [--mark-scale:0.9] group-hover:bg-brand-accent-ink"
                            :style="markStyle(family)"
                            aria-hidden="true"
                        />
                        <span class="text-[14.5px] leading-none font-bold">
                            {{ family.label }}
                        </span>
                    </Link>
                </li>

                <!-- The way out of the panel, painted in the violet so it reads
                     as a link rather than as one more handset. -->
                <li>
                    <Link
                        :href="props.allHref"
                        :class="[
                            chipClasses,
                            'text-sm font-bold text-brand-highlight hover:border-brand-highlight',
                        ]"
                    >
                        <span class="inline-flex items-center gap-1.5">
                            {{ BRANDS_COPY.allLabel }}
                            <ArrowRight class="size-4" />
                        </span>
                    </Link>
                </li>
            </ul>
        </div>
    </StorefrontContainer>
</template>
