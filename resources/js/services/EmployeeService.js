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
    return await authClient.get("/employee")
  },
  async get(employee) {
    return await authClient.get(`/employee/${employee}`)
  },
  async store(employee) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.post("/employee", employee)
  },
  async update(employee) {
    await authClient.get("/sanctum/csrf-cookie")
    
    return await authClient.put(`/employee/${employee.id}`, employee)
  },
  async delete(employee) {
    return await authClient.delete(`/employee/${employee?.id}`)
  },
}
