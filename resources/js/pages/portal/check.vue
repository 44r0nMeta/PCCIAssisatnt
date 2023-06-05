<script setup>
import { useScheduleStore } from '@/stores/ScheduleStore'
import { setInterval } from 'timers'
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'


const route = useRoute()
const router = useRouter()
const scheduleStore = useScheduleStore()


// Methods
const statusValue = key => {
  switch (key) {
  case 'P':
    return 'Présent'
    break
  case 'A':
    return 'Absent'
    break
  case 'R':
    return 'Retard'
    break
  case 'AR':
    return 'Arrivé Retardé'
    break
  case 'OFF':
    return 'OFF'
    break
  case 'DA':
    return 'Depart Anticipé'
    break
  case 'AJ':
    return 'Absence Justifié'
    break
  case'AP':
    return 'Absence payé'
    break
  }
  
  return 'En Attente...'
}

const resolveScheduleStatusVariant = stat => {
  if (!stat)
    return "inactive"
    
  const statLowerCase = stat.toLowerCase()
  if (statLowerCase === 'a')
    return 'error'
  if (statLowerCase === 'p')
    return 'success'
  if (statLowerCase === 'r')
    return 'warning'
  
  return 'primary'
}


// Computed
const plannings = computed(() =>  {
  return scheduleStore.$state.planningCheckList
})


const isLoading = computed(() => {
  return scheduleStore.$state.isLoading
})

onMounted(async () => {
  console.log(route.params?.mtle)
  await scheduleStore.checkAttendance(route.params?.mtle).catch(error => {
    if(error.response.status === 404)
      router.push('/notfound')
  })
  setInterval(() => {
    console.log('Tick')
  }, 100)
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard v-if="isLoading">
        <!-- Display When loading -->
        <VProgressCircular
        
          :size="60"
          color="primary"
          indeterminate
        />
      </VCard>
      <VCard
        v-else
        :title="`Planning ${route.params?.mtle}`"
      >
        <VCardText>
          <VTable class="text-no-wrap">
            <thead>
              <tr>
                <th class="text-uppercase">
                  Jour
                </th>
                <th class="text-uppercase">
                  Type
                </th>
                <th class="text-uppercase">
                  Heure début prévu
                </th>
                <th class="text-uppercase">
                  Heure fin prévu
                </th>
                <th class="text-uppercase">
                  Heure commencée
                </th>
                <th class="text-uppercase">
                  Heure terminé
                </th>
                <th class="text-uppercase">
                  Status
                </th>
              </tr>
            </thead>

            <tbody>
              <template v-if="plannings.length > 0">
                <tr
                  v-for="planning in plannings"
                  :key="planning.id"
                >
                  <td>{{ planning.day }}</td>
                  <td>{{ planning.type.toUpperCase() }}</td>
                  <td>{{ planning.expected_start_time }}</td>
                  <td>{{ planning.expected_end_time }}</td>
                  <td>{{ planning.started_time || '-' }}</td>
                  <td>{{ planning.ended_time || '-' }}</td>
                  <td>
                    <VChip
                      :color="resolveScheduleStatusVariant(planning.status)"
                      size="small"
                      label
                      class="text-capitalize"
                    >
                      {{ statusValue(planning.status) }}
                    </VChip>
                  </td>
                </tr>
              </template>
              <tr v-else>
                <!--
                  <td>
                  {{ item.dessert }}
                  </td>
                  <td class="text-center">
                  {{ item.calories }}
                  </td>
                  <td class="text-center">
                  {{ item.just }}
                  </td> 
                -->
                <td
                  colspan="7"
                  align="center"
                >
                  Aucune données trouvées
                </td>
              </tr>
            </tbody>
          </VTable>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
  meta:
    layout: blank
    isGuest: true
  path: /attendance/check/:mtle
  </route>
  