<template>
    <div>
        <Link :href="route('games.create')" class="bg-blue-400 p-2">Crear Partido</Link>

        <div v-if="!isGuest">
            <h1>Mis Partidos</h1>

            <p v-if="games.length === 0">Todavía no tenés partidos.</p>

            <Link
                v-for="game in games"
                :key="game.id"
                :href="route('games.summary', game.id)"
            >
                <span>{{ formatDate(game.date) }}</span>
                <span>Equipo A {{ game.score_team_a }} - {{ game.score_team_b }} Equipo B</span>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

defineProps({
    games: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isGuest = computed(() => !page.props.auth.user);

function formatDate(date) {
    return new Date(date).toLocaleDateString('es-AR');
}
</script>
