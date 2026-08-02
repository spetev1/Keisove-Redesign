<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    index as confirmOptions,
    store as confirmStore,
} from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyConfirmationController';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Потвърди паролата',
        description:
            'Това е защитена част от сайта. Потвърди паролата си, за да продължиш.',
    },
});
</script>

<template>
    <Head title="Потвърждение на паролата" />

    <PasskeyVerify
        :routes="{
            options: confirmOptions(),
            submit: confirmStore(),
        }"
        label="Потвърди с ключ за достъп"
        loading-label="Потвърждаване..."
        separator="Или потвърди с парола"
    />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password">Парола</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                    autofocus
                />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button
                    class="w-full"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" />
                    Потвърди паролата
                </Button>
            </div>
        </div>
    </Form>
</template>
