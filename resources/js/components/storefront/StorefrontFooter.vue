<script setup lang="ts">
import { Mail, MapPin, Phone } from '@lucide/vue';
import { computed } from 'vue';
import BrandWordmark from '@/components/storefront/BrandWordmark.vue';
import PaymentMethods from '@/components/storefront/PaymentMethods.vue';
import SocialLinks from '@/components/storefront/SocialLinks.vue';
import StorefrontContainer from '@/components/storefront/StorefrontContainer.vue';
import StorefrontNavLink from '@/components/storefront/StorefrontNavLink.vue';
import {
    contactDetails,
    contactPhoneHref,
    footerLinkColumns,
} from '@/lib/storefrontNav';

const currentYear = computed(() => new Date().getFullYear());
</script>

<template>
    <footer class="mt-16 border-t border-border bg-background">
        <StorefrontContainer class="py-12">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-5 lg:gap-8">
                <div class="lg:col-span-1">
                    <BrandWordmark class="h-12" />
                    <p class="mt-4 max-w-[16rem] text-sm text-muted-foreground">
                        Кейсове и парфюми, които подчертават твоя стил.
                    </p>
                    <SocialLinks class="mt-5" />
                </div>

                <div
                    v-for="column in footerLinkColumns"
                    :key="column.heading"
                    class="lg:col-span-1"
                >
                    <h2 class="text-sm font-semibold text-foreground">
                        {{ column.heading }}
                    </h2>
                    <ul class="mt-4 space-y-3">
                        <li v-for="link in column.links" :key="link.title">
                            <StorefrontNavLink
                                :href="link.href"
                                class="text-sm text-muted-foreground transition-colors hover:text-primary"
                            >
                                {{ link.title }}
                            </StorefrontNavLink>
                        </li>
                    </ul>
                </div>

                <div class="lg:col-span-1">
                    <h2 class="text-sm font-semibold text-foreground">
                        Свържи се с нас
                    </h2>
                    <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
                        <li class="flex items-center gap-2">
                            <Phone class="size-4 shrink-0 text-primary" />
                            <a
                                :href="contactPhoneHref"
                                class="transition-colors hover:text-primary"
                            >
                                {{ contactDetails.phone }}
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <Mail class="size-4 shrink-0 text-primary" />
                            <a
                                :href="`mailto:${contactDetails.email}`"
                                class="transition-colors hover:text-primary"
                            >
                                {{ contactDetails.email }}
                            </a>
                        </li>
                        <li class="flex items-center gap-2">
                            <MapPin class="size-4 shrink-0 text-primary" />
                            {{ contactDetails.address }}
                        </li>
                    </ul>
                </div>
            </div>
        </StorefrontContainer>

        <div class="border-t border-border">
            <StorefrontContainer
                class="flex flex-col items-center gap-4 py-5 text-xs text-muted-foreground sm:flex-row sm:justify-between"
            >
                <p>© {{ currentYear }} Keisove. Всички права запазени.</p>
                <div class="flex items-center gap-5">
                    <a href="#" class="transition-colors hover:text-primary">
                        Общи условия
                    </a>
                    <a href="#" class="transition-colors hover:text-primary">
                        Политика за поверителност
                    </a>
                </div>
                <PaymentMethods />
            </StorefrontContainer>
        </div>
    </footer>
</template>
