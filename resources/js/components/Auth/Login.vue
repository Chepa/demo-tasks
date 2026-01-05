<script setup lang="ts">
import {ref} from 'vue'
import {useAuthStore} from '../../stores/auth'

const emit = defineEmits<{
    'switch-to-register': []
}>()

const authStore = useAuthStore()
const error = ref('')

const form = ref({
    email: '',
    password: '',
})

async function handleSubmit() {
    error.value = ''
    const result = await authStore.login(form.value.email, form.value.password)

    if (result.success) {
        // Перенаправление будет обработано в App.vue через watch isAuthenticated
    } else {
        error.value = result.message || 'Неверные учетные данные'
    }
}
</script>
<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <h1 class="text-4xl font-extrabold mb-8 text-center tracking-wide">Вход</h1>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-text-secondary mb-2">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-4 py-3 border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
                        placeholder="your@email.com"
                    />
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-text-secondary mb-2">
                        Пароль
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full px-4 py-3 border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
                        placeholder="••••••••"
                    />
                </div>
                <div v-if="error" class="text-danger text-sm text-center">
                    {{ error }}
                </div>
                <button
                    type="submit"
                    :disabled="authStore.loading"
                    class="w-full px-6 py-3 text-base font-semibold bg-accent rounded-lg cursor-pointer transition-colors hover:bg-accent-hover active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ authStore.loading ? 'Вход...' : 'Войти' }}
                </button>
                <div class="text-center text-text-secondary text-sm">
                    Нет аккаунта?
                    <button
                        type="button"
                        @click="$emit('switch-to-register')"
                        class="text-accent hover:text-accent-hover font-medium cursor-pointer"
                    >
                        Зарегистрироваться
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

