<script setup lang="ts">
import type {Task} from '../../types/task.ts'
import {useTaskStore} from '../../stores/task'

interface Props {
    task: Task
}

const props = defineProps<Props>()
const taskStore = useTaskStore()
const taskId = props.task.id ?? 0

async function handleToggle() {
    await taskStore.toggleTask(taskId)
}

async function handleRemove() {
    await taskStore.removeTask(taskId)
}
</script>
<template>
    <div
        class="flex items-center gap-3 p-4 bg-bg-secondary rounded-lg border border-border transition-all hover:border-accent hover:bg-bg-hover"
        :class="{ 'opacity-60': task.completed }"
    >
        <input
            type="checkbox"
            :checked="task.completed"
            @change="handleToggle"
            class="w-5 h-5 cursor-pointer accent-accent"
        />
        <span
            class="flex-1 text-base text-text-primary break-words"
            :class="{ 'line-through': task.completed }"
        >
            <a :href="`/tasks/${task.id}`">{{ task.title }}</a>
    </span>
        <button
            @click="handleRemove"
            class="w-7 h-7 text-2xl leading-none text-text-secondary bg-transparent border-none rounded cursor-pointer transition-all flex items-center justify-center hover:text-danger hover:bg-danger-bg"
            aria-label="Удалить"
        >
            ×
        </button>
    </div>
</template>

