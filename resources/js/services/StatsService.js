import axios from "axios"

const url = `${import.meta.env.VITE_API_BASE_URL}/api`
export const authClient = axios.create({
  baseURL: url,
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

  async productionReporting(filters) {
    if(filters.export){
      await authClient.get(`/stats/schedules`, { params: { ...filters }, responseType: 'text/csv' })
        .then(response => {
          console.log(response?.data)

          const url = window.URL.createObjectURL(new Blob([response?.data]))
          const link = document.createElement('a')

          link.href = url
          link.setAttribute('download', `Export_Production_${(new Date()).toLocaleDateString().replaceAll('/', '-')}.csv`) //or any other extension
          document.body.appendChild(link)
          link.click()
        })

      console.log('file saved!')      
    }
    else
      return await authClient.get(`/stats/schedules`, { params: { ...filters } })
  },

  async productionReportingCumul(filters) {
    if(filters.export){
      await authClient.get(`/stats/schedules/cumul`, { params: { ...filters }, responseType: 'text/csv' })
        .then(response => {
          console.log(response?.data)

          const url = window.URL.createObjectURL(new Blob([response?.data]))
          const link = document.createElement('a')

          link.href = url
          link.setAttribute('download', `Export_CumulProduction_${(new Date()).toLocaleDateString().replaceAll('/', '-')}.csv`) //or any other extension
          document.body.appendChild(link)
          link.click()
        })

      console.log('file saved!') 
    }else
    
      return await authClient.get(`/stats/schedules/cumul`, { params: { ...filters } })
  }, 
}
