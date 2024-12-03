// plugins/vuetify.ts
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

export default defineNuxtPlugin((nuxtApp) => {
    const vuetify = createVuetify({
        ssr: true,
        components,
        directives,
        icons: {
            defaultSet: 'mdi', // Указываем, что используем MDI
        },
    })

    nuxtApp.vueApp.use(vuetify)
})
