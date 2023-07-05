<script setup>
import { useAuthStore } from "@/stores/AuthStore"
import { useGenerateImageVariant } from "@core/composable/useGenerateImageVariant"
import authV2LoginIllustrationBorderedDark from "@images/pages/auth-v2-login-illustration-bordered-dark.png"
import authV2LoginIllustrationBorderedLight from "@images/pages/auth-v2-login-illustration-bordered-light.png"
import authV2LoginIllustrationDark from "@images/pages/auth-v2-login-illustration-dark.png"
import authV2LoginIllustrationLight from "@images/pages/auth-v2-login-illustration-light.png"
import authV2MaskDark from "@images/pages/misc-mask-dark.png"
import authV2MaskLight from "@images/pages/misc-mask-light.png"
import authV1BottomShape from "@images/svg/auth-v1-bottom-shape.svg?raw"
import authV1TopShape from "@images/svg/auth-v1-top-shape.svg?raw"
import { VNodeRenderer } from "@layouts/components/VNodeRenderer"
import { themeConfig } from "@themeConfig"
import { computed } from "vue"
import { useRoute, useRouter } from "vue-router"
import { VForm } from "vuetify/components/VForm"

const authThemeImg = useGenerateImageVariant(
  authV2LoginIllustrationLight,
  authV2LoginIllustrationDark,
  authV2LoginIllustrationBorderedLight,
  authV2LoginIllustrationBorderedDark,
  true,
)

const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const isPasswordVisible = ref(false)
const refVForm = ref()

const creds = reactive({
  email: null,
  password: null,
  remember: false,
})

const router = useRouter()
const authStore = useAuthStore()
const route = useRoute()
let query = route.redirectedFrom?.fullPath

const login = async () => {
  await authStore
    .login(creds)
    .then(() => {
      router.push(route.query.redirect || "/")
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
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <div class="position-relative my-sm-16">
      <!-- 👉 Top shape -->
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1TopShape })"
        class="text-primary auth-v1-top-shape d-none d-sm-block"
      />

      <!-- 👉 Bottom shape -->
      <VNodeRenderer
        :nodes="h('div', { innerHTML: authV1BottomShape })"
        class="text-primary auth-v1-bottom-shape d-none d-sm-block"
      />

      <!-- 👉 Auth Card -->
      <VCard
        class="auth-card pa-4"
        max-width="448"
      >
        <VCardItem class="justify-center">
          <template #prepend>
            <div class="d-flex">
              <VNodeRenderer :nodes="themeConfig.app.logo" />
            </div>
          </template>

          <VCardTitle class="font-weight-bold text-capitalize text-h5 py-1">
            {{ themeConfig.app.title }}
          </VCardTitle>
        </VCardItem>

        <VCardText class="pt-1">
          <h5 class="text-h5 mb-1">
            Bienvenue sur
            <span class="text-capitalize">{{ themeConfig.app.title }}</span> ! 👋🏻
          </h5>
          <p class="mb-0">
            Entrez vos identifiants pour connecter au panel !
          </p>
        </VCardText>
        <VCardText v-if="errors">
          <VAlert
            color="error"
            variant="tonal"
          >
            <div
              v-for="(error, key, index) in errors"
              :key="index"
              class="alert-body"
            >
              <strong> - {{ error[0] }} </strong>
            </div>
          </VAlert>
        </VCardText>
        <VCardText>
          <VForm @submit.prevent="login">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppTextField
                  v-model="creds.email"
                  autofocus
                  label="Email"
                  type="email"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="creds.password"
                  label="Password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />

                <!-- remember me checkbox -->
                <div class="d-flex align-center justify-space-between flex-wrap mt-2 mb-4">
                  <VCheckbox
                    v-model="creds.remember"
                    label="Remember me"
                  />

                  <RouterLink
                    class="text-primary ms-2 mb-1"
                    :to="{ name: 'auth-login' }"
                  >
                    Forgot Password?
                  </RouterLink>
                </div>

                <!-- login button -->
                <VBtn
                  block
                  type="submit"
                >
                  Login
                </VBtn>
              </VCol>

              <VCol
                cols="12"
                class="d-flex align-center"
              >
                <VDivider />
                <VDivider />
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </div>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
  isGuest: true
</route>
