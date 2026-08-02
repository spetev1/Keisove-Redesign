<script setup lang="ts">
import { Mail } from '@lucide/vue';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

/**
 * The demo captures the address but has nowhere to send it yet, so the form
 * acknowledges locally rather than posting to an endpoint that does not exist.
 */
const email = ref('');
const hasSubscribed = ref(false);

function subscribe(): void {
    if (email.value.trim() === '') {
        return;
    }

    hasSubscribed.value = true;
    email.value = '';
}
</script>

<template>
    <section
        class="flex flex-col gap-4 rounded-xl border border-border bg-card p-5 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex items-start gap-3">
            <span
                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-secondary text-primary"
            >
                <Mail class="size-4" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-card-foreground">
                    Абонирай се за новини
                </h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Бъди пръв с нови продукти и оферти.
                </p>
            </div>
        </div>

        <p
            v-if="hasSubscribed"
            class="text-sm font-medium text-success sm:shrink-0"
        >
            Благодарим! Ще получаваш новините ни.
        </p>
        <form
            v-else
            class="flex w-full gap-2 sm:w-auto sm:shrink-0"
            @submit.prevent="subscribe"
        >
            <label for="newsletter-email" class="sr-only"> Вашият имейл </label>
            <Input
                id="newsletter-email"
                v-model="email"
                type="email"
                required
                placeholder="Вашият имейл"
                class="h-9 sm:w-56"
            />
            <Button type="submit" class="shrink-0">Абонирай се</Button>
        </form>
    </section>
</template>
