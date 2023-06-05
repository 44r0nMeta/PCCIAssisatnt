<script setup>
import { useScheduleStore } from '@/stores/ScheduleStore'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'
import authV2LoginIllustrationBorderedDark from '@images/pages/auth-v2-login-illustration-bordered-dark.png'
import authV2LoginIllustrationBorderedLight from '@images/pages/auth-v2-login-illustration-bordered-light.png'
import authV2LoginIllustrationDark from '@images/pages/auth-v2-login-illustration-dark.png'
import authV2LoginIllustrationLight from '@images/pages/auth-v2-login-illustration-light.png'
import authV2MaskDark from '@images/pages/misc-mask-dark.png'
import authV2MaskLight from '@images/pages/misc-mask-light.png'
import authV1BottomShape from '@images/svg/auth-v1-bottom-shape.svg?raw'
import authV1TopShape from '@images/svg/auth-v1-top-shape.svg?raw'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { VForm } from 'vuetify/components/VForm'

const authThemeImg = useGenerateImageVariant(authV2LoginIllustrationLight, authV2LoginIllustrationDark, authV2LoginIllustrationBorderedLight, authV2LoginIllustrationBorderedDark, true)
const authThemeMask = useGenerateImageVariant(authV2MaskLight, authV2MaskDark)
const isPasswordVisible = ref(false)
const refVForm = ref()

const router = useRouter()
const scheduleStore = useScheduleStore()

const submitSuccess = reactive({
  success: false,
  message: null,
})

const bage = reactive({
  type: 'in',
  mtle: null,
})

const bageType = [
  { value: 'in', label: 'Entrée' },
  { value: 'out', label: 'Sortie' },
]


const onSubmit = async () => {
  scheduleStore.$state.submitErrors  = []
  submitSuccess.success = false
  scheduleStore.badgeSchedule(bage).then(({ data }) => {
    let heure = bage.type === 'in' ? data?.data.started_time : data?.data.ended_time
    submitSuccess.message = `Pointage effectué pour ${data?.data.employee?.firstname} ${data?.data.employee?.lastname} \n à ${heure}`
    submitSuccess.success = true
    console.log(data)
  }).catch(error => {
    scheduleStore.$state.submitErrors = error.response.data
  })

}

const loadPlanning = () => {
  if(bage.mtle)
    router.push({ name: "portal-check", params: { mtle: bage.mtle } })
  else
    alert('Renseignez votre ID svp')
}


// computed
let errors = computed(() => {
  return scheduleStore.$state.submitErrors?.errors
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
            Bienvenue sur  <span class="text-capitalize">{{ themeConfig.app.title }}</span> ! 👋🏻
          </h5>
          <p class="mb-0">
            Entrez votre ID pour pointer !
          </p>
        </VCardText>

        <!-- Success Alert  -->
        <VCardText v-if="submitSuccess.success">
          <VAlert
            color="success"
            variant="tonal"
          >
            <div class="alert-body">
              <strong> {{ submitSuccess.message }} </strong>
            </div>
          </VAlert>
        </VCardText>  
        <!-- Error Alert  -->
        <VCardText v-if="errors">
          <VAlert
            color="error"
            variant="tonal"
          >
            <div
              v-for="(error, key ,index) in errors"
              :key="index"
              class="alert-body"
            >
              <strong> - {{ error[0] }} </strong>
            </div>
          </VAlert>
        </VCardText>  
        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppSelect
                  v-model="bage.type"
                  :items="bageType"
                  item-value="value"
                  item-title="label"
                  autofocus
                  label="Flux"
                  type="text"
                />
              </VCol>

              <!-- password -->
              <VCol cols="12">
                <AppTextField
                  v-model="bage.mtle"
                  label="ID Agent"
                  type="text"
                />
              </VCol>

              <!-- remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap mt-2 mb-4">
                <RouterLink
                  to="#"
                  class="text-primary ms-2 mb-1"
                  @click="loadPlanning"
                >
                  Voir l'état de votre planning ?
                </RouterLink>
              </div>
              <VCol
                cols="12"
                class="d-flex align-center"
              >
                <!-- Bage button -->
                <VSpacer />
                <VBtn
                  block
                  type="submit"
                >
                  Pointer
                </VBtn>
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
path: /attendance/badge
</route>

