import axios from "axios"

export const authClient = axios.create({
  baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

export default {
  async dashboardStats() {
    return await authClient.get("/stats/dashboard")
  },
}
