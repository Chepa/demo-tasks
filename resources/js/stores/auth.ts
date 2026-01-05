import {defineStore} from 'pinia'
import {computed, ref} from 'vue'

export interface User {
    id: number
    name: string
    email: string
    email_verified_at?: string
    created_at?: string
    updated_at?: string
}

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null)
    const token = ref<string | null>(localStorage.getItem('auth_token'))
    const loading = ref(false)

    const isAuthenticated = computed(() => !!token.value && !!user.value)

    async function register(name: string, email: string, password: string, passwordConfirmation: string) {
        loading.value = true
        try {
            const response = await fetch('/api/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({name, email, password, password_confirmation: passwordConfirmation}),
            })

            const result = await response.json()

            if (result.success) {
                token.value = result.data.token
                user.value = result.data.user
                localStorage.setItem('auth_token', result.data.token)
                return {success: true}
            } else {
                return {success: false, errors: result.errors}
            }
        } catch (error) {
            console.error('Ошибка регистрации:', error)
            return {success: false, errors: {general: ['Произошла ошибка при регистрации']}}
        } finally {
            loading.value = false
        }
    }

    async function login(email: string, password: string) {
        loading.value = true
        try {
            const response = await fetch('/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify({email, password}),
            })

            const result = await response.json()

            if (result.success) {
                token.value = result.data.token
                user.value = result.data.user
                localStorage.setItem('auth_token', result.data.token)
                return {success: true}
            } else {
                return {success: false, message: result.message || 'Неверные учетные данные'}
            }
        } catch (error) {
            console.error('Ошибка входа:', error)
            return {success: false, message: 'Произошла ошибка при входе'}
        } finally {
            loading.value = false
        }
    }

    async function logout() {
        try {
            await fetch('/api/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token.value}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            })
        } catch (error) {
            console.error('Ошибка выхода:', error)
        } finally {
            token.value = null
            user.value = null
            localStorage.removeItem('auth_token')
        }
    }

    async function fetchUser() {
        if (!token.value) return

        try {
            const response = await fetch('/api/user', {
                headers: {
                    'Authorization': `Bearer ${token.value}`,
                },
            })

            const result = await response.json()

            if (result.success) {
                user.value = result.data
            } else {
                await logout()
            }
        } catch (error) {
            console.error('Ошибка загрузки пользователя:', error)
            await logout()
        }
    }

    function getAuthHeaders(): Record<string, string> {
        const headers: Record<string, string> = {
            'Content-Type': 'application/json',
        }

        if (token.value) {
            headers['Authorization'] = `Bearer ${token.value}`
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken
        }

        return headers
    }

    function hydrate(initialState: any) {
        if (initialState?.auth?.user) {
            user.value = initialState.auth.user
        }
        if (initialState?.auth?.token) {
            token.value = initialState.auth.token
        }
    }

    return {
        user,
        token,
        loading,
        isAuthenticated,
        register,
        login,
        logout,
        fetchUser,
        getAuthHeaders,
        hydrate,
    }
})

