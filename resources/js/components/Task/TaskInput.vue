<script setup lang="ts">
import {ref} from 'vue'
import {useTaskStore} from '../../stores/task'

const taskStore = useTaskStore()
const inputTitle = ref('')
const inputText = ref('')

async function handleAdd() {
    if (inputTitle.value.trim() && inputText.value.trim()) {
        await taskStore.addTask({
            title: inputTitle.value,
            text: inputText.value,
            completed: false
        })
        inputTitle.value = ''
        inputText.value = ''
    }
}
</script>
<template>
    <div class="grid gap-3 mb-6">
        <input
            v-model="inputTitle"
            type="text"
            placeholder="Название задачи"
            class="flex-1 px-4 py-3 text-base border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
        />
        <textarea
            v-model="inputText"
            type="text"
            placeholder="Описание задачи"
            class="w-full flex-1 px-4 py-3 text-base border-2 border-border rounded-lg bg-bg-secondary text-text-primary transition-colors focus:outline-none focus:border-accent"
        ></textarea>
        <button
            @click="handleAdd"
            class="px-6 py-3 text-base font-semibold bg-accent rounded-lg cursor-pointer hover:bg-accent-hover border-2"
        >
            Добавить
        </button>
    </div>
</template>

