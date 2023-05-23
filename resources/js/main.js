/* eslint-disable import/order */
import '@/@iconify/icons-bundle'
import App from '@/App.vue'
import layoutsPlugin from '@/plugins/layouts'
import vuetify from '@/plugins/vuetify'
import { loadFonts } from '@/plugins/webfontloader'
import router from '@/router'
import '@core-scss/template/index.scss'
import '@styles/styles.scss'
import { createPinia } from 'pinia'
import { createApp } from 'vue'
import { useAuthStore } from './stores/AuthStore'

loadFonts()

//load Store
const pinia = createPinia()


// Create vue app
const app = createApp(App)


// Use plugins
app.use(vuetify)
app.use(pinia)

app.use(layoutsPlugin)

const authStore = useAuthStore()

authStore.checkAuth()
app.use(router)


// Mount vue app
app.mount('#app')
