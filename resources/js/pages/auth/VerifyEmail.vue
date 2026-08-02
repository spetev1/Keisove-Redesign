<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Потвърждение на имейла',
        description:
            'Потвърди имейл адреса си от линка, който току-що ти изпратихме.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Потвърждение на имейла" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        Изпратихме нов линк за потвърждение на имейла, който посочи при
        регистрацията.
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary">
            <Spinner v-if="processing" />
            Изпрати нов линк
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm">
            Изход
        </TextLink>
    </Form>
</template>
