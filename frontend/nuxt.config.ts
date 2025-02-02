import { defineNuxtConfig } from 'nuxt/config';
import vuetify from 'vite-plugin-vuetify';
import MarkdownIt from 'markdown-it'


const md = new MarkdownIt();

export default defineNuxtConfig({
    runtimeConfig: {
        public: {
            apiBaseUrl: process.env.API_BASE_URL,
        },
    },
    server: {
        watch: {
            usePolling: true, // Включает опрос изменений
            interval: 100,    // Интервал в миллисекундах
        },
    },
    devtools: {
        enabled: true // or false to disable
    },

    compatibilityDate: '2024-11-28',
    css: [
        '@mdi/font/css/materialdesignicons.css',
        'vuetify/lib/styles/main.sass'
    ],
    build: {
        transpile: ['vuetify']
    },
    vite: {
        define: {
            'process.env.DEBUG': false
        },
        ssr: {
            noExternal: ['vuetify']
        },
        css: {
            preprocessorOptions: {
                scss: {}
            }
        },
        plugins: [
            vuetify({
                styles: true
            }),

        ]
    },
    components: {
        dirs: [
            { path: '~/components', global: true },
        ],
    },
    modules: ['@nuxtjs/i18n'],
    i18n: {
        locales: [
            {
                code: 'en',
                language: 'en-US',
                name: 'English America',
                file: 'en-us.json'
            }
        ],

        defaultLocale: 'en',
        fallbackLocale: 'en',
        langDir: 'locales/',
        lazy: true,
        legacy: false,
        strategy: 'prefix',

    },
    hooks: {
        'i18n:extend-messages'(messages, context) {
            console.log('Extending messages:', messages);
            for (const locale in messages) {
                if (messages[locale]) {
                    for (const key in messages[locale]) {
                        if (typeof messages[locale][key] === 'string') {
                            messages[locale][key] = md.render(messages[locale][key]);
                        }
                    }
                }
            }
        }
    }
});
