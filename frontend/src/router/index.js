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
  {
    path: '/cart',
    name: 'Cart',
    component: () => import('../components/pages/CartPage/CartPage.vue'),
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../components/pages/AdminDashboardPage/AdminDashboardPage.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/products',
    name: 'AdminProducts',
    component: () => import('../components/pages/AdminProductsPage/AdminProductsPage.vue'),
    meta: { requiresAdmin: true },
  },
  {
    path: '/admin/orders',
    name: 'AdminOrders',
    component: () => import('../components/pages/AdminOrdersPage/AdminOrdersPage.vue'),
    meta: { requiresAdmin: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')
  const user  = JSON.parse(localStorage.getItem('user') || 'null')

  if ((to.meta.requiresAuth || to.meta.requiresAdmin) && !token) {
    return { name: 'Login', query: { redirect: to.fullPath } }
  }
  if (to.meta.requiresAdmin && user?.role !== 'admin') {
    return { name: 'AuctionArchive' }
  }
})

export default router
