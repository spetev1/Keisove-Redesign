<script setup lang="ts">
import AnnouncementBar from '@/components/storefront/AnnouncementBar.vue';
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
        <!--
            Bar and header are pinned as one band. The bar has to stay on
            screen to collapse where it can be seen; left to scroll away it
            would already be out of view by the time it folded.
        -->
        <div class="sticky top-0 z-50">
            <AnnouncementBar />
            <StorefrontHeader />
        </div>
        <main class="flex-1">
            <slot />
        </main>
        <StorefrontFooter />

        <!-- Lives in the layout so its timer survives navigation rather than
             restarting on every page. -->
        <NewsletterPopup />
    </div>
</template>
