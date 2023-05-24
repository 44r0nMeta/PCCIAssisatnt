<script setup>
import AppTextField from '@/@core/components/app-form-elements/AppTextField.vue'
import { useTeamStore } from '@/stores/TeamStore'
import TeamFormDialog from '@/views/pages/team/TeamFormDialog.vue'
import { computed, onMounted, reactive, ref } from 'vue'
import { VDataTable } from 'vuetify/labs/VDataTable'

const teamStore = useTeamStore()

const search = ref('')

const headers = [
  // { title: '#', key: 'id' },
  { title: 'Libelle', key: 'label' },
  { title: 'Description', key: 'description' },
  { title: 'Cree Le', key: 'created_at' },
  { title: 'Cree Par', key: 'created_by.name' },
  { title: 'Action', key: 'actions', sortable: false },
]


let dialogState = reactive({
  delete: false,
  form: false,
})

const setSelectedTeam = team => {
  teamStore.$state.selectedTeam = { ...team }
  dialogState.form = true

  // console.log(team)
}

const deleteTeam = () => {
  teamStore.deleteTeam(teamStore.$state.selectedTeam)
    .then(response => {
      teamStore.fetchTeams()
      dialogState.delete = false
    }).catch(error => {
      console.log(error)
    })
}

const callDeleteTeamActionDialog = team => {
  dialogState.delete = true
  teamStore.$state.selectedTeam = { ...team }
  console.log(team)
}

const closeDeleteTeamActionDialog = () => {
  dialogState.delete = false
  teamStore.$state.selectedTeam = {}
}

const callAddTeamDialog = () => {
  teamStore.resetSelectedTeam()
  dialogState.form = true
}



const teamsLoading = computed(() => {
  return teamStore.$state.isLoading
})

const teams = computed(() => {
  return teamStore.$state.teamList
})


onMounted(() => {
  teamStore.fetchTeams()
})
</script>

<template>
  <div>
    <section>
      <VCard title="Groupes">
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
                @click="callAddTeamDialog"
              >
                Creer une Team
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
        <VDataTable
          v-if="!teamsLoading"
          :headers="headers"
          :items="teams"
          :search="search"
          :items-per-page="10"
          class="text-no-wrap"
        >
          <template #item.actions="{ item }">
            <div class="d-flex gap-1">
              <IconBtn @click="setSelectedTeam(item.raw)">
                <VIcon icon="mdi-pencil-outline" />
              </IconBtn>
              <IconBtn @click="callDeleteTeamActionDialog(item.raw)">
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
      </VCard>
    </section>

    <!-- Delete Dialog -->
    <VDialog
      v-model="dialogState.delete"
      max-width="500px"
    >
      <VCard>
        <VCardTitle>
          Etes-vous sûr de  vouloir supprimer {{ teamStore.$state.selectedTeam?.label }}?
        </VCardTitle>
        <VCardActions>
          <VSpacer />

          <VBtn
            color="error"
            variant="outlined"
            @click="closeDeleteTeamActionDialog"
          >
            Annuler
          </VBtn>

          <VBtn
            color="success"
            variant="elevated"
            @click="deleteTeam"
          >
            Supprimer
          </VBtn>

          <VSpacer />
        </VCardActions>
      </VCard>
    </VDialog>

    <!-- TeamFormDialog -->
    <TeamFormDialog
      v-model="dialogState.form"
      :team-data="teamStore.$state.selectedTeam"
      @update-state="dialogState.form = !dialogState.form"
    />
  </div>
</template>

<route lang="yaml">
  meta:
    requiresAuth: true
</route>
