<script setup lang="ts">
import SpecularButton from '@/components/storefront/SpecularButton.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import { categoryHref } from '@/lib/storefrontNav';
</script>

<template>
    <!--
        Runs the full width of the viewport, so no square corners and no page
        gutter.
    -->
    <section
        class="relative flex min-h-[520px] items-stretch overflow-hidden bg-brand-ink text-brand-ink-foreground lg:aspect-[5/2] lg:min-h-0 lg:items-center"
    >
        <!--
            The photograph is cut to 5/2, the same ratio as the band, so from
            `lg` up it fills the frame with nothing cropped and the case sits
            with concrete all around it.

            Narrower than that the band is taller than the picture and the crop
            turns horizontal, so the frame is held near its right-hand end.
            85% is where the whole case lands in shot: any further left and the
            camera module is cut off, any further right and the case runs off
            the other edge.
        -->
        <img
            src="/images/hero/home-hero.webp"
            alt=""
            fetchpriority="high"
            class="absolute inset-0 size-full object-cover object-[85%_bottom]"
            aria-hidden="true"
        />

        <!--
            The headline falls in the shadow the photograph already has, so this
            only steadies it - enough that the shaft of light crossing the frame
            never washes out the end of a line, and no more. Pushed further it
            stops reading as concrete and turns into a flat panel.

            Narrow screens turn it on its side. The copy sits at the top of the
            band there rather than beside the case, so what needs steadying is
            the top rather than the left, and easing off towards the bottom is
            what lets the case come through underneath the copy.
        -->
        <div
            class="absolute inset-0 bg-gradient-to-b from-brand-ink/90 via-brand-ink/55 via-45% to-brand-ink/15 lg:bg-gradient-to-r lg:from-brand-ink/55 lg:via-brand-ink/25 lg:via-40% lg:to-transparent lg:to-60%"
            aria-hidden="true"
        />

        <!--
            On a phone the copy is centred and the call to action is held at the
            foot of the band, clear of the case, rather than trailing the
            paragraph. That wants the column to fill the band's height, which is
            what the flex chain here is for; from `lg` it all reverts to the
            block layout the desktop composition is built on.
        -->
        <StorefrontContainer
            class="relative flex flex-col pt-24 pb-20 lg:block lg:py-14"
        >
            <div
                class="flex max-w-xl flex-1 flex-col text-center lg:block lg:text-left"
            >
                <h1
                    class="text-4xl leading-[1.1] font-bold tracking-tight sm:text-5xl"
                >
                    Всичко за
                    <span class="block text-brand-highlight">
                        твоя телефон.
                    </span>
                </h1>

                <!--
                    Every claim here is one the live store makes: the delivery
                    price, the departments and the handsets covered. The counts
                    the copy used to lead on - twelve thousand products, forty
                    brands, next-day delivery - are published nowhere, so they
                    are not put in the client's mouth.
                -->
                <p class="mt-5 text-base font-medium text-white/85 sm:text-lg">
                    Избери по марка и модел.
                </p>

                <p class="mx-auto mt-3 max-w-lg text-sm text-white/60 lg:mx-0">
                    Калъфи, протектори, зарядни и батерии за Apple, Samsung,
                    Xiaomi, Huawei и още. Доставка за 6.95 лв.
                </p>

                <!-- `mt-auto` is what drops it to the foot of the column on a
                     phone - the band's bottom padding is what keeps it off the
                     edge. In the block layout from `lg` it has no effect, so
                     the spacing is restated there. -->
                <div class="mt-auto pt-8 lg:mt-8 lg:pt-0">
                    <!--
                        Brand colours are named, not spelled out, so the button
                        re-skins with the rest of the storefront. The fill is
                        the headline's violet; the streak is a paler wash of it,
                        broad enough to read as light falling across the edge.
                    -->
                    <SpecularButton
                        size="md"
                        :href="categoryHref('keisove')"
                        tint="var(--brand-highlight)"
                        :tint-opacity="0.3"
                        line-color="var(--brand-highlight-soft)"
                        :shine-size="20"
                        :proximity="50"
                        :follow-mouse="false"
                        auto-animate
                    >
                        Разгледай колекции
                    </SpecularButton>
                </div>
            </div>
        </StorefrontContainer>
    </section>
</template>
