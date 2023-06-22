<script setup>
import { useStatsStore } from '@/stores/StatsStore'
import { computed, onMounted, onUnmounted, ref } from 'vue'

const statsStore = useStatsStore()

const dashboadStats = computed(() => {
  return statsStore.$state.dashboard
})

let userListMeta = ref()


// Methods
const refreshValue = () => {
  userListMeta.value = [
    {
      icon: 'tabler-user',
      color: 'primary',
      title: 'Production',
      stats: dashboadStats.value.awaitToday,

      percentage: +100,
      subtitle: 'Total Prévus',
    },
    {
      icon: 'tabler-user-check',
      color: 'success',
      title: 'Production',
      stats: dashboadStats.value.presentToday,
      percentage: -14,
      subtitle: 'Présents',
    },
    {
      icon: 'tabler-user-exclamation',
      color: 'warning',
      title: 'Production',
      stats: dashboadStats.value.lateToday,
      percentage: +42,
      subtitle: 'Retards',
    },
    {
      icon: 'tabler-user-off',
      color: 'primary',
      title: 'Production',
      stats: dashboadStats.value.offToday,
      percentage: +42,
      subtitle: 'Agents OFF',
    },
    {
      icon: 'tabler-user-pause',
      color: 'secondary',
      title: 'Pauses',
      stats: dashboadStats.value.totalBreakTime,
      percentage: +18,
      subtitle: 'Total des pauses',
    },
    {
      icon: 'tabler-user-cancel',
      color: 'warning',
      title: 'Pauses',
      stats: dashboadStats.value.currentlyInBreaktime,
      percentage: +18,
      subtitle: 'En pause actuellement',
    },
    {
      icon: 'tabler-users-group',
      color: 'primary',
      title: 'Agents',
      stats: dashboadStats.value.totalEmployee,
      percentage: +18,
      subtitle: 'Total Employés',
    },
    {
      icon: 'tabler-plane-inflight',
      color: 'primary',
      title: 'Agents',
      stats: dashboadStats.value.onHolyday,
      percentage: +18,
      subtitle: 'En Congés',
    },

  // {
  //   icon: 'tabler-user-minus',
  //   color: 'error',
  //   title: 'Production',
  //   stats: '4,567',
  //   percentage: +18,
  //   subtitle: 'Absents',
  // },
  ]
}


onMounted(async () => {
  await statsStore.fetchDashboardStats()
  refreshValue()
})

onUnmounted(() => {
  clearInterval(dashboardInterval)
})
let dashboardInterval = setInterval(() => {
  statsStore.fetchDashboardStats()
  refreshValue()
}, 10000)
</script>

<template>
  <div>
    <!--
      <VCard
      class="mb-6"
      title="Kick start your project 🚀"
      >
      <VCardText>All the best for your new project.</VCardText>
      <VCardText>
      Please make sure to read our <a
      href="https://demos.pixinvent.com/vuexy-vuejs-admin-template/documentation/"
      target="_blank"
      rel="noopener noreferrer"
      class="text-decoration-none"
      >
      Template Documentation
      </a> to understand where to go from here and how to use our template.
      </VCardText>
      </VCard>

      <VCard title="Want to integrate JWT? 🔒">
      <VCardText>We carefully crafted JWT flow so you can implement JWT with ease and with minimum efforts.</VCardText>
      <VCardText>Please read our  JWT Documentation to get more out of JWT authentication.</VCardText>
      </VCard> 
    -->
    <VRow>
      <VCol
        v-for="meta in userListMeta"
        :key="meta.title"
        cols="12"
        sm="6"
        lg="3"
      >
        <VCard
          v-if="dashboadStats.isLoading"
          loading="true"
        >
          <VCardText class="d-flex justify-space-between">
            <div />
          </VCardText>
        </VCard>
        <VCard v-else>
          <VCardText class="d-flex justify-space-between">
            <div>
              <span>{{ meta.title }}</span>
              <div class="d-flex align-center gap-2 my-1">
                <h6 class="text-h4">
                  {{ meta.stats }}
                </h6>
                <!-- <span :class="meta.percentage > 0 ? 'text-success' : 'text-error'">( {{ meta.percentage > 0 ? '+' : '' }} {{ meta.percentage }}%)</span> -->
              </div>
              <span>{{ meta.subtitle }}</span>
            </div>

            <VAvatar
              rounded
              variant="tonal"
              :color="meta.color"
              :icon="meta.icon"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </div>
</template>

<route lang="yaml">
  meta:
    requiresAuth: true
</route>
