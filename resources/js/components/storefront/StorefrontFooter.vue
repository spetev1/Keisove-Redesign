<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import { FOOTER_COPY } from '@/lib/demoCopy';
import { EUR_RATE, SITE_NAME } from '@/lib/siteMeta';
import {
    categoryHref,
    contactDetails,
    contactPhoneHref,
    footerHelpLinks,
    PROMOTIONS_LINK,
} from '@/lib/storefrontNav';

const page = usePage();

/**
 * The design's footer lists a handful of departments rather than the taxonomy;
 * the homepage grid is where all of it is laid out. Six plus the offer is what
 * keeps this a column instead of a second menu.
 */
const FOOTER_DEPARTMENT_COUNT = 6;

/**
 * Taken from the database rather than written out here, so adding a department to
 * the catalogue puts it in the footer too.
 */
const categoryLinks = computed(() => [
    ...(page.props.storefrontCategories ?? [])
        .slice(0, FOOTER_DEPARTMENT_COUNT)
        .map((category) => ({
            title: category.name,
            href: categoryHref(category.slug),
        })),
    PROMOTIONS_LINK,
]);

const currentYear = computed(() => new Date().getFullYear());
</script>

<template>
    <footer class="mt-10 border-t border-border bg-card sm:mt-14 lg:mt-18">
        <StorefrontContainer
            class="grid gap-7 py-7 sm:grid-cols-2 sm:py-9 lg:grid-cols-4 lg:py-12"
        >
            <div class="flex flex-col gap-3">
                <!-- `self-start` so the column's default `align-items: stretch`
                     does not pull the mark's auto width out to the full column
                     and squash it against its fixed height. -->
                <BrandWordmark class="h-16 self-start" />
                <p
                    class="max-w-[30ch] text-sm leading-relaxed text-muted-foreground"
                >
                    {{ FOOTER_COPY.blurb }}
                </p>
                <a
                    :href="contactPhoneHref"
                    class="text-[17px] font-extrabold text-foreground transition-colors hover:text-brand-highlight"
                >
                    {{ contactDetails.phone }}
                </a>
            </div>

            <div class="flex flex-col gap-2.5">
                <h2 class="text-sm font-bold">Категории</h2>
                <StorefrontNavLink
                    v-for="link in categoryLinks"
                    :key="link.title"
                    :href="link.href"
                    class="text-sm text-muted-foreground transition-colors hover:text-brand-highlight"
                >
                    {{ link.title }}
                </StorefrontNavLink>
            </div>

            <div class="flex flex-col gap-2.5">
                <h2 class="text-sm font-bold">Помощ</h2>
                <StorefrontNavLink
                    v-for="link in footerHelpLinks"
                    :key="link.title"
                    :href="link.href"
                    class="text-sm text-muted-foreground transition-colors hover:text-brand-highlight"
                >
                    {{ link.title }}
                </StorefrontNavLink>
            </div>

            <div class="flex flex-col gap-2.5">
                <h2 class="text-sm font-bold">
                    {{ FOOTER_COPY.paymentHeading }}
                </h2>
                <!--
                    How an order is paid for rather than which cards are taken -
                    cash on delivery is how most of this catalogue sells, and it
                    is not a logo anyone would recognise.
                -->
                <ul class="flex flex-wrap gap-2">
                    <li
                        v-for="method in FOOTER_COPY.paymentMethods"
                        :key="method"
                        class="flex h-[34px] items-center rounded-[9px] border border-border px-3 text-[12.5px] font-bold text-muted-foreground"
                    >
                        {{ method }}
                    </li>
                </ul>
            </div>
        </StorefrontContainer>

        <div class="border-t border-border">
            <StorefrontContainer
                class="flex flex-wrap justify-between gap-3.5 py-[18px] text-[13px] text-muted-foreground"
            >
                <p>
                    © {{ currentYear }} {{ SITE_NAME }} ·
                    {{ FOOTER_COPY.rights }}
                </p>
                <!-- Both figures the law asks for during the changeover: that
                     the price includes VAT, and the rate it converts at. -->
                <p>{{ FOOTER_COPY.vatNote }} · 1 EUR = {{ EUR_RATE }} лв.</p>
            </StorefrontContainer>
        </div>
    </footer>
</template>
