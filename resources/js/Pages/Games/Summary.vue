<template>
    <div>
        <h1>Resumen del partido</h1>
        <h2>Equipo A {{ game.score_team_a }} - {{ game.score_team_b }} Equipo B</h2>

        <div>
            <h3>Equipo A</h3>
            <div v-for="gamePlayer in teamA" :key="gamePlayer.id">
                <strong>{{ gamePlayer.player.name }}</strong> — Puntaje: {{ playerScore(gamePlayer) }}
                <span v-for="stat in gamePlayer.stats" :key="stat.id">
                    {{ stat.stat.name }}: {{ stat.value }}
                </span>
            </div>
        </div>

        <div>
            <h3>Equipo B</h3>
            <div v-for="gamePlayer in teamB" :key="gamePlayer.id">
                <strong>{{ gamePlayer.player.name }}</strong> — Puntaje: {{ playerScore(gamePlayer) }}
                <span v-for="stat in gamePlayer.stats" :key="stat.id">
                    {{ stat.stat.name }}: {{ stat.value }}
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    game: Object,
    teamA: Array,
    teamB: Array,
});

function playerScore(gamePlayer) {
    const total = (gamePlayer.stats ?? []).reduce(
        (sum, stat) => sum + stat.value * Number(stat.stat.points),
        0
    );

    // Evitar decimales colgando (ej. 3.00 -> 3, 2.50 -> 2.5)
    return Number(total.toFixed(2));
}
</script>
