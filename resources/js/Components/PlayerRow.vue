<template>
    <div class="flex flex-wrap items-center gap-2">
        <!-- Calificación parcial (click = switch individual) -->
        <button
            class="flex w-12 items-center justify-center rounded-md py-1 text-sm font-semibold transition"
            :class="isRatingVisible(gamePlayer.id)
                ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                : 'bg-slate-100 text-slate-400 hover:bg-slate-200'"
            title="Mostrar/ocultar calificación"
            @click="$emit('toggle-rating', gamePlayer.id)"
        >
            {{ isRatingVisible(gamePlayer.id) ? getRating(gamePlayer.id) : '★' }}
        </button>

        <button
            class="rounded-md bg-slate-800 px-3 py-1 font-medium text-white hover:bg-slate-700"
            @click="$emit('open', gamePlayer)"
        >
            {{ gamePlayer.player.name }}
        </button>

        <template v-for="stat in stats" :key="stat.id">
            <StatTag
                v-if="getValue(gamePlayer.id, stat.id) > 0"
                :name="stat.name"
                :value="getValue(gamePlayer.id, stat.id)"
                :color-class="getColor(stat)"
                @add="$emit('add', { gamePlayerId: gamePlayer.id, statId: stat.id })"
                @subtract="$emit('subtract', { gamePlayerId: gamePlayer.id, statId: stat.id })"
            />
        </template>
    </div>
</template>

<script setup>
import StatTag from '@/Components/StatTag.vue';

defineProps({
    gamePlayer: { type: Object, required: true },
    stats: { type: Array, required: true },
    getValue: { type: Function, required: true },
    getColor: { type: Function, required: true },
    getRating: { type: Function, required: true },
    isRatingVisible: { type: Function, required: true },
});

defineEmits(['open', 'add', 'subtract', 'toggle-rating']);
</script>
