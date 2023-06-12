<script setup>
import { useEmployeeStore } from '@/stores/EmployeeStore'
import { useTeamStore } from '@/stores/TeamStore'
import { computed, onBeforeMount, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const teamStore = useTeamStore()
const employeeStore = useEmployeeStore()
const route = useRoute()
const router = useRouter()

console.log(route.params.id)

const employee = ref({
  id: null,
  mtle: "",
  firstname: "",
  lastname: "",
  gender: "",
  contract_type: "",
  email: "",
  phone: "",
  team: "",
  address: "",
  status: "",
})

const teams = ref([])
const form = ref()

const submitSucces = reactive({
  success: false,
  message: null,
})

const status = [
  { label: 'Actif', value: 'A' },
  { label: 'Inactif', value: 'I' },
  { label: 'Congé', value: 'C' },
]

const genders = [
  'M', 'F',
]

const contracts = [
  'CDD', 'CDI', 'Stage', 'Saisonnier',
]

// Methods
const updateAnEmployee = () => {
  submitSucces.success = false
  employeeStore.updateEmployee(employee.value).then(response => {
    employeeStore.$state.submitErrors = []
    submitSucces.success = true
    submitSucces.message = "Mise à jour effectuée avec succès !"
    console.log(response)
  }).catch(error => {
    employeeStore.$state.submitErrors = error.response.data
    console.log(error)
  })
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

onBeforeMount(() => {
  employeeStore.isLoading = true
  employeeStore.getEmployee(route.params.id).then(({ data }) => {
    employeeStore.isLoading = false
    console.log(data)
    employee.value = { ...data.data }
  }).catch(error => {
    router.push('/notfound')
  })
})
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard :title="`Mise à jour ${employee.mtle} - ${employee.firstname} ${employee.lastname}`">
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
            v-if="!employeeStore.$state.isLoading"
            ref="form"
            @submit.prevent="updateAnEmployee"
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
                  chips
                  clearable
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
                <VBtn type="submit">
                  Enregistrer
                </VBtn>

                <!--
                  <VBtn
                  type="reset"
                  color="secondary"
                  variant="tonal"
                  >
                  Reset
                  </VBtn> 
                -->
              </VCol>
            </VRow>
          </VForm>


          <!-- Display When loading -->
          <VProgressCircular
            v-else
            :size="60"
            color="primary"
            indeterminate
          />
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
  meta:
    requiresAuth: true
</route>
