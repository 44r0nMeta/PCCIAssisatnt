<script setup>
import AppSelect from "@/@core/components/app-form-elements/AppSelect.vue"
import { useEmployeeStore } from "@/stores/EmployeeStore"
import { useScheduleStore } from "@/stores/ScheduleStore"
import { computed, nextTick } from "vue"
import { PerfectScrollbar } from "vue3-perfect-scrollbar"

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(["update:isDrawerOpen"])

const isSubmitProcessing = ref(false)
const employeeStore = useEmployeeStore()
const scheduleStore = useScheduleStore()

const scheduleStatus = ref([
  { value: "A", label: "Absent" },
  { value: "P", label: "Présent" },
  { value: "R", label: "Retard" },
  { value: "OFF", label: "Jour Repos" },
  { value: "AJ", label: "Absence Justifié" },
  { value: "DA", label: "Départ Anticipé" },
  { value: "AR", label: "Arrivé Retardé" },
  { value: "AP", label: "Absence Payé" },
])

const scheduleType = ref([
  { value: "prod", label: "Production" },
  { value: "off", label: "Jour de Repos" },

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
  emit("update:isDrawerOpen", false)
  nextTick(() => {
    refForm.value?.reset()
    scheduleStore.$state.submitErrors = []
  })
}

const handleDrawerModelValueUpdate = val => {
  emit("update:isDrawerOpen", val)
}

const addNewSchedule = async () => {
  isSubmitProcessing.value = true
  await scheduleStore
    .addSchedule(scheduleStore.$state.selectedSchedule)
    .then(response => {
      submitSucces.success = true
      submitSucces.message = "Ajouter avec succès !"
      scheduleStore.resetSelectedSchedule()
      scheduleStore.fetchSchedules()

      // refForm?.value.reset()
    })
    .catch(error => {
      scheduleStore.$state.submitErrors = error.response.data
      console.log(error)
    })

  // console.log('Adding')
  isSubmitProcessing.value = false
}

const updateASchedule = async () => {
  isSubmitProcessing.value = true
  await scheduleStore
    .updateSchedule(scheduleStore.$state.selectedSchedule)
    .then(response => {
      submitSucces.success = true
      submitSucces.message = "Mise à jour reussi !"

      nextTick(() => {
        closeNavigationDrawer()
        scheduleStore.resetSelectedSchedule()
        submitSucces.success = false
        scheduleStore.fetchSchedules()
      })

      // refForm?.value.reset()
    })
    .catch(error => {
      scheduleStore.$state.submitErrors = error.response.data
      console.log(error)
    })

  // console.log('Adding')
  isSubmitProcessing.value = false
}

const onSubmit = () => {
  scheduleStore.$state.submitErrors = []
  if (!scheduleStore.$state.selectedSchedule.id) addNewSchedule()
  else updateASchedule()
}

// Computed
const formErrors = computed(() => {
  return scheduleStore.submitErrors?.errors
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
      :title="
        scheduleStore.$state.selectedSchedule.id
          ? 'Mise à jour de plannification'
          : 'Nouvelle plannification'
      "
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
              v-for="(error, key, index) in formErrors"
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
                  v-model="scheduleStore.$state.selectedSchedule.employee_id"
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
                  v-model="scheduleStore.$state.selectedSchedule.type"
                  :items="scheduleType"
                  item-title="label"
                  item-value="value"
                  label="Type"
                />
              </VCol>

              <!-- Date -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="scheduleStore.$state.selectedSchedule.day"
                  label="Jour"
                />
              </VCol>

              <!-- Expected Start Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="scheduleStore.$state.selectedSchedule.expected_start_time"
                  label="Heure début prévu"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                />
              </VCol>

              <!-- Expected End Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="scheduleStore.$state.selectedSchedule.expected_end_time"
                  label="Heure fin attendu"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                />
              </VCol>

              <!-- Start Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="scheduleStore.$state.selectedSchedule.started_time"
                  label="Heure commencé"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                  clearable
                />
              </VCol>

              <!-- Ended Time -->
              <VCol cols="6">
                <AppDateTimePicker
                  v-model="scheduleStore.$state.selectedSchedule.ended_time"
                  label="Heure terminé"
                  :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
                  clearable
                />
              </VCol>

              <!-- Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="scheduleStore.$state.selectedSchedule.status"
                  :items="scheduleStatus"
                  item-title="label"
                  item-value="value"
                  label="Status"
                  clearable
                />
              </VCol>

              <!-- Memo -->
              <VCol cols="12">
                <AppTextarea
                  v-model="scheduleStore.$state.selectedSchedule.memo"
                  label="Notes"
                  auto-grow
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  :loading="isSubmitProcessing"
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
