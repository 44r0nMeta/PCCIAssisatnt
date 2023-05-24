// import TeamService from "@/services/TeamService"
import TeamService from "@/services/TeamService"
import { defineStore } from "pinia"

export const useTeamStore = defineStore('teamStore', {
  state: () => ({
    teamList: [],
    selectedTeam: {
      id: null,
      label: null,
      description: null,
    },
    isLoading: true,
    submitError: [],
  }),

  actions: {
    async fetchTeams() {
      await TeamService.index().then(({ data }) => {
        this.teamList = data.data
        this.isLoading = false
      })
    },

    async addTeam(team) {
      return await TeamService.store(team)
    },
    async deleteTeam(team) {
      return await TeamService.delete(team)
    },
    async updateTeam(team) {
      return await TeamService.update(team)
    },
    resetSelectedTeam (state)  {
      this.selectedTeam.id = null
      this.selectedTeam.description = null
      this.selectedTeam.label = null
    },
  },
})
