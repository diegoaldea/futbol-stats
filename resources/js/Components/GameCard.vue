<template>
    <Link
        :href="isFinished ? route('games.summary', game.id) : route('games.show', game.id)"
        class="block rounded-lg bg-white p-4 shadow transition hover:shadow-md"
    >
        <div class="mb-2 flex items-center justify-between">
            <p class="text-xs text-slate-400">{{ formattedDate }}</p>
            <span
                v-if="!isFinished"
                class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700"
            >
                Sin finalizar
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2">
            <!-- Equipo A -->
            <div class="text-center">
                <p class="break-words font-semibold leading-tight text-slate-700">{{ teamAName }}</p>
                <p class="text-3xl font-bold text-slate-800">{{ game.score_team_a }}</p>
                <ul class="mt-1 text-xs text-slate-500">
                    <li v-for="(scorer, index) in game.scorers?.a" :key="index">
                        {{ scorer.name }}<template v-if="scorer.ownGoal"> (e.c.)</template><template v-if="scorer.goals > 1"> ({{ scorer.goals }})</template>
                    </li>
                </ul>
            </div>

            <!-- Equipo B -->
            <div class="text-center">
                <p class="break-words font-semibold leading-tight text-slate-700">{{ teamBName }}</p>
                <p class="text-3xl font-bold text-slate-800">{{ game.score_team_b }}</p>
                <ul class="mt-1 text-xs text-slate-500">
                    <li v-for="(scorer, index) in game.scorers?.b" :key="index">
                        {{ scorer.name }}<template v-if="scorer.ownGoal"> (e.c.)</template><template v-if="scorer.goals > 1"> ({{ scorer.goals }})</template>
                    </li>
                </ul>
            </div>
        </div>
    </Link>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    game: {
        type: Object,
        required: true,
    },
});

const formattedDate = computed(() => new Date(props.game.date).toLocaleDateString('es-AR'));
const teamAName = computed(() => props.game.team_a_name || 'Equipo A');
const teamBName = computed(() => props.game.team_b_name || 'Equipo B');
const isFinished = computed(() => !!props.game.finished_at);
</script>
