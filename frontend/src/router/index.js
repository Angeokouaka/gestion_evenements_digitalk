import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import EvenementsView from '../views/EvenementsView.vue'
import EvenementDetailView from '../views/EvenementDetailView.vue'
import EditEvenementView from '../views/EditEvenementView.vue'
import LoginView from '../views/LoginView.vue'
import CreateEvenementView from '../views/CreateEvenementView.vue'
import ProfilView from '../views/ProfilView.vue'
import ScanView from '../views/ScanView.vue'
import DashboardView from '../views/DashboardView.vue'
import IntervenantsView from '../views/IntervenantsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/evenements',
      name: 'evenements',
      component: EvenementsView,
    },
    {
      path: '/evenements/:id',
      name: 'evenement-detail',
      component: EvenementDetailView,
    },
    {
      path: '/evenements/:id/modifier',
      name: 'evenement-modifier',
      component: EditEvenementView,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/creer-evenement',
      name: 'creer-evenement',
      component: CreateEvenementView,
    },
    {
      path: '/profil',
      name: 'profil',
      component: ProfilView,
    },
    {
      path: '/scan/:qrCode',
      name: 'scan',
      component: ScanView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
    },
    {
      path: '/intervenants',
      name: 'intervenants',
      component: IntervenantsView,
    },
  ],
})

export default router