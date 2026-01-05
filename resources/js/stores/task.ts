import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Task } from '../types/task'
import { useAuthStore } from './auth'

const API_BASE = '/api/tasks'

export const useTaskStore = defineStore('task', () => {
    const tasks = ref<Task[]>([])

    const activeTasks = computed(() =>
        tasks.value.filter(task => !task.completed)
    )

    const completedTasks = computed(() =>
        tasks.value.filter(task => task.completed)
    )

    const totalTasks = computed(() => tasks.value.length)

    const activeTasksCount = computed(() => activeTasks.value.length)

    function getHeaders() {
        const authStore = useAuthStore()
        return authStore.getAuthHeaders()
    }

    async function fetchTasks() {
        try {
            const response = await fetch(API_BASE, {
                headers: getHeaders(),
            })
            const result = await response.json()
            if (result.success) {
                tasks.value = result.data
            }
        } catch (error) {
            console.error('Ошибка загрузки tasks:', error)
        }
    }

    async function addTask(task: Task) {
        if (!task.text.trim()) return

        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: getHeaders(),
                body: JSON.stringify(task),
            })
            const result = await response.json()
            if (result.success) {
                tasks.value.push(result.data)
            }
        } catch (error) {
            console.error('Ошибка добавления task:', error)
        }
    }

    async function removeTask(id: number) {
        try {
            const response = await fetch(`${API_BASE}/${id}`, {
                method: 'DELETE',
                headers: getHeaders(),
            })
            const result = await response.json()
            if (result.success) {
                tasks.value = tasks.value.filter(task => task.id !== id)
            }
        } catch (error) {
            console.error('Ошибка удаления task:', error)
        }
    }

    async function toggleTask(id: number) {
        const task = tasks.value.find(t => t.id === id)
        if (!task) return

        const newCompleted = !task.completed

        try {
            const response = await fetch(`${API_BASE}/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                body: JSON.stringify({ completed: newCompleted }),
            })
            const result = await response.json()
            if (result.success) {
                task.completed = newCompleted
            }
        } catch (error) {
            console.error('Ошибка обновления task:', error)
        }
    }

    async function clearCompleted() {
        try {
            const response = await fetch(`${API_BASE}/completed/clear`, {
                method: 'DELETE',
                headers: getHeaders(),
            })
            const result = await response.json()
            if (result.success) {
                tasks.value = tasks.value.filter(task => !task.completed)
            }
        } catch (error) {
            console.error('Ошибка очистки выполненных tasks:', error)
        }
    }

    function hydrate(initialState: any) {
        if (initialState?.task?.tasks) {
            tasks.value = initialState.task.tasks
        }
    }

    return {
        tasks,
        activeTasks,
        completedTasks,
        totalTasks,
        activeTasksCount,
        fetchTasks,
        addTask,
        removeTask,
        toggleTask,
        clearCompleted,
        hydrate
    }
})

