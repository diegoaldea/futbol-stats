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
            </p>

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

const props = defineProps({
    player: { type: Object, required: true },
    players: { type: Array, required: true },
    statMeta: { type: Object, required: true },
});

defineEmits(['close']);

const teamColor = computed(() => (props.player.team === 'a' ? 'text-sky-600' : 'text-amber-600'));
</script>
