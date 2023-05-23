import { useAuthStore } from '@/stores/AuthStore'
import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...setupLayouts(routes),
  ],
})


// router.beforeEach((to, from, next) => {
//   console.log(routes)

//   const authStore = useAuthStore()
//   const loginQuery = { name: "auth-login", query: { redirect: to.fullPath } }
//   if(to.meta.requiresAuth && !authStore.user){
//     next({ name: 'auth-login' })
//   }else if(to.meta.isGuest && authStore.user){
//     next({ name: 'index' })
//   }else{
//     next()
//   }
// })

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const authUser = authStore.user
  const reqAuth = to.matched.some(record => record.meta.requiresAuth)
  const loginQuery = { name: "auth-login", query: { redirect: to.fullPath } }
  let auth = false
  
  
  if (reqAuth) {
    console.log(auth)

    authStore.checkAuth().then(() => {
      if (authStore.authAuthenticated) {
        next()
      }
      else {
        next(loginQuery)
      }
    })

  }else {
    next()// make sure to always call next()!
  }
})

// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
export default router
