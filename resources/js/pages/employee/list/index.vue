<script setup>
import AppTextField from '@/@core/components/app-form-elements/AppTextField.vue'
import { useEmployeeStore } from '@/stores/EmployeeStore'
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { VDataTable } from 'vuetify/labs/VDataTable'

const employeeStore = useEmployeeStore()
const router = useRouter()

const search = ref('')

const headers = [
  // { title: '#', key: 'id' },
  { title: "Mtle", key: "mtle" },
  { title: "Prénoms", key: "firstname" },
  { title: "Noms", key: "lastname" },

  // { title: "", key: "bithday" },
  { title: "Téléphone", key: "phone" },
  { title: "Email", key: "email" },

  // { title: "Genre", key: "gender" },
  // { title: "Adresse", key: "address" },

  // { title: "Contrat", key: "contract_type" },
  { title: "Team", key: "team_name" },
  { title: 'Cree Le', key: 'created_at', width: '50' },
  { title: 'Cree Par', key: 'created_by' },
  { title: 'Action', key: 'actions', sortable: false },
]

const selectedEmployee = ref({})

let dialogState = reactive({
  delete: false,
})

// Methods
const callEditEmployee = employee => {
  selectedEmployee.value = { ...employee }
  router.push({ name: "employee-edit-id", params: { id: employee?.id } })
  console.log(employee)
}

const deleteEmployee = () => {
  employeeStore.deleteEmployee(selectedEmployee.value).then(() => {
    employeeStore.fetchEmployees()
  }).catch(error => {
    console.log(error)
  })
  dialogState.delete = false
}

const callDeleteEmployeeActionDialog = employee => {
  selectedEmployee.value = { ...employee }
  dialogState.delete = true
  console.log(employee)
}

const closeDeleteEmployeeActionDialog = () => {
  dialogState.delete = false
}

const callAddNewEmploye = () => {
  router.push({ name: "employee-add" })
}


// Computed 
const employeesLoading = computed(() => {
  return employeeStore.$state.isLoading
})

const employees = computed(() => {
  return employeeStore.$state.employeeList
})


onMounted(() => {
  employeeStore.fetchEmployees()
})
</script>

<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard title="Employées">
          <VCardText>
            <VCardText>
              <VRow>
                <VCol
                  cols="12"
                  offset-md="4"
                  md="4"
                >
                  <AppTextField
                    v-model="search"
                    density="compact"
                    placeholder="Recherche"
                    append-inner-icon="tabler-search"
                    single-line
                    hide-details
                    dense
                    outlined
                  />
                </VCol>
                <VCol>
                  <VBtn
                    prepend-icon="tabler-plus"
                    @click="callAddNewEmploye"
                  >
                    Nouveau
                  </VBtn>
                </VCol>
              </VRow>
            </VCardText>
            <VDataTable
              v-if="!employeesLoading"
              :headers="headers"
              :items="employees"
              :search="search"
              :items-per-page="10"
              class="text-no-wrap"
            >
              <template #item.actions="{ item }">
                <div class="d-flex gap-1">
                  <IconBtn>
                    <VIcon icon="mdi-eye-outline" />
                  </IconBtn>
                  <IconBtn @click="callEditEmployee(item.raw)">
                    <VIcon icon="mdi-pencil-outline" />
                  </IconBtn>
                  <IconBtn @click="callDeleteEmployeeActionDialog(item.raw)">
                    <VIcon icon="mdi-delete-outline" />
                  </IconBtn>
                </div>
              </template> 
            </VDataTable>
            <VProgressLinear
              v-else
              indeterminate
              color="primary"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- Delete Dialog -->
    <VDialog
      v-model="dialogState.delete"
      max-width="500px"
    >
      <VCard>
        <VCardTitle>
          Etes-vous sûr de  vouloir supprimer {{ selectedEmployee?.mtle }}?
        </VCardTitle>
        <VCardActions>
          <VSpacer />

          <VBtn
            color="error"
            variant="outlined"
            @click="closeDeleteEmployeeActionDialog"
          >
            Annuler
          </VBtn>

          <VBtn
            color="success"
            variant="elevated"
            @click="deleteEmployee"
          >
            Supprimer
          </VBtn>

          <VSpacer />
        </VCardActions>
      </VCard>
    </VDialog>
  </div>
</template>

<route lang="yaml">
  meta:
    requiresAuth: true
</route>
