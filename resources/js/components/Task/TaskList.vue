<script setup lang="ts">
import { useTaskStore } from '../../stores/task'
import TaskItem from './TaskItem.vue'

const taskStore = useTaskStore()

function pluralize(count: number, one: string, few: string, many: string): string {
    const mod10 = count % 10
    const mod100 = count % 100

    if (mod100 >= 11 && mod100 <= 19) {
        return many
    }
    if (mod10 === 1) {
        return one
    }
    if (mod10 >= 2 && mod10 <= 4) {
        return few
    }
    return many
}

async function handleClearCompleted() {
    await taskStore.clearCompleted()
}
</script>
<template>
    <div class="mt-2">
        <div v-if="taskStore.totalTasks === 0" class="text-center py-12 px-6 text-text-secondary">
            <p>Список задач пуст</p>
            <p class="text-sm opacity-70 mt-2">Добавьте новую задачу выше</p>
        </div>

        <div v-else>
            <div class="flex flex-col gap-2 mb-4">
                <TaskItem
                    v-for="task in taskStore.tasks"
                    :key="task.id"
                    :task="task"
                />
            </div>

            <div class="flex justify-between items-center p-4 bg-bg-secondary rounded-lg border border-border">
        <span class="text-text-secondary text-sm">
          {{ taskStore.activeTasksCount }} {{ pluralize(taskStore.activeTasksCount, 'задача', 'задачи', 'задач') }}
        </span>
                <button
                    v-if="taskStore.completedTasks.length > 0"
                    @click="handleClearCompleted"
                    class="px-4 py-2 text-sm text-danger bg-transparent border border-border rounded-md cursor-pointer transition-colors hover:bg-danger-bg hover:border-danger"
                >
                    Очистить выполненные ({{ taskStore.completedTasks.length }})
                </button>
            </div>
        </div>
    </div>
</template>

