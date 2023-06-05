<script setup>
import { useEmployeeStore } from '@/stores/EmployeeStore'
import { useScheduleStore } from '@/stores/ScheduleStore'
import AddNewScheduleDrawer from '@/views/pages/schedule/AddNewScheduleDrawer.vue'
import { computed, nextTick, onMounted } from 'vue'
import { VDataTable } from 'vuetify/labs/VDataTable'

// import { useUserListStore } from '@/views/apps/user/useUserListStore'

const scheduleStore = useScheduleStore()
const employeeStore = useEmployeeStore()

// const userListStore = useUserListStore()

const searchQuery = ref('')
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()


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
  {
    title: 'Type',
    key: 'type',
    sortable: false,
  },
  {
    title: 'Début',
    key: 'expected_start_time',
  },
  {
    title: 'Fin',
    key: 'expected_end_time',
  },
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

const plans = [
  {
    title: 'Basic',
    value: 'basic',
  },
  {
    title: 'Company',
    value: 'company',
  },
  {
    title: 'Enterprise',
    value: 'enterprise',
  },
  {
    title: 'Team',
    value: 'team',
  },
]

const status = [
  {
    title: 'En attente',
    value: 'pending',
  },
  {
    title: 'Présent',
    value: 'active',
  },
  {
    title: 'Retard',
    value: 'inactive',
  },
]


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

const isAddNewScheduleDrawerVisible = ref(false)


const deleteSchedule = schedule => {
  if(confirm('Voulez vous supprimer cette plannification ?'))
    scheduleStore.deleteSchedule(schedule).then(() => {
      scheduleStore.fetchSchedules()
    })
}

const setSelectedSchedule = schedule => {
  scheduleStore.$state.selectedSchedule = { ...schedule, employee_id: schedule?.employee?.id  }
  nextTick(() => {
    isAddNewScheduleDrawerVisible.value = true
  })
}

const callAddSchedule = () => {
  scheduleStore.resetSelectedSchedule()
  nextTick(() => {
    isAddNewScheduleDrawerVisible.value = true
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


// Computed
const schedules = computed(() => {
  return scheduleStore.$state.scheduleList
})

const schedulesLoading = computed(() => {
  return scheduleStore.$state.isLoading
})



// Life cycle Hooks
onMounted(async () => {
  await scheduleStore.fetchSchedules()

  if(!employeeStore.$state.employeeList.length)
    employeeStore.fetchEmployees()
})
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Plannification">
          <!-- 👉 Filters -->
          <VCardText title="Filtres">
            <VRow>
              <!-- 👉 Select Role -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppSelect
                  v-model="selectedRole"
                  label="Select Role"
                  :items="roles"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <!-- 👉 Select Plan -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppSelect
                  v-model="selectedPlan"
                  label="Select Plan"
                  :items="plans"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <!-- 👉 Select Status -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppSelect
                  v-model="selectedStatus"
                  label="Select Status"
                  :items="status"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
            </VRow>
          </VCardText>

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

              <!-- 👉 Add user button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="callAddSchedule"
              >
                Ajouter
              </VBtn>
              <IconBtn @click="scheduleStore.fetchSchedules()">
                <VIcon icon="tabler-refresh" />
              </IconBtn>
            </div>
          </VCardText>

          <VDivider />


          <!-- SECTION datatable -->
          <VCardText>
            <VDataTable
              :items="schedules"
              :headers="headers"
              :search="searchQuery"
              class="text-no-wrap"
              :loading="schedulesLoading"
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
                  :color="resolveScheduleStatusVariant(item.raw.status)"
                  size="small"
                  label
                  class="text-capitalize"
                >
                  {{ statusValue(item.raw.status) }}
                </VChip>
              </template> 
             

              <!-- Actions -->
              <template #item.actions="{ item }">
                <IconBtn @click="deleteSchedule(item.raw)">
                  <VIcon icon="tabler-trash" />
                </IconBtn>

                <IconBtn @click="setSelectedSchedule(item.raw)">
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

                <VListItem @click="deleteSchedule(item.raw.id)">
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

        <!-- 👉 Add New Schedule -->
        
        <AddNewScheduleDrawer v-model:isDrawerOpen="isAddNewScheduleDrawerVisible" />
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
