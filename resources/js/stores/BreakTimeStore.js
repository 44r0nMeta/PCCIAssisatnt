import BreakTimeService from "@/services/BreakTimeService"
import { defineStore } from "pinia"

export const useBreakTimeStore = defineStore('breakTimeStore', {
  state: () => ({
    breakTimeList: [],
    planningCheckList: [],
    selectedBreakTime: {
      id: null,
      type: "",
      day: "",
      employee_id: null,
      expected_start_time: null,
      expected_end_time: null,
      started_time: null,
      ended_time: null,
      breakdown_total_min: 0,
      memo: "",
      status: "",
    },
    isLoading: true,
    submitErrors: [],
  }),

  actions: {
    async fetchBreakTimes() {
      this.selectedBreakTime.isLoading = true
      console.log('Fecthing....')
      await BreakTimeService.index().then(({ data }) => {
        this.breakTimeList = data.data
        this.isLoading = false
      }).catch(error => {
        console.log(error)
      })
    },
    async fetchLiveBreakTimes() {
      this.selectedBreakTime.isLoading = true
      console.log('Fecthing....')
      await BreakTimeService.live().then(({ data }) => {
        this.breakTimeList = data.data
        this.isLoading = false
      }).catch(error => {
        console.log(error)
      })
    },
    async addBreakTime (breakTime){
      return await BreakTimeService.store(breakTime)
    },
    async deleteBreakTime(breakTime) {
      return await BreakTimeService.delete(breakTime)
    },
    async getBreakTime(breakTime) {
      return await BreakTimeService.get(breakTime)
    },
    async updateBreakTime(breakTime) {
      return await BreakTimeService.update(breakTime)
    },
    async badgeBreakTime(data) {
      return await BreakTimeService.bage(data)
    },
    async checkBreakTime(mtle) {
      this.isLoading = true
      
      return await BreakTimeService.check(mtle).then(({ data }) => {
        this.planningCheckList = data.data
        this.isLoading = false
      })
    },

    resetSelectedBreakTime (state) {
      //   this.selectedBreakTime.employee_id = null
      this.selectedBreakTime.id = null
      this.selectedBreakTime.type = ""
      this.selectedBreakTime.day = ""
      this.selectedBreakTime.expected_start_time = null
      this.selectedBreakTime.expected_end_time = null
      this.selectedBreakTime.started_time = null
      this.selectedBreakTime.ended_time = null
      this.selectedBreakTime.breakdown_total_min = 0
      this.selectedBreakTime.memo = ""
      this.selectedBreakTime.status = ""
    },
  },
})
