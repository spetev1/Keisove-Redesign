<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { Heart, Menu, ShoppingCart } from '@lucide/vue';
import { computed } from 'vue';
import AccountMenu from '@/components/storefront/AccountMenu.vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import CategoryMenu from '@/components/storefront/CategoryMenu.vue';
import GooeyNav from '@/components/storefront/GooeyNav.vue';
import GooeySearch from '@/components/storefront/GooeySearch.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import { categoryHref, mainNavLinks, searchHref } from '@/lib/storefrontNav';
import { home } from '@/routes';

/**
 * There is no basket yet, so the count is genuinely zero and the badge stays
 * hidden. Kept as a prop so the real cart can feed it without touching the
 * markup. The design's "2" was mockup dressing, not a starting state.
 */
type Props = {
    cartItemCount?: number;
};

withDefaults(defineProps<Props>(), {
    cartItemCount: 0,
});

const page = usePage();

const storefrontCategories = computed(
    () => page.props.storefrontCategories ?? [],
);

/**
 * The field keeps the term after the visit rather than resetting, so a search
 * that missed can be narrowed without typing it out again.
 */
function runSearch(term: string): void {
    router.visit(searchHref(term));
}
</script>

<template>
    <!--
        Carries the same ink as the announcement bar above it, so the two read
        as one dark band. Opaque on purpose: a translucent bar let the white
        page bleed through and washed the ink out to a lighter grey than the
        strip above it.
    -->
    <header
        class="border-b border-white/10 bg-brand-ink text-brand-ink-foreground"
    >
        <StorefrontContainer
            class="flex h-16 items-center gap-2 sm:gap-4 lg:gap-6"
        >
            <Sheet>
                <SheetTrigger as-child>
                    <Button
                        variant="ghost"
                        size="icon"
                        class="text-brand-ink-foreground hover:bg-white/10 hover:text-brand-ink-foreground lg:hidden"
                        aria-label="Отвори менюто"
                    >
                        <Menu />
                    </Button>
                </SheetTrigger>
                <SheetContent side="left" class="w-72 p-6">
                    <SheetHeader class="p-0">
                        <SheetTitle class="text-left">
                            <BrandWordmark class="h-8" />
                        </SheetTitle>
                    </SheetHeader>
                    <!--
                        The "Категории" menu is pointer-driven and desktop only,
                        so the sheet is where a phone reaches the departments.
                    -->
                    <nav class="mt-8 flex flex-col gap-1">
                        <p
                            class="px-3 pb-1 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Категории
                        </p>
                        <Link
                            v-for="category in storefrontCategories"
                            :key="category.id"
                            :href="categoryHref(category.slug)"
                            class="rounded-md px-3 py-2 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            {{ category.name }}
                        </Link>

                        <hr class="my-3 border-border" />

                        <StorefrontNavLink
                            v-for="link in mainNavLinks"
                            :key="link.title"
                            :href="link.href"
                            class="rounded-md px-3 py-2 text-base font-medium text-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
                        >
                            {{ link.title }}
                        </StorefrontNavLink>
                    </nav>
                </SheetContent>
            </Sheet>

            <!-- Straight onto the ink: the script carries a light outline and
                 the swoosh is green, so both hold up on the dark band. The two
                 Cyrillic lines in the artwork are near black and do fade out,
                 but at this size they are a few pixels tall and were never
                 readable. A light-on-dark variant of the logo would fix them. -->
            <Link :href="home()" class="shrink-0" aria-label="Начало">
                <BrandWordmark class="h-11 sm:h-14" />
            </Link>

            <CategoryMenu />

            <GooeyNav v-slot="{ activeIndex }" class="hidden lg:flex">
                <StorefrontNavLink
                    v-for="(link, index) in mainNavLinks"
                    :key="link.title"
                    :href="link.href"
                    data-gooey-item
                    class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors duration-300 focus-visible:outline-none motion-reduce:transition-none"
                    :class="
                        activeIndex === index
                            ? 'text-brand-ink'
                            : 'text-brand-ink-foreground/85'
                    "
                >
                    {{ link.title }}
                </StorefrontNavLink>
            </GooeyNav>

            <div class="ml-auto hidden sm:flex" role="search">
                <GooeySearch @search="runSearch" />
            </div>

            <GooeyNav v-slot="{ activeIndex }" class="ml-auto shrink-0 sm:ml-0">
                <AccountMenu data-gooey-item :is-active="activeIndex === 0" />
                <a
                    href="#"
                    data-gooey-item
                    class="hidden items-center rounded-full px-3 py-2 transition-colors duration-300 focus-visible:outline-none motion-reduce:transition-none sm:flex"
                    :class="
                        activeIndex === 1
                            ? 'text-brand-ink'
                            : 'text-brand-ink-foreground/85'
                    "
                    aria-label="Любими"
                >
                    <Heart class="size-5" />
                </a>
                <a
                    href="#"
                    data-gooey-item
                    class="relative flex items-center rounded-full px-3 py-2 transition-colors duration-300 focus-visible:outline-none motion-reduce:transition-none"
                    :class="
                        activeIndex === 2
                            ? 'text-brand-ink'
                            : 'text-brand-ink-foreground/85'
                    "
                    aria-label="Кошница"
                >
                    <ShoppingCart class="size-5" />
                    <span
                        v-if="cartItemCount > 0"
                        class="absolute -top-0.5 left-4 flex size-4 items-center justify-center rounded-full bg-primary text-[10px] font-semibold text-primary-foreground"
                    >
                        {{ cartItemCount }}
                    </span>
                </a>
            </GooeyNav>
        </StorefrontContainer>
    </header>
</template>
