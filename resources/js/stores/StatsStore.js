import StatsService from "@/services/StatsService"
import { defineStore } from "pinia"

export const useStatsStore= defineStore('statsStore', {
  state: () => ({
    dashboard: {
      awaitToday: 0,
      presentToday: 0,
      lateToday: 0,
      waitingNow: 0,
      offToday: 0,
      totalBreakTime: 0,
      currentlyInBreaktime: 0,
      isLoading: true,
    },
  }),

  actions: {
    async fetchDashboardStats() {
    //   this.dashboard.isLoading = true
      await  StatsService.dashboardStats().then(({ data }) => {
        this.dashboard.awaitToday = data.awaitToday
        this.dashboard.presentToday = data.presentToday
        this.dashboard.lateToday = data.lateToday
        this.dashboard.waitingNow = data.waitingNow
        this.dashboard.offToday = data.offToday
        this.dashboard.totalBreakTime = data.totalBreakTime
        this.dashboard.currentlyInBreaktime = data.currentlyInBreaktime
        this.dashboard.isLoading = false
      }).catch(error => {
        console.log(error)
      })
    },
  },
})
