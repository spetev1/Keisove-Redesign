<script setup lang="ts">
/**
 * Placeholder marks for the payment providers shown in the design. They are
 * deliberately plain rather than pixel-accurate brand logos - the real assets
 * come from whichever processor the store ends up using.
 */
</script>

<template>
    <ul class="flex items-center gap-2">
        <li
            class="flex h-6 w-10 items-center justify-center rounded border border-border bg-background text-[9px] font-bold tracking-tight text-[#1a1f71] italic"
        >
            VISA
        </li>
        <li
            class="flex h-6 w-10 items-center justify-center rounded border border-border bg-background"
            aria-label="Mastercard"
        >
            <svg viewBox="0 0 32 20" class="h-3.5" aria-hidden="true">
                <circle cx="12" cy="10" r="7" fill="#eb001b" />
                <circle cx="20" cy="10" r="7" fill="#f79e1b" opacity="0.9" />
            </svg>
        </li>
        <li
            class="flex h-6 w-10 items-center justify-center rounded border border-border bg-background text-[9px] font-semibold text-foreground"
        >
            &#63743;Pay
        </li>
    </ul>
</template>
