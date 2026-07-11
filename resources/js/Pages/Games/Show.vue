<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-6xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: `${teamAName} vs ${teamBName}` },
            ]" />
        </div>

        <!-- Marcador -->
        <header class="bg-emerald-500 py-6 text-white">
            <Scoreboard
                class="!text-white [&_span]:text-white text-3xl md:text-4xl"
                :team-a="teamAName"
                :team-b="teamBName"
                :score-a="score.team_a"
                :score-b="score.team_b"
            />
        </header>

        <!-- Barra: switch de ratings + historial -->
        <div class="mx-auto flex max-w-6xl items-center justify-end gap-4 px-4 pt-4">
            <button
                class="flex items-center gap-2 text-sm font-medium text-slate-600"
                @click="toggleAllRatings"
            >
                <span
                    class="relative h-5 w-9 rounded-full transition"
                    :class="ratingsVisible ? 'bg-emerald-500' : 'bg-slate-300'"
                >
                    <span
                        class="absolute top-0.5 h-4 w-4 rounded-full bg-white transition-all"
                        :class="ratingsVisible ? 'left-4' : 'left-0.5'"
                    ></span>
                </span>
                Ratings
            </button>

            <StatHistory :entries="history" :team-a-name="teamAName" :team-b-name="teamBName" />
        </div>

        <!-- Equipos -->
        <main class="mx-auto flex max-w-6xl flex-col gap-4 p-4 md:flex-row">
            <TeamPanel
                :name="teamAName"
                :players="teamA"
                :stats="stats"
                :get-value="getPlayerStat"
                :get-color="statColor"
                :get-rating="playerRating"
                :is-rating-visible="isRatingVisible"
                :last-activity="lastActivityA"
                @open="openModal"
                @add="onAdd"
                @subtract="onSubtract"
                @toggle-rating="toggleRatingPlayer"
            />
            <TeamPanel
                :name="teamBName"
                :players="teamB"
                :stats="stats"
                :get-value="getPlayerStat"
                :get-color="statColor"
                :get-rating="playerRating"
                :is-rating-visible="isRatingVisible"
                :last-activity="lastActivityB"
                @open="openModal"
                @add="onAdd"
                @subtract="onSubtract"
                @toggle-rating="toggleRatingPlayer"
            />
        </main>

        <div class="mx-auto max-w-6xl px-4 pb-8">
            <button
                v-if="!isFinished"
                class="rounded-md bg-slate-800 px-4 py-2 text-white hover:bg-slate-700"
                @click="finishGame"
            >
                Finalizar partido
            </button>
            <p v-else class="font-medium text-slate-500">
                Partido finalizado — no se pueden cargar más estadísticas.
            </p>
        </div>

        <!-- Modal de agregar estadística -->
        <AddStatModal
            v-if="showModal"
            :title="selectedPlayer.player.name"
            :stats="availableStats"
            @select="onSelectStat"
            @close="closeModal"
        />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { momentumRating } from '@/rating';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Scoreboard from '@/Components/Scoreboard.vue';
import StatHistory from '@/Components/StatHistory.vue';
import TeamPanel from '@/Components/TeamPanel.vue';
import AddStatModal from '@/Components/AddStatModal.vue';

const props = defineProps({
    game: Object,
    teamA: Array,
    teamB: Array,
    stats: Array,
    history: { type: Array, default: () => [] },
});

const selectedPlayer = ref(null);
const showModal = ref(false);

// Historial de estadísticas (solo sumas), inicializado con lo guardado en la base
const history = ref([...props.history]);

const lastActivityA = computed(
    () => [...history.value].reverse().find((entry) => entry.team === 'a') ?? null
);
const lastActivityB = computed(
    () => [...history.value].reverse().find((entry) => entry.team === 'b') ?? null
);

function playerName(gamePlayerId) {
    const gamePlayer = [...props.teamA, ...props.teamB].find((gp) => gp.id === gamePlayerId);
    return gamePlayer?.player?.name ?? '';
}

function playerTeam(gamePlayerId) {
    return props.teamA.some((gp) => gp.id === gamePlayerId) ? 'a' : 'b';
}

function statName(statId) {
    return props.stats.find((stat) => stat.id === statId)?.name ?? '';
}

const score = ref({
    team_a: props.game.score_team_a,
    team_b: props.game.score_team_b,
});

const teamAName = computed(() => props.game.team_a_name || 'Equipo A');
const teamBName = computed(() => props.game.team_b_name || 'Equipo B');
const isFinished = computed(() => !!props.game.finished_at);

