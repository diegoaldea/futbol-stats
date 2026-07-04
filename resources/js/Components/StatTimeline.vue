<template>
    <div>
        <!-- Leyenda de colores por equipo -->
        <div v-if="entries.length" class="mb-3 flex gap-4 text-sm">
            <span class="text-sky-600">● {{ teamAName }}</span>
            <span class="text-rose-600">● {{ teamBName }}</span>
        </div>

        <ol class="flex flex-col text-sm">
            <li
                v-for="(entry, index) in scoredEntries"
                :key="index"
                class="rounded-md px-3 py-2 odd:bg-slate-50"
            >
                <span class="text-slate-400">{{ index + 1 }}.</span>
                <span class="ml-1 font-medium" :class="teamColors[entry.team]">{{ entry.player }}</span>
                <span class="text-slate-500"> · {{ entry.stat }}</span>
                <span v-if="entry.partial">
                    ⚽ <span class="font-semibold text-slate-700">{{ entry.partial }}</span>
                </span>
            </li>
            <li v-if="entries.length === 0" class="px-3 py-2 text-slate-400">
                No hay cronología disponible para este partido.
            </li>
        </ol>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    // [{ player, stat, team }] en orden cronológico
    entries: { type: Array, default: () => [] },
    teamAName: { type: String, default: 'Equipo A' },
    teamBName: { type: String, default: 'Equipo B' },
});

const teamColors = {
    a: 'text-sky-600',
    b: 'text-rose-600',
};

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
            entry.team === 'a' ? b++ : a++;
            partial = `${a}-${b}`;
        }

        return { ...entry, partial };
    });
});
</script>
