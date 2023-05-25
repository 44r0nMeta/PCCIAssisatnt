import EmployeeService from "@/services/EmployeeService"
import { defineStore } from "pinia"

export const useEmployeeStore = defineStore('employeeStore', {
  state: () => ({
    employeeList: [],
    isLoading: true,
    submitErrors: [],
  }),

  actions: {
    async fetchEmployees() {
      this.isLoading = true
      await EmployeeService.index().then(({ data }) => {
        this.employeeList = data.data
        this.isLoading = false
      })
    },
    async addEmployee (employee){
      return await EmployeeService.store(employee)
    },
    async deleteEmployee(employee) {
      return await EmployeeService.delete(employee)
    },
    async getEmployee(employee) {
      return await EmployeeService.get(employee)
    },
    async updateEmployee(employee) {
      return await EmployeeService.update(employee)
    },
  },
})
