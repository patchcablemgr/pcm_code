import Vue from 'vue'
import VueRouter from 'vue-router'
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getHomeRouteForLoggedInUser } from '@/auth/utils'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: () => import('@/views/Dashboard.vue'),
      meta: {
        pageTitle: 'Dashboard',
        breadcrumb: [
          {
            text: 'Dashboard',
            active: true,
          },
        ],
        resource: 'User',
        action: 'read',
      },
    },
	{
    path: '/templates',
    name: 'templates',
    component: () => import('@/views/Templates.vue'),
    meta: {
      pageTitle: 'Templates',
      resource: 'Operator',
      action: 'read',
    },
  },
  {
    path: '/environment',
    name: 'environment',
    component: () => import('@/views/Environment.vue'),
    meta: {
      pageTitle: 'Environment',
      resource: 'Operator',
      action: 'read',
    },
  },
  {
    path: '/explore',
    name: 'explore',
    component: () => import('@/views/Explore.vue'),
    meta: {
      pageTitle: 'Explore',
      resource: 'User',
      action: 'read',
    },
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/Admin.vue'),
    meta: {
      pageTitle: 'Admin',
      resource: 'User',
      action: 'read',
    },
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/Profile.vue'),
    meta: {
      pageTitle: 'Profile',
      breadcrumb: [
        {
          text: 'Profile',
          active: true,
        },
      ],
      resource: 'User',
      action: 'read',
    },
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login.vue'),
    meta: {
      layout: 'full',
      resource: 'Public',
      action: 'read',
    },
  },
	{
    path: '/register',
    name: 'register',
    component: () => import('@/views/Register.vue'),
    meta: {
      layout: 'full',
      resource: 'Public',
      action: 'read',
    },
  },
	{
      path: '/not-authorized',
      name: 'not-authorized',
      component: () => import('@/views/NotAuthorized.vue'),
      meta: {
        layout: 'full',
        resource: 'Public',
        action: 'read',
      },
    },
    {
      path: '/error-404',
      name: 'error-404',
      component: () => import('@/views/error/Error404.vue'),
      meta: {
        layout: 'full',
        resource: 'Public',
        action: 'read',
      },
    },
    {
      path: '*',
      redirect: 'error-404',
      meta: {
        resource: 'Public',
        action: 'read',
      },
    },
  ],
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

// Router Before Each hook for route protection
router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()

  if (!canNavigate(to)) {
	
    // Redirect to login if not logged in
    // ! We updated login route name here from `auth-login` to `login` in starter-kit
    if (!isLoggedIn) return next({ name: 'login' })
    // If logged in => not authorized
    return next({ name: 'not-authorized' })
  }
  
  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    next(getHomeRouteForLoggedInUser())
  }
  return next()
})

export default router
