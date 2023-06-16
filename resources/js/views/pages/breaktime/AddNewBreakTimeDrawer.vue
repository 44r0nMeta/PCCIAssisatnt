<script setup>
import AppSelect from '@/@core/components/app-form-elements/AppSelect.vue'
import { useBreakTimeStore } from '@/stores/BreakTimeStore'
import { useEmployeeStore } from '@/stores/EmployeeStore'
import { computed, nextTick } from 'vue'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
])

const employeeStore = useEmployeeStore()
const breakTimeStore = useBreakTimeStore()

const breakTimeStatus = ref([
  // { value: "A", label: "Absent" },
  { value: "P", label: "Présent" },
  { value: "R", label: "Retard" },

])

const breakTimeType = ref([
  { value: "break", label: "Pause" },

  // { value: "off", label: "Jour de Repos" },

  // { value: "pause", label: "Pause" },
])


const refForm = ref()

const submitSucces = reactive({
  success: false,
  message: null,
})

// Methods
// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    breakTimeStore.$state.submitErrors = []
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}

const addNewBreakTime = () => {
  breakTimeStore.addBreakTime(breakTimeStore.$state.selectedBreakTime).then(response => {
    submitSucces.success = true
    submitSucces.message = "Ajouter avec succès !"
    breakTimeStore.resetSelectedBreakTime()
    breakTimeStore.fetchBreakTimes()

    // refForm?.value.reset()
  }).catch(error => {
    breakTimeStore.$state.submitErrors = error.response.data
    console.log(error)
  })

  // console.log('Adding')
}

const updateABreakTime = () => {
  breakTimeStore.updateBreakTime(breakTimeStore.$state.selectedBreakTime).then(response => {
    submitSucces.success = true
    submitSucces.message = "Mise à jour reussi !"
    
    nextTick(() => {

      closeNavigationDrawer()
      breakTimeStore.resetSelectedBreakTime()
      submitSucces.success = false
      breakTimeStore.fetchBreakTimes()
    })

    // refForm?.value.reset()
  }).catch(error => {
    breakTimeStore.$state.submitErrors = error.response.data
    console.log(error)
  })

  // console.log('Adding')
}

const onSubmit = () => {
  breakTimeStore.$state.submitErrors  = []
  if(!breakTimeStore.$state.selectedBreakTime.id)
    addNewBreakTime()
  else
    updateABreakTime()

}

// Computed
const formErrors = computed(() => {
  return breakTimeStore.submitErrors?.errors
})

const employees = computed(() => {
  return employeeStore.$state.employeeList
})
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="800"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      :title="breakTimeStore.$state.selectedBreakTime.id ? 'Mise à jour de pause' : 'Nouvelle pause'"
      @cancel="closeNavigationDrawer"
    />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <!-- Errors Alert Component -->
        <VCardText v-if="formErrors">
          <VAlert
            color="error"
            variant="tonal"
          >
            <div
              v-for="(error, key ,index) in formErrors"
              :key="index"
              class="alert-body"
            >
              <strong> - {{ error[0] }} </strong>
            </div>
          </VAlert>
        </VCardText>
        <!-- Success Alert Component -->
        <VCardText v-if="submitSucces.success">
          <VAlert
            color="success"
            type="success"
            variant="tonal"
          >
            <div class="alert-body">
              <strong>{{ submitSucces.message }}</strong>
            </div>
          </VAlert>
        </VCardText>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Employee -->
              <VCol cols="12">
                <AppAutocomplete
                  v-model="breakTimeStore.$state.selectedBreakTime.employee_id"
                  :items="employees"
                  :loading="!employees"
                  item-title="firstname"
                  item-value="id"
                  label="Concerné"
                >
                  <template #item="{ props, item }">
                    <VListItem
                      v-bind="props"
                      :title="`${item?.raw?.firstname} ${item?.raw?.lastname}`"
                      :subtitle="item?.raw?.mtle"
                    />
                  </template>
                </AppAutocomplete>
              </VCol>
              <!-- 👉 Type -->
              <VCol cols="6">
                <AppSelect
                  v-model="breakTimeStore.$state.selectedBreakTime.type"
                  :items="breakTimeType"
                  item-title="label"
                  item-value="value"
                  label="Type"
                />
              </VCol>

              
              <!-- Date -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="breakTimeStore.$state.selectedBreakTime.day"
                  label="Jour"
                />
              </VCol>

              <!-- Expected Start Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="breakTimeStore.$state.selectedBreakTime.expected_start_time"
                  label="Heure début prévu"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                />
              </VCol>

              <!-- Expected End Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="breakTimeStore.$state.selectedBreakTime.expected_end_time"
                  label="Heure fin attendu"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                />
              </VCol>

              <!-- Start Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="breakTimeStore.$state.selectedBreakTime.started_time"
                  label="Heure commencé"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                  clearable
                />
              </VCol>

              <!-- Ended Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="breakTimeStore.$state.selectedBreakTime.ended_time"
                  label="Heure terminé"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                  clearable
                />
              </VCol>

              <!-- Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="breakTimeStore.$state.selectedBreakTime.status"
                  :items="breakTimeStatus"
                  item-title="label"
                  item-value="value"
                  label="Status"
                  clearable
                />
              </VCol>

              <!-- Memo -->
              <VCol cols="12">
                <AppTextarea
                  v-model="breakTimeStore.$state.selectedBreakTime.memo"
                  label="Notes"
                  auto-grow
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Enregistrer
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Annuler
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
