<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, reactive } from 'vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { categoryHref } from '@/lib/storefrontNav';
import type {
    CategoryFilterState,
    CategoryPriceBounds,
    CategorySortOption,
    ProductFacet,
} from '@/types';

type Props = {
    categorySlug: string;
    filters: CategoryFilterState;
    brands: ProductFacet[];
    deviceFamilies: ProductFacet[];
    caseTypes: ProductFacet[];
    priceBounds: CategoryPriceBounds;
};

/** The checkbox groups differ only by which key they write to. */
type FacetKey = 'brands' | 'devices' | 'types';

const props = defineProps<Props>();

const emit = defineEmits<{
    applied: [];
}>();

const SORT_LABELS: Record<CategorySortOption, string> = {
    default: 'Подредба по подразбиране',
    price_asc: 'Цена: ниска към висока',
    price_desc: 'Цена: висока към ниска',
    newest: 'Първо новите',
};

const state = reactive<CategoryFilterState>({
    ...props.filters,
    brands: [...props.filters.brands],
    devices: [...props.filters.devices],
    types: [...props.filters.types],
});

const facetGroups = computed<
    { key: FacetKey; legend: string; options: ProductFacet[] }[]
>(() => [
    {
        key: 'brands',
        legend: 'Марка',
        options: props.brands,
    },
    {
        key: 'devices',
        legend: 'За кой телефон',
        options: props.deviceFamilies,
    },
    {
        key: 'types',
        legend: 'Вид кейс',
        options: props.caseTypes,
    },
]);

let debounce: ReturnType<typeof setTimeout> | undefined;

/**
 * Only non-default values reach the URL, so a bare department keeps a clean
 * shareable address and the "clear" button has something honest to react to.
 */
function urlForState(): string {
    const params = new URLSearchParams();

    if (state.q) {
        params.set('q', state.q);
    }

    // The bracketed names are what make Laravel read these back as arrays.
    state.brands.forEach((value) => params.append('brands[]', value));
    state.devices.forEach((value) => params.append('devices[]', value));
    state.types.forEach((value) => params.append('types[]', value));

    if (state.minPrice !== null) {
        params.set('min', String(state.minPrice));
    }

    if (state.maxPrice !== null) {
        params.set('max', String(state.maxPrice));
    }

    if (state.onSale) {
        params.set('sale', '1');
    }

    if (state.isNew) {
        params.set('new', '1');
    }

    if (state.sort !== 'default') {
        params.set('sort', state.sort);
    }

    const query = params.toString();
    const base = categoryHref(props.categorySlug);

    return query ? `${base}?${query}` : base;
}

function apply(): void {
    clearTimeout(debounce);

    router.visit(urlForState(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => emit('applied'),
    });
}

/** Typing should not fire a request per keystroke. */
function applySoon(): void {
    clearTimeout(debounce);
    debounce = setTimeout(apply, 400);
}

function toggleFacet(key: FacetKey, value: string, checked: boolean): void {
    state[key] = checked
        ? [...state[key], value]
        : state[key].filter((current) => current !== value);

    apply();
}

function setPrice(key: 'minPrice' | 'maxPrice', raw: string): void {
    const parsed = Number.parseInt(raw, 10);

    state[key] = Number.isNaN(parsed) ? null : Math.max(0, parsed);
    applySoon();
}

function clearAll(): void {
    state.q = null;
    state.brands = [];
    state.devices = [];
    state.types = [];
    state.minPrice = null;
    state.maxPrice = null;
    state.onSale = false;
    state.isNew = false;
    state.sort = 'default';

    apply();
}

const hasActiveFilters = computed(
    () =>
        Boolean(state.q) ||
        state.brands.length > 0 ||
        state.devices.length > 0 ||
        state.types.length > 0 ||
        state.minPrice !== null ||
        state.maxPrice !== null ||
        state.onSale ||
        state.isNew ||
        state.sort !== 'default',
);

