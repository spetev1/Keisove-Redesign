<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Search, ShoppingCart } from '@lucide/vue';
import { computed, ref } from 'vue';
import AccountMenu from '@/components/storefront/AccountMenu.vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { useScrollHysteresis } from '@/composables/useScrollHysteresis';
import {
    ALL_CATEGORIES_LINK,
    categoryHref,
    contactDetails,
    contactPhoneHref,
    PROMOTIONS_LINK,
    searchHref,
} from '@/lib/storefrontNav';
import { home } from '@/routes';

/**
 * There is no basket yet, so the count is genuinely zero. Kept as a prop so the
 * real cart can feed it without touching the markup - the design's "0" is the
 * honest starting state rather than mockup dressing.
 */
type Props = {
    cartItemCount?: number;
};

withDefaults(defineProps<Props>(), {
    cartItemCount: 0,
});

/*
  How far the page travels before the bar shrinks, and how far back up before it
  returns. The release is well below the trigger because shrinking takes height
  out of the page and the browser corrects the scroll position to compensate;
  see useScrollHysteresis.
*/
const COMPACT_AFTER_PX = 110;
const EXPAND_BEFORE_PX = 40;

const isCompact = useScrollHysteresis(COMPACT_AFTER_PX, EXPAND_BEFORE_PX);

const page = usePage();
const { isCurrentUrl } = useCurrentUrl();

/**
 * How many departments the nav row names.
 *
 * Six, which is exactly the design's row: Калъфи, Протектори, Зарядни, Слушалки,
 * Power Bank, Арабски парфюми. The taxonomy holds more, and the row is
 * deliberately not all of it - the rest are reached through the homepage grid,
 * whose own link opens everything. Naming the lot here would be the menu the
 * redesign replaced.
 */
const NAV_DEPARTMENT_COUNT = 6;

const storefrontCategories = computed(() =>
    (page.props.storefrontCategories ?? []).slice(0, NAV_DEPARTMENT_COUNT),
);

const term = ref('');

/**
 * The field keeps the term after the visit rather than resetting, so a search
 * that missed can be narrowed without typing it out again.
 */
function runSearch(): void {
    if (term.value.trim() === '') {
        return;
    }

    router.visit(searchHref(term.value.trim()));
}

/**
 * The leading tab is not a department and has no page of its own, so it stands
 * lit on the homepage - which is where the section it opens lives.
 */
const isHomepage = computed(() => isCurrentUrl(home.url()));

const tabClasses =
    'shrink-0 border-b-2 text-sm whitespace-nowrap transition-[color,border-color,padding] duration-250 motion-reduce:transition-none';
</script>

