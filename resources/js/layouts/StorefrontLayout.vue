<script setup lang="ts">
import AnnouncementBar from '@/components/storefront/AnnouncementBar.vue';
import BackToTop from '@/components/storefront/BackToTop.vue';
import NewsletterPopup from '@/components/storefront/NewsletterPopup.vue';
import StorefrontFooter from '@/components/storefront/StorefrontFooter.vue';
import StorefrontHeader from '@/components/storefront/StorefrontHeader.vue';
</script>

<template>
    <!--
        `text-foreground` is not redundant: <body> sits outside `.theme-light`,
        so without it every element that does not set its own colour inherits
        the dark theme's near-white text.
    -->
    <div
        class="theme-light flex min-h-screen flex-col bg-background text-foreground"
    >
        <!-- The bar is read once and scrolls away; the header below it pins
             itself and shrinks as it goes. -->
        <AnnouncementBar />
        <StorefrontHeader />

        <main class="flex-1">
            <slot />
        </main>
        <StorefrontFooter />

        <!-- Both live in the layout so their state survives navigation rather
             than resetting on every page. -->
        <NewsletterPopup />
        <BackToTop />
    </div>
</template>
