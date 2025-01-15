import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        ssr: true,
        components,
        directives,
        icons: {
            defaultSet: 'mdi', // Материальные иконки
        },
        theme: {
            defaultTheme: 'light',
            themes: {
                light: {
                    colors: {
                        primary: '#6200ea',
                        secondary: '#03dac6',
                    },
                },
                dark: {
                    colors: {
                        primary: '#bb86fc',
                        secondary: '#03dac6',
                    },
                },
            },
        },
    })

    nuxtApp.vueApp.use(vuetify)
})
