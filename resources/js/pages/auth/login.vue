<script setup>
import { useAuthStore } from '@/stores/AuthStore'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { VForm } from 'vuetify/components/VForm'

const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const isPasswordVisible = ref(false)
const refVForm = ref()

const creds = reactive({
  email: 'admin@pcci.sn',
  password: 'password',
  remember: false,
})

const router = useRouter()
const authStore = useAuthStore()
const route = useRoute()
let query = route.redirectedFrom?.fullPath

const login = async () => {
  await authStore.login(creds)
    .then(() => {

      router.push(route.query.redirect || '/')
    })
    .catch(error => {
      authStore.$state.authErrors = error.response.data
    })

}

// watch(() => authStore.user, newVal => {
//   if(authStore.user) {
//     router.push(route.query.redirect || '/')
//   }
// }, { immediate: true })

let errors = computed(() => {
  return authStore.errors?.errors
})
</script>

<template>
  <VRow
    no-gutters
    class="auth-wrapper bg-surface"
  >
    <VCol
      lg="8"
      class="d-none d-lg-flex"
    >
      <div class="position-relative bg-background rounded-lg w-100 ma-8 me-0">
        <div class="d-flex align-center justify-center w-100 h-100">
          <VImg
            max-width="505"
            :src="authThemeImg"
            class="auth-illustration mt-16 mb-2"
          />
        </div>

        <VImg
          :src="authThemeMask"
          class="auth-footer-mask"
        />
      </div>
    </VCol>

    <VCol
      cols="12"
      lg="4"
      class="auth-card-v2 d-flex align-center justify-center"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText>
          <VNodeRenderer
            :nodes="themeConfig.app.logo"
            class="mb-6"
          />

          <h5 class="text-h5 mb-1">
            Bienvenue sur <span class="text-capitalize"> {{ themeConfig.app.title }} </span> ! 👋🏻
          </h5>

          <p class="mb-0">
            Entrez vos identifiants pour vous connecter au panel !
          </p>
        </VCardText>

        
        
        <!--
          <VCardText>
          <VAlert
          color="primary"
          variant="tonal"
          >
          <p class="text-caption mb-2">
          Admin Email: <strong>admin@demo.com</strong> / Pass: <strong>admin</strong>
          </p>

          <p class="text-caption mb-0">
          Client Email: <strong>client@demo.com</strong> / Pass: <strong>client</strong>
          </p>
          </VAlert>
          </VCardText>   
        -->
        <VCardText v-if="errors">
          <VAlert
            color="error"
            variant="tonal"
          >
            <p
              v-for="(error, key ,index) in errors"
              :key="index"
              class="text-caption mb-1"
            >
              <strong>{{ error[0] }}</strong>
            </p>
          </VAlert>
        </VCardText>  
       
       

        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="login"
          >
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="creds.email"
                  label="Email"
                  type="email"
                  autofocus
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="creds.password"
                  label="Mot de passe"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <div class="d-flex align-center flex-wrap justify-space-between mt-2 mb-4">
                  <VCheckbox
                    v-model="creds.remember"
                    label="Se souvenir de moi"
                  />
                  <a
                    class="text-primary ms-2 mb-1"
                    href="#"
                  >
                    Mot de passe oublié ?
                  </a>
                </div>

                <VBtn
                  block
                  type="submit"
                >
                  Login
                </VBtn>
              </VCol>

              <!-- create account -->
              <!--
                <VCol
                cols="12"
                class="text-center"
                >
                <span>New on our platform?</span>

                <a
                class="text-primary ms-2"
                href="#"
                >
                Create an account
                </a>
                </VCol>

                <VCol
                cols="12"
                class="d-flex align-center"
                >
                <VDivider />

                <span class="mx-4">or</span>

                <VDivider />
                </VCol> 
              -->

              <!-- auth providers -->
              <!--
                <VCol
                cols="12"
                class="text-center"
                >
                <AuthProvider />
                </VCol> 
              -->
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
  isGuest: true
</route>
