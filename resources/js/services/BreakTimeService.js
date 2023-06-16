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
    return await authClient.get("/breaktime")
  },
  async live() {
    return await authClient.get("/breaktime/live")
  },
  async get(breaktime) {
    return await authClient.get(`/breaktime/${breaktime}`)
  },
  async store(breaktime) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post("/breaktime", breaktime)
  },
  async update(breaktime) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.put(`/breaktime/${breaktime.id}`, breaktime)
  },
  
  async bage(data) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post(`/breaktime/bage`, data)
  },
  async check(mtle) {
    
    return await authClient.get(`/breaktime/${mtle}`)
  },
  async delete(breaktime) {
    return await authClient.delete(`/breaktime/${breaktime?.id}`)
  },
}
