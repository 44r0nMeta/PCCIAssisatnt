import axios from "axios"

export const authClient = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// console.log(import.meta.env.PROD)

export default {
  async index() {
    return await authClient.get("/schedule")
  },
  async get(schedule) {
    return await authClient.get(`/schedule/${schedule}`)
  },
  async store(schedule) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post("/schedule", schedule)
  },
  async update(schedule) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.put(`/schedule/${schedule.id}`, schedule)
  },
  
  async bage(data) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post(`/attendance/bage`, data)
  },
  async check(mtle) {
    
    return await authClient.get(`/planning/${mtle}`)
  },
  async delete(schedule) {
    return await authClient.delete(`/schedule/${schedule?.id}`)
  },
}
