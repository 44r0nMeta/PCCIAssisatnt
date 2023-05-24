import axios from "axios"

export const authClient = axios.create({
  baseURL: `http://localhost:8000/api`,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})


export default {
  async index() {
    return await authClient.get("/team")
  },
  async get(team) {
    return await authClient.get(`/team/${team}`)
  },
  async store(team) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post("/team", team)
  },
  async update(team) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.put(`/team/${team.id}`, team)
  },
  async delete(team) {
    return await authClient.delete(`/team/${team?.id}`)
  },
}