// --- Calificación parcial (rating) ---
const ratingsVisible = ref(true); // estado del switch general
const playerVisible = ref({}); // estado individual por jugador

// Puntos de cada stat por id (para el momentum)
const statPointsById = computed(() => {
    const m = {};
    props.stats.forEach((s) => {
        m[s.id] = Number(s.points);
    });
    return m;
});

// Rating "Momentum": recorre las acciones del jugador EN ORDEN (historial)
function playerRating(gamePlayerId) {
    const points = history.value
        .filter((e) => e.gamePlayerId === gamePlayerId)
        .map((e) => statPointsById.value[e.statId] ?? 0);
    return momentumRating(points);
}

// Si el jugador no tiene estado propio, sigue al switch general
function isRatingVisible(gamePlayerId) {
    return playerVisible.value[gamePlayerId] ?? ratingsVisible.value;
}

// El switch general fuerza a todos los jugadores a su estado
function toggleAllRatings() {
    ratingsVisible.value = !ratingsVisible.value;

    const all = {};
    [...props.teamA, ...props.teamB].forEach((gamePlayer) => {
        all[gamePlayer.id] = ratingsVisible.value;
    });
    playerVisible.value = all;
}

// Cada cuadrado es un switch propio
function toggleRatingPlayer(gamePlayerId) {
    playerVisible.value[gamePlayerId] = !isRatingVisible(gamePlayerId);
}

// Color del tag según la categoría de la estadística
const categoryColors = [
    'bg-rose-200',
    'bg-sky-200',
    'bg-amber-200',
    'bg-emerald-200',
    'bg-violet-200',
    'bg-orange-200',
    'bg-teal-200',
    'bg-pink-200',
];

function statColor(stat) {
    const categoryId = stat.stat_category_id ?? stat.category?.id ?? 0;
    return categoryColors[categoryId % categoryColors.length];
}

// Stats que el jugador seleccionado todavía no tiene (las ya cargadas no se muestran)
const availableStats = computed(() => {
    if (!selectedPlayer.value) return [];
    return props.stats.filter((stat) => getPlayerStat(selectedPlayer.value.id, stat.id) === 0);
});

function openModal(gamePlayer) {
    if (isFinished.value) return;
    selectedPlayer.value = gamePlayer;
    showModal.value = true;
}

function closeModal() {
    selectedPlayer.value = null;
    showModal.value = false;
}

function onAdd({ gamePlayerId, statId }) {
    updateStat(gamePlayerId, statId, 'add');
}

function onSubtract({ gamePlayerId, statId }) {
    updateStat(gamePlayerId, statId, 'subtract');
}

function onSelectStat(statId) {
    updateStat(selectedPlayer.value.id, statId, 'add');
}

async function updateStat(gamePlayerId, statId, action) {
    if (isFinished.value) return;

    const response = await axios.post(route('games.stats.add', props.game.id), {
        game_player_id: gamePlayerId,
        stat_id: statId,
        action: action,
    });

    updateLocalStat(gamePlayerId, statId, response.data.gameStat);
    score.value.team_a = response.data.game.score_team_a;
    score.value.team_b = response.data.game.score_team_b;

    if (action === 'add') {
        history.value.push({
            player: playerName(gamePlayerId),
            stat: statName(statId),
            team: playerTeam(gamePlayerId),
            gamePlayerId,
            statId,
        });
    } else {
        // Quitar del historial el último evento de esa stat para ese jugador
        for (let i = history.value.length - 1; i >= 0; i--) {
            if (history.value[i].gamePlayerId === gamePlayerId && history.value[i].statId === statId) {
                history.value.splice(i, 1);
                break;
            }
        }
    }

    if (action === 'add' && showModal.value) {
        closeModal();
    }
}

const playerStats = ref({});

// Inicializar con las stats que ya existen en la base de datos
[...props.teamA, ...props.teamB].forEach((gamePlayer) => {
    gamePlayer.stats?.forEach((stat) => {
        playerStats.value[`${gamePlayer.id}-${stat.stat_id}`] = stat;
    });
});

function getPlayerStat(gamePlayerId, statId) {
    const key = `${gamePlayerId}-${statId}`;
    return playerStats.value[key]?.value ?? 0;
}

function updateLocalStat(gamePlayerId, statId, data) {
    const key = `${gamePlayerId}-${statId}`;
    playerStats.value[key] = data;
}

async function finishGame() {
    if (confirm('¿Estás seguro que querés finalizar el partido?')) {
        await axios.post(route('games.finish', props.game.id));
        router.visit(route('games.summary', props.game.id));
    }
}
</script>
