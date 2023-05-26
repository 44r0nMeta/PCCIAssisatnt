import axios from "axios"

export const authClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})


// /*
//  * Add a response interceptor
//  */
// authClient.interceptors.response.use(
//   response => {
//     return response
//   },
//   function (error) {
//     if (
//       error.response &&
//       [401, 419].includes(error.response.status) &&
//       useAuthStore().user 
//     ) {
//       useAuthStore().logout()
//     }
    
//     return Promise.reject(error)
//   },
// )

export default {
  async login(credentials) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post("/login", credentials)
  },
  logout() {
    return authClient.post("/logout")
  },
  async getAuthUser() {
    return await authClient.get("/api/user")
  },
  async resetPassword(credentials) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return authClient.post("/reset-password", credentials)
  },
  updatePassword(credentials) {
    return authClient.put("/user/password", credentials)
  },
}
