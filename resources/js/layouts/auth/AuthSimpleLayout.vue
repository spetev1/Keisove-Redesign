<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from '@lucide/vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import { Button } from '@/components/ui/button';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <!--
        Held to the light theme for the same reason the storefront is: signing
        in is part of the demo the client is shown, and the wordmark is artwork
        with near-black lettering in it, which a dark page would swallow.
    -->
    <div
        class="theme-light flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 text-foreground md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <!-- The client's own mark, the same component the
                         storefront header renders, so re-skinning stays the
                         one-file change it is there. -->
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                        aria-label="Начало"
                    >
                        <BrandWordmark class="mb-1 h-14" />
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />

                <!--
                    The way back out. The wordmark above already leads home,
                    but nothing about a logo says so, and someone who opened
                    the sign-in page by accident should not have to reach for
                    the browser's back button. Ghost rather than filled: the
                    form's own button is what the page is asking for.
                -->
                <Button
                    variant="ghost"
                    size="sm"
                    as-child
                    class="mx-auto text-muted-foreground hover:text-foreground"
                >
                    <Link :href="home()">
                        <ArrowLeft />
                        Назад към сайта
                    </Link>
                </Button>
            </div>
        </div>
    </div>
</template>
