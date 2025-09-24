import { createRouter, createWebHistory } from 'vue-router'
import LoginForm from '@/components/LoginForm.vue'
import RegisterForm from '@/components/RegisterForm.vue'
import TodoPage from '../views/TodoPage.vue'


const routes = [
    {
        path: '/',
        redirect: '/auth/login'
    },
    {   
        path: '/auth/login',
        name: 'Login',
        component: LoginForm
    },
    {
        path: '/auth/register',
        name: 'Register',
        component: RegisterForm
    },
    {
        path: '/todoPage',
        name: 'TodoPage',
        component: TodoPage,
        meta: { requiresAuth: true }
    },
    {
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')

    if (to.name === 'TodoPage' && !token) {
        next('/auth/login')
    } else if ((to.name === 'Login' || to.name === 'Register') && token) {
        next('/todoPage')
    } else {
        next()
    }
})

export default router