<template>
    <!--
        Pinned on its own rather than as part of a band with the announcement
        bar: the bar is a message that can be read once and scrolled past, while
        this carries the search field and the way into every department, so this
        is the part worth keeping on screen.

        Translucent over a blur, so the page reads as passing underneath it.
    -->
    <header
        class="sticky top-0 z-40 border-b border-border bg-card/90 backdrop-blur-md"
    >
        <StorefrontContainer
            :class="[
                'flex flex-wrap items-center gap-3 transition-[padding] duration-250 ease-out motion-reduce:transition-none sm:gap-5 lg:gap-7',
                isCompact ? 'py-[7px]' : 'py-3.5',
            ]"
        >
            <!-- Sized by height with the width left to follow, and transitioned
                 with the padding so the whole bar settles as one movement. -->
            <Link :href="home()" class="shrink-0" aria-label="Начало">
                <BrandWordmark
                    :class="[
                        'transition-[height] duration-250 ease-out motion-reduce:transition-none',
                        isCompact ? 'h-11' : 'h-14 sm:h-16',
                    ]"
                />
            </Link>

            <!--
                Wrapping is decided on `basis`, not on `min-w`: the field asks
                for 300px, which no phone can fit beside the logo, so it takes a
                line of its own however narrow the screen gets. Left in source
                order that pushed the account and cart group down to a *third*
                line, where it sat alone against an empty left half.

                `order-last` spends that same wrap better - the buttons ride up
                beside the logo and the bar loses a whole row.

                From `sm` up the row is wide enough to share, so the field goes
                back to asking for 300px and shrinking to 200 before it wraps.
            -->
            <form
                :class="[
                    'order-last flex basis-full items-center gap-2.5 rounded-xl border border-border bg-brand-surface-strong px-3.5 transition-[padding,border-color] duration-250 ease-out focus-within:border-brand-accent motion-reduce:transition-none sm:order-none sm:min-w-[200px] sm:flex-1 sm:basis-[300px]',
                    isCompact ? 'py-[7px]' : 'py-2.5',
                ]"
                role="search"
                @submit.prevent="runSearch"
            >
                <Search class="size-4 shrink-0 text-muted-foreground" />
                <label for="storefront-search" class="sr-only">
                    Търси в магазина
                </label>
                <input
                    id="storefront-search"
                    v-model="term"
                    type="search"
                    name="q"
                    placeholder="Търси по модел, напр. Samsung S26"
                    class="w-full bg-transparent text-[14.5px] outline-none placeholder:text-muted-foreground"
                />
            </form>

            <div class="ml-auto flex items-center gap-2.5">
                <!--
                    The number the store takes orders on. Hidden on the narrowest
                    screens, where the phone is the device itself and the field
                    beside it needs the room more.
                -->
                <a
                    :href="contactPhoneHref"
                    class="hidden flex-col pr-1.5 leading-[1.15] sm:flex"
                >
                    <span
                        class="text-[11px] font-semibold tracking-[0.06em] text-muted-foreground uppercase"
                    >
                        Поръчки
                    </span>
                    <span class="text-[15.5px] font-extrabold text-foreground">
                        {{ contactDetails.phone }}
                    </span>
                </a>

                <AccountMenu
                    :trigger-class="[
                        'transition-[height] duration-250 ease-out motion-reduce:transition-none',
                        isCompact ? 'h-[38px]' : 'h-11',
                    ]"
                />

                <!--
                    The one element on the bar painted in the brighter violet,
                    because it is the only one that is a step towards paying.
                -->
                <a
                    href="#"
                    :class="[
                        'flex shrink-0 items-center gap-2 rounded-xl bg-brand-highlight px-4 text-sm font-bold text-primary-foreground transition-[background-color,height] duration-250 ease-out hover:bg-brand-ink-hover motion-reduce:transition-none',
                        isCompact ? 'h-[38px]' : 'h-11',
                    ]"
                >
                    <ShoppingCart class="size-4 sm:hidden" />
                    <span class="hidden sm:inline">Количка</span>
                    <span
                        class="rounded-full bg-white/25 px-2 py-px text-[12.5px]"
                    >
                        {{ cartItemCount }}
                    </span>
                </a>
            </div>
        </StorefrontContainer>

        <!--
            Opaque where the bar above it is translucent, so the departments stay
            legible over whatever is scrolling past underneath.

            The row carries departments only. Their subcategories are offered as
            chips on the department's own page instead, because ten departments
            and twenty children is a menu rather than a row - and a menu is the
            thing the design replaced.

            It scrolls sideways rather than collapsing: the ten fit a desktop
            outright, and on a phone a swipe along a visible row is a shorter
            journey than opening a drawer to find the same words.
        -->
        <nav class="border-t border-border bg-card">
            <StorefrontContainer
                class="scrollbar-none flex gap-1 overflow-x-auto"
            >
                <StorefrontNavLink
                    :href="ALL_CATEGORIES_LINK.href"
                    :class="[
                        tabClasses,
                        isCompact ? 'px-2.5 py-2.5' : 'px-2.5 py-3.5',
                        isHomepage
                            ? 'border-brand-ink font-bold text-brand-ink'
                            : 'border-transparent font-semibold text-secondary-foreground hover:border-brand-accent hover:text-brand-accent-ink',
                    ]"
                >
                    {{ ALL_CATEGORIES_LINK.title }}
                </StorefrontNavLink>

                <StorefrontNavLink
                    v-for="category in storefrontCategories"
                    :key="category.id"
                    :href="categoryHref(category.slug)"
                    :class="[
                        tabClasses,
                        isCompact ? 'px-2.5 py-2.5' : 'px-2.5 py-3.5',
                        isCurrentUrl(categoryHref(category.slug))
                            ? 'border-brand-ink font-bold text-brand-ink'
                            : 'border-transparent font-semibold text-secondary-foreground hover:border-brand-accent hover:text-brand-accent-ink',
                    ]"
                >
                    {{ category.name }}
                </StorefrontNavLink>

                <!--
                    The one tab that is an offer rather than a department, so it
                    is the one tab that moves. The animation carries its own
                    colour, which is why the static class is only what reduced
                    motion falls back to.
                -->
                <StorefrontNavLink
                    :href="PROMOTIONS_LINK.href"
                    :class="[
                        tabClasses,
                        isCompact ? 'px-2.5 py-2.5' : 'px-2.5 py-3.5',
                        'inline-block animate-promo-pulse border-transparent font-bold text-brand-sale motion-reduce:animate-none',
                    ]"
                >
                    {{ PROMOTIONS_LINK.title }}
                </StorefrontNavLink>
            </StorefrontContainer>
        </nav>
    </header>
</template>
