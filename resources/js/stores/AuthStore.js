import AuthService from "@/services/AuthService"
import { defineStore } from "pinia"


export const useAuthStore  = defineStore('authStore', {
  state: () => ({
    authUser: null,
    authAuthenticated: false,
    authErrors: null,
    authStatus: null,
  }),
  actions: {
    async login (credentials) {
      return await AuthService.login(credentials)
    },
    async logout (){
      return await AuthService.logout()
    },
    async checkAuth(){
      return await AuthService.getAuthUser()
        .then(response => {
          this.authAuthenticated = true
          this.authUser = response.data
        }).catch(error => {
          this.authAuthenticated = false
          console.log({ error })
        })
    },
  },
  getters: {
    user: state => state.authUser,
    errors: state => state.authErrors,
  },
})
