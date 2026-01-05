<script setup lang="ts">
import {ref} from 'vue'
import {useAuthStore} from '../../stores/auth'

const emit = defineEmits<{
    'switch-to-login': []
}>()

const authStore = useAuthStore()
const errors = ref<Record<string, string[]>>({})

const form = ref({
    name: '',
    email: '',
    password: '',
    passwordConfirmation: '',
})

async function handleSubmit() {
    errors.value = {}
    const result = await authStore.register(
        form.value.name,
        form.value.email,
        form.value.password,
        form.value.passwordConfirmation
    )

    if (!result.success && result.errors) {
        errors.value = result.errors
    }
}
</script>
<template>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="w-full max-w-md">
            <h1 class="text-4xl font-extrabold mb-8 text-center tracking-wide">Регистрация</h1>
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-text-secondary mb-2">
                        Имя
                    </label>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-3 border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
                        placeholder="Ваше имя"
                    />
                    <div v-if="errors.name" class="text-danger text-sm mt-1">
                        {{ errors.name[0] }}
                    </div>
                </div>
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
                    <div v-if="errors.email" class="text-danger text-sm mt-1">
                        {{ errors.email[0] }}
                    </div>
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
                        minlength="8"
                        class="w-full px-4 py-3 border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
                        placeholder="••••••••"
                    />
                    <div v-if="errors.password" class="text-danger text-sm mt-1">
                        {{ errors.password[0] }}
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-text-secondary mb-2">
                        Подтверждение пароля
                    </label>
                    <input
                        id="password_confirmation"
                        v-model="form.passwordConfirmation"
                        type="password"
                        required
                        minlength="8"
                        class="w-full px-4 py-3 border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
                        placeholder="••••••••"
                    />
                </div>
                <button
                    type="submit"
                    :disabled="authStore.loading"
                    class="w-full px-6 py-3 text-base font-semibold bg-accent rounded-lg cursor-pointer transition-colors hover:bg-accent-hover active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ authStore.loading ? 'Регистрация...' : 'Зарегистрироваться' }}
                </button>
                <div class="text-center text-text-secondary text-sm">
                    Уже есть аккаунт?
                    <button
                        type="button"
                        @click="$emit('switch-to-login')"
                        class="text-accent hover:text-accent-hover font-medium cursor-pointer"
                    >
                        Войти
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

