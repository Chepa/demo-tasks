<script setup lang="ts">
import {onMounted, ref, watch} from 'vue'
import {useAuthStore} from './stores/auth'
import {useTaskStore} from './stores/task'
import TaskInput from './components/Task/TaskInput.vue'
import TaskList from './components/Task/TaskList.vue'
import Login from './components/Auth/Login.vue'
import Register from './components/Auth/Register.vue'

const authStore = useAuthStore()
const taskStore = useTaskStore()
const showLogin = ref(true)

onMounted(async () => {
    // Проверяем наличие токена и загружаем пользователя
    if (authStore.token) {
        await authStore.fetchUser()
        if (authStore.isAuthenticated) {
            await taskStore.fetchTasks()
        }
    }
})

watch(() => authStore.isAuthenticated, async (isAuthenticated) => {
    if (isAuthenticated) {
        await taskStore.fetchTasks()
    } else {
        taskStore.tasks = []
    }
})

async function handleLogout() {
    await authStore.logout()
    showLogin.value = true
}
</script>
<template>
    <div class="min-h-screen">
        <div v-if="authStore.isAuthenticated" class="flex items-start justify-center py-6 px-4">
            <div class="w-full max-w-2xl">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-4xl font-extrabold tracking-wide">Список задач</h1>
                    <div class="flex items-center gap-4">
                        <span class="text-text-secondary">{{ authStore.user?.name }}</span>
                        <button
                            @click="handleLogout"
                            class="px-4 py-2 text-sm text-danger bg-transparent border border-border rounded-md cursor-pointer transition-colors hover:bg-danger-bg hover:border-danger"
                        >
                            Выйти
                        </button>
                    </div>
                </div>
                <TaskInput/>
                <TaskList/>
            </div>
        </div>
        <Login v-else-if="showLogin" @switch-to-register="showLogin = false"/>
        <Register v-else @switch-to-login="showLogin = true"/>
    </div>
</template>

