<script setup>
import { useBreakTimeStore } from '@/stores/BreakTimeStore'
import { useEmployeeStore } from '@/stores/EmployeeStore'
import AddNewBreakTimeDrawer from '@/views/pages/breaktime/AddNewBreakTimeDrawer.vue'
import { computed, nextTick, onMounted, onUnmounted, reactive, ref, watch } from 'vue'
import { VDataTable } from 'vuetify/labs/VDataTable'

// import { useUserListStore } from '@/views/apps/user/useUserListStore'

const breakTimeStore = useBreakTimeStore()
const employeeStore = useEmployeeStore()

// const userListStore = useUserListStore()

const searchQuery = ref('')
const selectedTeam = ref()

const filters = reactive({
  day: null,
  starth: null,
  endh: null,
})

// Headers
const headers = [
  {
    title: 'Personne Concerné',
    key: 'employee.mtle',
    sortable: false,
  },
  {
    title: 'Jour',
    key: 'day',
  },

  // {
  //   title: 'Type',
  //   key: 'type',
  //   sortable: false,
  // },
  // {
  //   title: 'Début',
  //   key: 'expected_start_time',
  // },
  // {
  //   title: 'Fin',
  //   key: 'expected_end_time',
  // },
  {
    title: 'Commencé',
    key: 'started_time',
  },
  {
    title: 'Terminé',
    key: 'ended_time',
    sortable: false,
  },
  {
    title: 'Status',
    key: 'status',
    sortable: false,
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

// 👉 search filters
const roles = [
  {
    title: 'Team 3535',
    value: '3535',
  },
  {
    title: 'Team 777',
    value: '777',
  },
  {
    title: 'Team 777',
    value: '777',
  },
  {
    title: 'Team Digital',
    value: 'digit',
  },
]




const resolveBreakTimeStatusVariant = stat => {
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

const isAddNewBreakTimeDrawerVisible = ref(false)


const deleteBreakTime = breakTime => {
  if(confirm('Voulez vous supprimer cette plannification ?'))
    breakTimeStore.deleteBreakTime(breakTime).then(() => {
      breakTimeStore.fetchLiveBreakTimes()
    })
}

const setSelectedBreakTime = breakTime => {
  breakTimeStore.$state.selectedBreakTime = { ...breakTime, employee_id: breakTime?.employee?.id  }
  nextTick(() => {
    isAddNewBreakTimeDrawerVisible.value = true
  })
}

const callAddBreakTime = () => {
  breakTimeStore.resetSelectedBreakTime()
  nextTick(() => {
    isAddNewBreakTimeDrawerVisible.value = true
  })
}

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
  }
  
  return 'En Attente...'
}

// Watchers

watch(() => filters.dateRange, (newValue, oldValue) => {

  if(newValue)
    console.log(newValue)

})

// Computed
const breakTimes = computed(() => {
  return breakTimeStore.$state.breakTimeList
})

const breakTimesLoading = computed(() => {
  return breakTimeStore.$state.isLoading
})



// Life cycle Hooks
onMounted(async () => {
  await breakTimeStore.fetchLiveBreakTimes()

  if(!employeeStore.$state.employeeList.length)
    employeeStore.fetchEmployees()
})

let liveInterval = setInterval(() => {
  breakTimeStore.fetchLiveBreakTimes()
}, 10000)

onUnmounted(() => {
  clearInterval(liveInterval)
})
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Pause Live">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <VSpacer />

            <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="inline-size: 10rem;">
                <AppTextField
                  v-model="searchQuery"
                  placeholder="Recherche..."
                  density="compact"
                />
              </div>

              <!-- 👉 Export button -->
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
              >
                Export
              </VBtn>

              <!-- 👉 Add breaktime button -->
              <!--
                <VBtn
                prepend-icon="tabler-plus"
                @click="callAddBreakTime"
                >
                Ajouter
                </VBtn> 
              -->
              <IconBtn @click="breakTimeStore.fetchLiveBreakTimes()">
                <VIcon icon="tabler-refresh" />
              </IconBtn>
            </div>
          </VCardText>

          <VDivider />


          <!-- SECTION datatable -->
          <VCardText>
            <VDataTable
              :items="breakTimes"
              :headers="headers"
              :search="searchQuery"
              class="text-no-wrap"
              :loading="breakTimesLoading"
              multi-sort
            >
              <template #item.employee.mtle="{item}">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      :to="{ name: 'employee-edit-id', params: { id: item.raw.employee?.id } }"
                      class="font-weight-medium user-list-name"
                    >
                      {{ item.raw?.employee?.firstname }} {{ item.raw?.employee?.lastname }}
                    </RouterLink>
                  </h6>

                  <span class="text-sm text-medium-emphasis">@{{ item.raw.employee?.mtle }}</span>
                </div>
              </template>
              <!-- Status -->
              
              <template #item.status="{ item }">
                <VChip
                  :color="resolveBreakTimeStatusVariant(item.raw.status)"
                  size="small"
                  label
                  class="text-capitalize"
                >
                  {{ statusValue(item.raw.status) }}
                </VChip>
              </template> 
             

              <!-- Actions -->
              <template #item.actions="{ item }">
                <IconBtn @click="deleteBreakTime(item.raw)">
                  <VIcon icon="tabler-trash" />
                </IconBtn>

                <IconBtn @click="setSelectedBreakTime(item.raw)">
                  <VIcon icon="tabler-edit" />
                </IconBtn>

              <!--
                <VBtn
                icon
                variant="text"
                size="small"
                color="medium-emphasis"
                >
                <VIcon
                size="24"
                icon="tabler-dots-vertical"
                />

                
                <VMenu activator="parent">
                <VList>
                <VListItem :to="{ name: 'apps-user-view-id', params: { id: item.raw.id } }">
                <template #prepend>
                <VIcon icon="tabler-eye" />
                </template>

                <VListItemTitle>View</VListItemTitle>
                </VListItem>

                <VListItem link>
                <template #prepend>
                <VIcon icon="tabler-pencil" />
                </template>
                <VListItemTitle>Edit</VListItemTitle>
                </VListItem>

                <VListItem @click="deleteBreakTime(item.raw.id)">
                <template #prepend>
                <VIcon icon="tabler-trash" />
                </template>
                <VListItemTitle>Delete</VListItemTitle>
                </VListItem>
                </VList>
                </VMenu>
                </VBtn> 
              -->
              </template>
            </VDataTable>
          </VCardText>
          <!-- SECTION -->
        </VCard>

        <!-- 👉 Add New BreakTime -->
        
        <AddNewBreakTimeDrawer v-model:isDrawerOpen="isAddNewBreakTimeDrawerVisible" />
      </VCol>
    </VRow>
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
  meta:
    requiresAuth: true
</route>
