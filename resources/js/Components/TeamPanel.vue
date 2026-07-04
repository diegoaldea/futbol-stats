<template>
    <section class="flex-1 rounded-lg bg-white p-4 shadow">
        <h2 class="text-xl font-bold text-slate-800">{{ name }}</h2>

        <p class="mb-4 text-xs text-slate-400">
            Última actividad:
            <template v-if="lastActivity">
                <span class="font-medium text-slate-600">{{ lastActivity.player }}</span>
                · {{ lastActivity.stat }}
            </template>
            <span v-else>—</span>
        </p>

        <div class="flex flex-col gap-3">
            <PlayerRow
                v-for="gamePlayer in players"
                :key="gamePlayer.id"
                :game-player="gamePlayer"
                :stats="stats"
                :get-value="getValue"
                :get-color="getColor"
                :get-rating="getRating"
                :is-rating-visible="isRatingVisible"
                @open="$emit('open', $event)"
                @add="$emit('add', $event)"
                @subtract="$emit('subtract', $event)"
                @toggle-rating="$emit('toggle-rating', $event)"
            />
        </div>
    </section>
</template>

<script setup>
import PlayerRow from '@/Components/PlayerRow.vue';

defineProps({
    name: { type: String, required: true },
    players: { type: Array, required: true },
    stats: { type: Array, required: true },
    getValue: { type: Function, required: true },
    getColor: { type: Function, required: true },
    getRating: { type: Function, required: true },
    isRatingVisible: { type: Function, required: true },
    lastActivity: { type: Object, default: null },
});

defineEmits(['open', 'add', 'subtract', 'toggle-rating']);
</script>
