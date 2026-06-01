import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  { path: '/', redirect: '/auctions' },
  {
    path: '/auctions',
    name: 'AuctionArchive',
    component: () => import('../components/pages/AuctionArchivePage/AuctionArchivePage.vue'),
  },
  {
    path: '/auctions/:id',
    name: 'AuctionDetail',
    component: () => import('../components/pages/AuctionDetailPage/AuctionDetailPage.vue'),
  },
  {
    path: '/products',
    name: 'ProductArchive',
    component: () => import('../components/pages/ProductArchivePage/ProductArchivePage.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../components/pages/LoginPage/LoginPage.vue'),
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../components/pages/RegisterPage/RegisterPage.vue'),
  },
  {
    path: '/orders',
    name: 'MyOrders',
    component: () => import('../components/pages/MyOrdersPage/MyOrdersPage.vue'),
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) {
    return { name: 'Login', query: { redirect: to.fullPath } }
  }
})

export default router
