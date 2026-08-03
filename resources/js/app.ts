import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { SITE_NAME } from '@/lib/siteMeta';

createInertiaApp({
    // The live store tabs are separated with a pipe rather than Laravel's dash,
    // and carry the domain rather than the bare app name.
    title: (title) => (title ? `${title} | ${SITE_NAME}` : SITE_NAME),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('storefront/'):
                return StorefrontLayout;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];
            default:
                return AppLayout;
        }
    },
    progress: {
        // The brand violet rather than Laravel's grey. Spelled out because the
        // progress bar is mounted outside the app, where the theme's custom
        // properties are not in scope; it tracks --brand-highlight.
        color: '#A21CAF',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
