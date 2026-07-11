<template>
    <div
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center"
        @click.self="$emit('close')"
    >
        <div class="max-h-[85vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:rounded-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ player.name }}</h2>
                    <p class="text-sm" :class="teamColor">{{ player.teamName }}</p>
                </div>
                <button
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200"
                    @click="$emit('close')"
                >
                    ✕
                </button>
            </div>

            <p class="mt-3 text-4xl font-bold text-emerald-600">{{ player.rating }}</p>
            <p class="text-sm text-slate-400">
                {{ player.actions }} acciones · rating del partido
                <span v-if="player.calificacion != null"> · calificación <span class="font-semibold text-slate-500">{{ player.calificacion }}/10</span></span>
                <span v-if="player.momentum != null"> · momentum <span class="font-semibold text-slate-500">{{ player.momentum }}/10</span></span>
            </p>

            <!-- Evolución en el partido -->
            <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-400">Evolución en el partido</p>
            <div v-if="trajectory.length > 1" class="mt-2 rounded-lg bg-slate-50 p-2">
                <PerformanceChart :points="trajectory" :baseline="0" />
            </div>
            <p v-else class="mt-1 text-sm text-slate-400">Sin historial en este partido.</p>

            <PlayerStatsDetail
                class="mt-5"
                :player="player"
                :players="players"
                :stat-meta="statMeta"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import PlayerStatsDetail from '@/Components/PlayerStatsDetail.vue';
import PerformanceChart from '@/Components/PerformanceChart.vue';

const props = defineProps({
    player: { type: Object, required: true },
    players: { type: Array, required: true },
    statMeta: { type: Object, required: true },
    history: { type: Array, default: () => [] },
});

defineEmits(['close']);

const teamColor = computed(() => (props.player.team === 'a' ? 'text-sky-600' : 'text-amber-600'));

// Forma/racha: promedio móvil del aporte por acción (no acumulado, así no sube siempre)
const WINDOW = 5;
const trajectory = computed(() => {
    const events = props.history.filter((e) => e.gamePlayerId === props.player.id);
    const deltas = events.map((e) => props.statMeta[e.stat]?.points ?? 0);

    return deltas.map((_, i) => {
        const slice = deltas.slice(Math.max(0, i - WINDOW + 1), i + 1);
        const avg = slice.reduce((a, b) => a + b, 0) / slice.length;
        return { label: events[i].stat, value: Number(avg.toFixed(2)), negative: avg < 0 };
    });
});
</script>
