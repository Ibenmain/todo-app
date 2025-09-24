// // src/stores/auth.js
// import { defineStore } from 'pinia'
// import axios from '@/services/api'

// export const useAuthStore = defineStore('auth', {
//     state: () => ({
//         user: null,
//         token: localStorage.getItem('token'),
//         isAuthenticated: false
//     }),

//     getters: {
//         getUser: (state) => state.user,
//         isLoggedIn: (state) => state.isAuthenticated
//     },

//     actions: {
//         async login(credentials) {
//             try {
//                 const response = await axios.post('/auth/login', credentials)
//                 const { user, token } = response.data.data

//                 this.user = user
//                 this.token = token
//                 this.isAuthenticated = true

//                 localStorage.setItem('token', token)
//                 axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

//                 return { success: true }
//             } catch (error) {
//                 return {
//                     success: false,
//                     message: error.response?.data?.message || 'Erreur de connexion'
//                 }
//             }
//         },

//         async register(userData) {
//             try {
//                 const response = await axios.post('/auth/register', userData)
//                 const { user, token } = response.data.data

//                 this.user = user
//                 this.token = token
//                 this.isAuthenticated = true

//                 localStorage.setItem('token', token)
//                 axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

//                 return { success: true }
//             } catch (error) {
//                 return {
//                     success: false,
//                     message: error.response?.data?.message || 'Erreur d\'inscription'
//                 }
//             }
//         },

//         logout() {
//             this.user = null
//             this.token = null
//             this.isAuthenticated = false

//             localStorage.removeItem('token')
//             delete axios.defaults.headers.common['Authorization']
//         }
//     }
// })