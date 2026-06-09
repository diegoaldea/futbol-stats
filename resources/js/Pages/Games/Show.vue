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
                    <span @click="openModal(gamePlayer)">{{ gamePlayer.player.name }}</span>
                    <template v-for="stat in stats" :key="stat.id">
                        <span 
                            v-if="getPlayerStat(gamePlayer.id, stat.id) > 0"
                            @mouseenter="hoveredStat = `${gamePlayer.id}-${stat.id}`"
                            @mouseleave="hoveredStat = null"
                        >
                            {{ stat.name }} {{ getPlayerStat(gamePlayer.id, stat.id) }}
                            <span v-if="hoveredStat === `${gamePlayer.id}-${stat.id}`">
                                <button @click.stop="updateStat(gamePlayer.id, stat.id, 'add')">+</button>
                                <button @click.stop="updateStat(gamePlayer.id, stat.id, 'subtract')">-</button>
                            </span>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Equipo B -->
            <div>
                <h2>Equipo B</h2>
                <div v-for="gamePlayer in teamB" :key="gamePlayer.id">
                    <span @click="openModal(gamePlayer)">{{ gamePlayer.player.name }}</span>
                    <template v-for="stat in stats" :key="stat.id">
                        <span 
                            v-if="getPlayerStat(gamePlayer.id, stat.id) > 0"
                            @mouseenter="hoveredStat = `${gamePlayer.id}-${stat.id}`"
                            @mouseleave="hoveredStat = null"
                        >
                            {{ stat.name }} {{ getPlayerStat(gamePlayer.id, stat.id) }}
                            <span v-if="hoveredStat === `${gamePlayer.id}-${stat.id}`">
                                <button @click.stop="updateStat(gamePlayer.id, stat.id, 'add')">+</button>
                                <button @click.stop="updateStat(gamePlayer.id, stat.id, 'subtract')">-</button>
                            </span>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal">
            <h2>{{ selectedPlayer.player.name }}</h2>
            <ul>
                <li v-for="stat in stats" :key="stat.id" @click="updateStat(selectedPlayer.id, stat.id, 'add')">
                    {{ stat.name }}
                </li>
            </ul>
            <button @click="closeModal">Cerrar</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    game: Object,
    teamA: Array,
    teamB: Array,
    stats: Array,
});

const selectedPlayer = ref(null);
const showModal = ref(false);

const hoveredStat = ref(null);

function openModal(gamePlayer) {
    selectedPlayer.value = gamePlayer;
    showModal.value = true;
}

function closeModal() {
    selectedPlayer.value = null;
    showModal.value = false;
}

async function updateStat(gamePlayerId, statId, action) {
    const response = await axios.post(route('games.stats.add', props.game.id), {
        game_player_id: gamePlayerId,
        stat_id: statId,
        action: action,
    });
    
    updateLocalStat(gamePlayerId, statId, response.data);

    if (action === 'add' && showModal.value) {
        closeModal();
    }
}

const playerStats = ref({});

function getPlayerStat(gamePlayerId, statId) {
    const key = `${gamePlayerId}-${statId}`;
    return playerStats.value[key]?.value ?? 0;
}

function updateLocalStat(gamePlayerId, statId, data) {
    const key = `${gamePlayerId}-${statId}`;
    playerStats.value[key] = data;
}
</script>