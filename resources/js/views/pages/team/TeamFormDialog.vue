<script setup>
import AppTextarea from '@/@core/components/app-form-elements/AppTextarea.vue'
import { useTeamStore } from '@/stores/TeamStore'
import { reactive, ref } from 'vue'

const props = defineProps({
  teamData: Object,
})

const emits = defineEmits(['updateState'])
const isDialogVisible = ref(false)
const teamStore = useTeamStore()

const submitSucces = reactive({
  success: false,
  message: null,
})

// Function
const addNewTeam = () => {
  teamStore.addTeam(teamStore.$state.selectedTeam).then(response => {
    submitSucces.success = true
    submitSucces.message = "Ajouter avec succès !"
    teamStore.resetSelectedTeam()
    teamStore.fetchTeams()
  }).catch(error => {
    teamStore.$state.submitError = error.response.data
    console.log(error)
  })

  // console.log('Adding')
}

const updateATeam = () => {
  teamStore.updateTeam(teamStore.$state.selectedTeam).then(response => {
    submitSucces.success = true
    submitSucces.message = "Mise a jour reussi !"
    teamStore.resetSelectedTeam()
    closeMe()
    teamStore.fetchTeams()
  }).catch(error => {
    teamStore.$state.submitError = error.response.data
    console.log(error)
  })
}

const savePerform = () => {
  teamStore.$state.submitError = []
  submitSucces.success = false
  if(teamStore.$state.selectedTeam.id) updateATeam()
  else addNewTeam()
}

const closeMe  = () => {
  teamStore.$state.submitError = []
  submitSucces.success = false
  emits('updateState', false)
}


// Computed
const formErrors = computed(() => {
  return teamStore.submitError?.errors
})
</script>

<template>
  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeMe" />


    <!-- Dialog Content -->
    <VCard title="Team">
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
          <p class="text-caption mb-1">
            <strong>{{ submitSucces.message }}</strong>
          </p>
        </VAlert>
      </VCardText>
      <VForm @submit.prevent="savePerform">
        <VCardText>
          <VRow>
            <VCol cols="12">
              <AppTextField
                v-model="teamStore.$state.selectedTeam.label"
                clearable
                clear-icon="tabler-circle-x"
                label="Libelle"
              />
            </VCol>
            <VCol cols="12">
              <AppTextarea
                v-model="teamStore.$state.selectedTeam.description"
                clearable
                clear-icon="tabler-circle-x"
                label="Description"
              />
            </VCol>
          </VRow>
        </VCardText>

        <VCardText class="d-flex justify-end flex-wrap gap-3">
          <VBtn
            variant="tonal"
            color="secondary"
            @click="closeMe"
          >
            Fermer
          </VBtn>
          <VBtn type="submit">
            Enregistrer
          </VBtn>
        </VCardText>
      </VForm>
    </VCard>
  </VDialog>
</template>
