<script setup>
import AppAutocomplete from "@/@core/components/app-form-elements/AppAutocomplete.vue"
import { useEmployeeStore } from "@/stores/EmployeeStore"
import { useTeamStore } from "@/stores/TeamStore"
import { computed, onMounted, reactive, ref } from "vue"

const teamStore = useTeamStore()
const employeeStore = useEmployeeStore()
const isSubmitProcessing = ref(false)

const employee = reactive({
  mtle: "",
  firstname: "",
  lastname: "",
  gender: "",
  contract_type: "",
  email: "",
  phone: "",
  team: "",
  address: "",
  status: "A",
})

const teams = ref([])
const form = ref()

const submitSucces = reactive({
  success: false,
  message: null,
})

const genders = ["M", "F"]

const status = [
  { label: "Actif", value: "A" },
  { label: "Inactif", value: "I" },
  { label: "Congé", value: "C" },
]

const contracts = ["CDD", "CDI", "Stage", "Saisonnier"]

// Methods
const addNewEmployee = async () => {
  isSubmitProcessing.value = true
  submitSucces.success = false
  await employeeStore
    .addEmployee(employee)
    .then(response => {
      employeeStore.$state.submitErrors = []
      form.value.reset()
      submitSucces.success = true
      submitSucces.message = "Ajouter avec succès !"
      console.log(response)
    })
    .catch(error => {
      employeeStore.$state.submitErrors = error.response.data
      console.log(error)
    })
  isSubmitProcessing.value = false
}

// Computed
const formErrors = computed(() => {
  return employeeStore.$state.submitErrors?.errors
})

onMounted(() => {
  teamStore.fetchTeams().then(() => {
    teams.value = teamStore.teamList
  })

  employeeStore.$state.submitErrors = []
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Nouvel employé">
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

        <!-- Errors Success Component -->
        <VCardText v-if="submitSucces.success">
          <VAlert
            color="success"
            variant="tonal"
          >
            <div class="alert-body">
              <strong> {{ submitSucces.message }} </strong>
            </div>
          </VAlert>
        </VCardText>
        <VCardText>
          <VForm
            ref="form"
            @submit.prevent="addNewEmployee"
          >
            <VRow>
              <!-- 👉 First Name -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="employee.firstname"
                  label="Prénoms"
                  placeholder="Prénoms"
                />
              </VCol>

              <!-- 👉 Last Name -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="employee.lastname"
                  label="Noms"
                  placeholder="Noms"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="employee.email"
                  label="Email"
                  type="email"
                  placeholder="Email"
                />
              </VCol>

              <!-- 👉 Phone -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="employee.phone"
                  type="number"
                  label="Téléphone"
                  placeholder="Téléphone"
                />
              </VCol>

              <!-- 👉 Gender -->
              <VCol
                cols="12"
                md="3"
              >
                <AppSelect
                  v-model="employee.gender"
                  :items="genders"
                  label="Genre"
                />
              </VCol>
              <!-- 👉 Contract Type -->
              <VCol
                cols="12"
                md="3"
              >
                <AppSelect
                  v-model="employee.contract_type"
                  :items="contracts"
                  label="Type Contrat"
                />
              </VCol>

              <!-- 👉 Team -->
              <VCol
                cols="12"
                md="3"
              >
                <AppAutocomplete
                  v-model="employee.team"
                  label="Team"
                  density="compact"
                  item-title="label"
                  item-value="id"
                  :items="teams"
                  clearable
                  chips
                />
              </VCol>
              <!-- 👉 Mtle -->
              <VCol
                cols="12"
                md="3"
              >
                <AppTextField
                  v-model="employee.mtle"
                  label="Matricule"
                  placeholder="Mtle"
                />
              </VCol>
              <!-- 👉 Address -->
              <VCol cols="12">
                <AppTextarea
                  v-model="employee.address"
                  label="Adresse"
                  rows="3"
                  clearable
                />
              </VCol>
              <!-- 👉 Status -->
              <VCol cols="12">
                <AppSelect
                  v-model="employee.status"
                  :items="status"
                  item-value="value"
                  item-title="label"
                  label="Etat"
                  rows="3"
                  clearable
                />
              </VCol>
              <!-- 👉 Submit & Reset -->
              <VCol
                cols="12"
                class="d-flex gap-4"
              >
                <VBtn
                  type="submit"
                  :loading="isSubmitProcessing"
                >
                  Enregistrer
                </VBtn>

                <VBtn
                  type="reset"
                  color="secondary"
                  variant="tonal"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
meta:
  requiresAuth: true
</route>
