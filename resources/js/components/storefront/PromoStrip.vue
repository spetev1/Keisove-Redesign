<script setup lang="ts">
import { Tag } from '@lucide/vue';
import { Button } from '@/components/ui/button';
</script>

<template>
    <section
        class="flex flex-col gap-4 rounded-xl border border-border bg-card p-5 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex items-start gap-3">
            <span
                class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-secondary text-primary"
            >
                <Tag class="size-4" />
            </span>
            <div>
                <h2 class="text-sm font-bold text-card-foreground">
                    Специални предложения
                </h2>
                <p class="mt-1 text-sm text-muted-foreground">
                    Открий промоции на кейсове и парфюми до -30%.
                </p>
            </div>
        </div>

        <Button as="a" href="#" variant="outline" class="shrink-0">
            Виж промоциите
        </Button>
    </section>
</template>
