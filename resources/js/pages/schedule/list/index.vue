<script setup>
import { useEmployeeStore } from '@/stores/EmployeeStore'
import { useScheduleStore } from '@/stores/ScheduleStore'
import { useTeamStore } from '@/stores/TeamStore'
import AddNewScheduleDrawer from '@/views/pages/schedule/AddNewScheduleDrawer.vue'
import { computed, nextTick, onMounted, reactive, ref, watch } from 'vue'
import { VDataTable } from 'vuetify/labs/VDataTable'

// import { useUserListStore } from '@/views/apps/user/useUserListStore'

const scheduleStore = useScheduleStore()
const employeeStore = useEmployeeStore()
const teamStore = useTeamStore()

// const userListStore = useUserListStore()

const searchQuery = ref('')

// 👉 search filters
const filters = reactive({
  day: null,
  team: null,
  starth: null,
  endh: null,
})

// Headers
const headers = [
  {
    title: 'Personne Concerné',
    key: 'employee.mtle',
    sortable: false,
    fixed: true,
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
    sortable: false,
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

// Watchers

watch(() => filters, (newValue, oldValue) => {

  if(newValue.team)
    scheduleStore.fetchSchedulesWithFilters(newValue)
  else
    scheduleStore.fetchSchedules()


}, { deep: true })

// Computed
const schedules = computed(() => {
  return scheduleStore.$state.scheduleList
})

const schedulesLoading = computed(() => {
  return scheduleStore.$state.isLoading
})

const teams = computed(() => {
  return teamStore.$state.teamList
})

// Life cycle Hooks
onMounted(async () => {
  await scheduleStore.fetchSchedules()

  if(!employeeStore.$state.employeeList.length)
    employeeStore.fetchEmployees()
  if(!teamStore.$state.teamList.length)
    teamStore.fetchTeams()
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
                  v-model="filters.team"
                  label="Team"
                  :items="teams"
                  item-title="label"
                  item-value="id"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <!-- 👉 Date Range -->
              <VCol
                cols="12"
                sm="4"
              >
                <AppDateTimePicker
                  v-model="filters.day"
                  clearable
                  label="Jour"
                />
              </VCol>
              <!-- 👉 Time Range -->
              <VCol
                cols="6"
                sm="2"
              >
                <AppDateTimePicker
                  v-model="filters.starth"
                  label="Débute entre"
                  clearable
                  :config="{enableTime: true, noCalendar: true, dateFormat: 'H:i'}"
                />
              </VCol>
              <VCol
                cols="6"
                sm="2"
              >
                <AppDateTimePicker
                  v-model="filters.endh"
                  label="Et"
                  clearable
                  :config="{enableTime: true, noCalendar: true, dateFormat: 'H:i'}"
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

              <!--
                👉 Export button
                <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
                >
                Export
                </VBtn> 
              -->

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
