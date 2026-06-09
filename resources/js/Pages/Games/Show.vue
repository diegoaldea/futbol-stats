<template>
    <div>
        <!-- Marcador -->
        <div>
            <h1>Equipo A {{ game.score_team_a }} - {{ game.score_team_b }} Equipo B</h1>
        </div>

        <!-- Equipos -->
        <div>
            <!-- Equipo A -->
            <div>
                <h2>Equipo A</h2>
                <div v-for="gamePlayer in teamA" :key="gamePlayer.id">
                    <span>{{ gamePlayer.player.name }}</span>
                </div>
            </div>

            <!-- Equipo B -->
            <div>
                <h2>Equipo B</h2>
                <div v-for="gamePlayer in teamB" :key="gamePlayer.id">
                    <span>{{ gamePlayer.player.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    game: Object,
    teamA: Array,
    teamB: Array,
});
</script>