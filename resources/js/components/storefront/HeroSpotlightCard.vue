<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';

type Props = {
    eyebrow: string;
    /**
     * Set out as written lines rather than one string, because the design breaks
     * both cards' titles deliberately and a natural wrap lands elsewhere.
     */
    titleLines: string[];
    cta: string;
    href: string;
    imageUrl: string | null;
    /**
     * `plain` is the white card with a violet call to action; `accent` is the
     * green one. The pair sit one above the other, so they are given different
     * surfaces rather than being two of the same card.
     */
    tone?: 'plain' | 'accent';
};

const props = withDefaults(defineProps<Props>(), {
    tone: 'plain',
});
</script>

<template>
    <!--
        Whole tile is the link, so the call to action inside it is a span the
        card's own hover drives rather than an anchor nested in an anchor.
    -->
    <Link
        :href="href"
        :class="[
            'group flex min-h-[180px] flex-1 flex-col justify-between overflow-hidden rounded-3xl border p-[22px] transition-colors',
            props.tone === 'accent'
                ? 'border-brand-accent-border bg-brand-accent-surface hover:bg-brand-accent-surface-hover'
                : 'border-border bg-card hover:border-brand-accent',
        ]"
    >
        <div>
            <p
                :class="[
                    'text-xs font-extrabold tracking-[0.08em] uppercase',
                    props.tone === 'accent'
                        ? 'text-brand-accent-ink'
                        : 'text-brand-accent',
                ]"
            >
                {{ eyebrow }}
            </p>
            <p
                :class="[
                    'mt-1.5 text-[19px] leading-tight font-extrabold tracking-tight lg:text-[23px]',
                    props.tone === 'accent'
                        ? 'text-brand-accent-ink-strong'
                        : 'text-card-foreground',
                ]"
            >
                <template v-for="(line, index) in titleLines" :key="line">
                    <br v-if="index > 0" />{{ line }}
                </template>
            </p>
        </div>

        <div class="flex items-end justify-between gap-3">
            <span
                :class="[
                    'inline-flex items-center gap-1.5 text-sm font-bold',
                    props.tone === 'accent'
                        ? 'text-brand-accent-ink'
                        : 'text-brand-highlight',
                ]"
            >
                {{ cta }}
                <ArrowRight
                    class="size-4 transition-transform duration-300 ease-out group-hover:translate-x-1 motion-reduce:transition-none"
                />
            </span>

            <!--
                The perfume shot is photographed on white, so on the green card it
                is multiplied into the surface rather than sitting on a white
                square the design does not draw.
            -->
            <img
                v-if="imageUrl"
                :src="imageUrl"
                alt=""
                aria-hidden="true"
                loading="lazy"
                :class="[
                    'size-23 shrink-0 object-contain transition-transform duration-500 ease-out group-hover:scale-105 motion-reduce:transition-none',
                    props.tone === 'accent'
                        ? 'rounded-[14px] mix-blend-multiply lg:size-30'
                        : 'rounded-xl bg-brand-surface',
                ]"
            />
        </div>
    </Link>
</template>
