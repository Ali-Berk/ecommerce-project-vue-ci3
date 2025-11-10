import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import axios from 'axios'
import HomeView from '../views/HomeView.vue'
import AboutView from '../views/AboutView.vue'
import CategoryView from '../views/CategoryView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import ProductDetailsView from '../views/ProductDetailsView.vue'
import ProfileView from '../views/ProfileView.vue'
import SettingsView from '../views/SettingsView.vue'
import OrdersView from '../views/OrdersView.vue'
import NotFoundView from '../views/NotFoundView.vue'
import DashboardProductsView from '../views/DashboardProductsView.vue'
import DashboardOrdersView from '../views/DashboardOrdersView.vue'
import DashboardAddProductView from '../views/DashboardAddProductView.vue'
import DashboardProductEditView from '@/views/DashboardProductEditView.vue'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta:{showLayout:true}
  },
  {
    path:'/:pathMatch(.*)*',
    name: 'NotFound',
    component:NotFoundView,
    meta:{showLayout:false}

  },
  {
    path: '/about',
    name: 'about',
    component: AboutView
  },
  {
    path:'/category',
    name:'categories',
    component: HomeView,
    meta:{showLayout:true}
  },
  {
    path: '/category/:slug',
    name: 'category',
    props: true,
    component: CategoryView,
    meta:{showLayout:true}
  },
  {
    path: '/login',
    name: 'Login',
    component: LoginView,
    meta:{showLayout:false}
  },
  {
    path: '/register',
    name: 'Register',
    component: RegisterView,
    meta: {showLayout:false}
  },
  {
    path: '/product/:slug',
    name: 'ProductDetails',
    component: ProductDetailsView,
    meta: {showLayout:false} 
  },
  {
    path: '/profile',
    name: 'Profile',
    component: ProfileView,
    meta: {showLayout:false, requiresAuth: true}
  },
  {
    path: '/settings',
    name: 'Settings',
    component: SettingsView,
    meta: {showLayout:false, requiresAuth: true}
  },
  {
    path: '/orders',
    name: 'Orders',
    component: OrdersView,
    meta: {showLayout: false, requiresAuth: true}
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component:NotFoundView,
    meta:{showLayout:false, requiresAdmin: true}
  },
  {
    path: '/dashboard/products',
    name: 'dashboardProducts',
    component: DashboardProductsView,
    meta:{showLayout:false, requiresAdmin: true}
  },
  {
    path: '/dashboard/orders',
    name: 'dashboardOrders',
    component: DashboardOrdersView,
    meta: {showLayout:false, requiresAdmin:true}
  },
  {
    path: '/dashboard/addProduct',
    name: 'dashboardAddProduct',
    component: DashboardAddProductView,
    meta: {showLayout:false, requiresAdmin:true}
  },
  {
    path: '/dashboard/products/:slug',
    name: 'dashboardProductEdit',
    component: DashboardProductEditView,
    meta: {showLayout:false, requiresAdmin:true}
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach(async (to,from,next) =>{
  if(to.meta.requiresAuth){
    try{
      const res = await axios.post('http://localhost:8080/api/checkLogin',{},{withCredentials:true})
      if(res.data.status === 'success'){
        next();
      }
      else{
        next('/login');
      }
    }
    catch(err){
      console.log(err);
      next('/login');
    }
  }
  else if(to.meta.requiresAdmin){
    try{
      const res = await axios.post('http://localhost:8080/api/checkLogin',{},{withCredentials:true})
      if(res.data.user.role == 1){
        next();
      }
      else{
        next('/login');
      }
    }
    catch(err){
      console.log(err);
      next('/login');
    }
  }
  else{
    next();
  }
})


export default router
