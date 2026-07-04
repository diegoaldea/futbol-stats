<template>
    <div>
        <button
            class="text-sm font-medium text-emerald-600 hover:underline disabled:text-slate-300 disabled:no-underline"
            :disabled="entries.length === 0"
            @click="showAll = true"
        >
            Ver historial
        </button>

        <!-- Modal: historial completo de ambos equipos -->
        <div
            v-if="showAll"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="showAll = false"
        >
            <div class="w-full max-w-md rounded-lg bg-white p-4 shadow-xl">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Historial de estadísticas</h2>
                    <button class="text-slate-400 hover:text-slate-600" @click="showAll = false">✕</button>
                </div>

                <!-- Leyenda de colores por equipo -->
                <div class="mb-3 flex gap-4 text-sm">
                    <span :class="teamColors.a">● {{ teamAName }}</span>
                    <span :class="teamColors.b">● {{ teamBName }}</span>
                </div>

                <ol class="flex max-h-80 flex-col overflow-auto text-sm">
                    <li
                        v-for="(entry, index) in reversed"
                        :key="index"
                        class="rounded-md px-3 py-2 odd:bg-slate-50"
                    >
                        <span class="text-slate-400">{{ entries.length - index }}.</span>
                        <span class="ml-1 font-medium" :class="teamColors[entry.team]">{{ entry.player }}</span>
                        <span class="text-slate-500"> · {{ entry.stat }}</span>
                        <span v-if="entry.partial">
                            ⚽ <span class="font-semibold text-slate-700">{{ entry.partial }}</span>
                        </span>
                    </li>
                    <li v-if="entries.length === 0" class="px-3 py-2 text-slate-400">
                        Sin estadísticas todavía
                    </li>
                </ol>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    // [{ player, stat, team }] en orden cronológico (solo sumas)
    entries: { type: Array, default: () => [] },
    teamAName: { type: String, default: 'Equipo A' },
    teamBName: { type: String, default: 'Equipo B' },
});

const teamColors = {
    a: 'text-sky-600',
    b: 'text-rose-600',
};

const showAll = ref(false);

// Recorre los eventos en orden y arma el resultado parcial en cada gol
const scoredEntries = computed(() => {
    let a = 0;
    let b = 0;

    return props.entries.map((entry) => {
        const name = entry.stat.toLowerCase();
        let partial = null;

        if (name === 'gol') {
            entry.team === 'a' ? a++ : b++;
            partial = `${a}-${b}`;
        } else if (name === 'gol en contra') {
            // El gol en contra suma al rival
            entry.team === 'a' ? b++ : a++;
            partial = `${a}-${b}`;
        }

        return { ...entry, partial };
    });
});

const reversed = computed(() => [...scoredEntries.value].reverse());
</script>
