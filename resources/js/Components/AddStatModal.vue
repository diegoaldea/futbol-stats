<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="$emit('close')"
    >
        <div class="w-full max-w-sm rounded-lg bg-white p-4 shadow-xl">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">{{ title }}</h2>
                <button class="text-slate-400 hover:text-slate-600" @click="$emit('close')">✕</button>
            </div>

            <input
                v-model="search"
                type="text"
                placeholder="Buscar stat"
                class="mb-3 w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                autofocus
            />

            <ul class="flex max-h-72 flex-col gap-1 overflow-auto">
                <li
                    v-for="stat in filtered"
                    :key="stat.id"
                    class="cursor-pointer rounded-md px-3 py-2 hover:bg-slate-100"
                    @click="$emit('select', stat.id)"
                >
                    {{ stat.name }}
                </li>
                <li v-if="filtered.length === 0" class="px-3 py-2 text-slate-400">
                    No hay estadísticas
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    title: { type: String, default: '' },
    stats: { type: Array, default: () => [] },
});

defineEmits(['select', 'close']);

const search = ref('');

const filtered = computed(() =>
    props.stats.filter((stat) =>
        stat.name.toLowerCase().includes(search.value.trim().toLowerCase())
    )
);
</script>