onBeforeUnmount(() => clearTimeout(debounce));
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between gap-2">
            <h2 class="text-sm font-semibold text-foreground">Филтри</h2>
            <Button
                v-if="hasActiveFilters"
                variant="ghost"
                size="sm"
                class="h-auto px-2 py-1 text-xs"
                @click="clearAll"
            >
                Изчисти
            </Button>
        </div>

        <div class="flex flex-col gap-2">
            <Label for="category-search" class="text-xs text-muted-foreground">
                Търсене
            </Label>
            <Input
                id="category-search"
                :model-value="state.q ?? ''"
                type="search"
                placeholder="Например S26 Ultra"
                class="h-9"
                @update:model-value="
                    (value) => {
                        state.q = String(value) || null;
                        applySoon();
                    }
                "
            />
        </div>

        <div class="flex flex-col gap-2">
            <Label for="category-sort" class="text-xs text-muted-foreground">
                Подреди по
            </Label>
            <Select
                :model-value="state.sort"
                @update:model-value="
                    (value) => {
                        state.sort = value as CategorySortOption;
                        apply();
                    }
                "
            >
                <SelectTrigger id="category-sort" class="h-9 w-full">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="(label, value) in SORT_LABELS"
                        :key="value"
                        :value="value"
                    >
                        {{ label }}
                    </SelectItem>
                </SelectContent>
            </Select>
        </div>

        <!--
            A group is only rendered where the department has something to put
            in it: perfumes fit no handset, only a case has a construction, and
            a department carrying a single brand is offered no brand filter.
        -->
        <fieldset
            v-for="group in facetGroups"
            :key="group.key"
            v-show="group.options.length"
            class="flex flex-col gap-2"
        >
            <legend class="mb-2 text-xs text-muted-foreground">
                {{ group.legend }}
            </legend>
            <div
                v-for="option in group.options"
                :key="option.value"
                class="flex items-center gap-2"
            >
                <Checkbox
                    :id="`${group.key}-${option.value}`"
                    :model-value="state[group.key].includes(option.value)"
                    @update:model-value="
                        (checked) =>
                            toggleFacet(
                                group.key,
                                option.value,
                                checked === true,
                            )
                    "
                />
                <Label
                    :for="`${group.key}-${option.value}`"
                    class="flex flex-1 cursor-pointer items-center justify-between gap-2 text-sm font-normal"
                >
                    {{ option.label }}
                    <span class="text-xs text-muted-foreground">
                        {{ option.count }}
                    </span>
                </Label>
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-2">
            <legend class="mb-2 text-xs text-muted-foreground">
                Цена (лв.)
            </legend>
            <div class="flex items-center gap-2">
                <Input
                    :model-value="state.minPrice ?? ''"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    class="h-9"
                    :placeholder="String(priceBounds.min)"
                    aria-label="Най-ниска цена"
                    @update:model-value="
                        (value) => setPrice('minPrice', String(value))
                    "
                />
                <span class="text-xs text-muted-foreground">-</span>
                <Input
                    :model-value="state.maxPrice ?? ''"
                    type="number"
                    inputmode="numeric"
                    min="0"
                    class="h-9"
                    :placeholder="String(priceBounds.max)"
                    aria-label="Най-висока цена"
                    @update:model-value="
                        (value) => setPrice('maxPrice', String(value))
                    "
                />
            </div>
        </fieldset>

        <fieldset class="flex flex-col gap-3">
            <legend class="mb-2 text-xs text-muted-foreground">Показвай</legend>
            <div class="flex items-center gap-2">
                <Checkbox
                    id="filter-sale"
                    :model-value="state.onSale"
                    @update:model-value="
                        (checked) => {
                            state.onSale = checked === true;
                            apply();
                        }
                    "
                />
                <Label
                    for="filter-sale"
                    class="cursor-pointer text-sm font-normal"
                >
                    Само намалени
                </Label>
            </div>
            <div class="flex items-center gap-2">
                <Checkbox
                    id="filter-new"
                    :model-value="state.isNew"
                    @update:model-value="
                        (checked) => {
                            state.isNew = checked === true;
                            apply();
                        }
                    "
                />
                <Label
                    for="filter-new"
                    class="cursor-pointer text-sm font-normal"
                >
                    Само нови
                </Label>
            </div>
        </fieldset>
    </div>
</template>
