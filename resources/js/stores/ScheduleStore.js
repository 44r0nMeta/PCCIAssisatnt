import ScheduleService from "@/services/ScheduleService"
import { defineStore } from "pinia"

export const useScheduleStore = defineStore('scheduleStore', {
  state: () => ({
    scheduleList: [],
    planningCheckList: [],
    selectedSchedule: {
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
    async fetchSchedules() {
      this.selectedSchedule.isLoading = true
      console.log('Fecthing....')
      await ScheduleService.index().then(({ data }) => {
        this.scheduleList = data.data
        this.isLoading = false
      }).catch(error => {
        console.log(error)
      })
    },
    async addSchedule (schedule){
      return await ScheduleService.store(schedule)
    },
    async deleteSchedule(schedule) {
      return await ScheduleService.delete(schedule)
    },
    async getSchedule(schedule) {
      return await ScheduleService.get(schedule)
    },
    async updateSchedule(schedule) {
      return await ScheduleService.update(schedule)
    },
    async badgeSchedule(data) {
      return await ScheduleService.bage(data)
    },
    async checkAttendance(mtle) {
      this.isLoading = true
      
      return await ScheduleService.check(mtle).then(({ data }) => {
        this.planningCheckList = data.data
        this.isLoading = false
      })
    },

    resetSelectedSchedule (state) {
      //   this.selectedSchedule.employee_id = null
      this.selectedSchedule.id = null
      this.selectedSchedule.type = ""
      this.selectedSchedule.day = ""
      this.selectedSchedule.expected_start_time = null
      this.selectedSchedule.expected_end_time = null
      this.selectedSchedule.started_time = null
      this.selectedSchedule.ended_time = null
      this.selectedSchedule.breakdown_total_min = 0
      this.selectedSchedule.memo = ""
      this.selectedSchedule.status = ""
    },
  },
})
