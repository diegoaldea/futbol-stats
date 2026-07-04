<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto flex min-h-screen max-w-3xl flex-col p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: `${teamAName} vs ${teamBName}` },
            ]" />

            <!-- Marcador -->
            <div class="mt-4 rounded-xl bg-white p-5 shadow">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1 text-center">
                        <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-sky-600">{{ teamAName }}</div>
                        <div class="font-mono text-5xl font-semibold leading-none text-slate-800">{{ game.score_team_a }}</div>
                        <div class="mt-2 font-mono text-sm font-semibold text-emerald-600">★ {{ teamRating.a }}</div>
                    </div>
                    <div class="font-mono text-2xl text-slate-300">–</div>
                    <div class="flex-1 text-center">
                        <div class="mb-1 text-xs font-semibold uppercase tracking-wider text-amber-600">{{ teamBName }}</div>
                        <div class="font-mono text-5xl font-semibold leading-none text-slate-800">{{ game.score_team_b }}</div>
                        <div class="mt-2 font-mono text-sm font-semibold text-emerald-600">★ {{ teamRating.b }}</div>
                    </div>
                </div>

                <p class="mt-3 text-center text-xs text-slate-400">
                    {{ matchActions }} acciones registradas · {{ players.length }} jugadores
                </p>

                <!-- % de acciones por equipo -->
                <div class="mt-4 flex h-6 overflow-hidden rounded-full text-xs font-medium text-white">
                    <div class="flex items-center justify-center bg-sky-500" :style="{ width: teamPct.a + '%' }">
                        {{ teamPct.a }}%
                    </div>
                    <div class="flex items-center justify-center bg-amber-500" :style="{ width: teamPct.b + '%' }">
                        {{ teamPct.b }}%
                    </div>
                </div>
                <div class="mt-1 flex justify-between text-xs text-slate-500">
                    <span>{{ teamAName }}</span>
                    <span>{{ teamBName }}</span>
                </div>

                <!-- Copiar -->
                <div class="mt-4 flex justify-center gap-2">
                    <button
                        class="rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100"
                        @click="copy('text')"
                    >
                        {{ copied === 'text' ? '¡Copiado!' : 'Copiar texto' }}
                    </button>
                    <button
                        class="rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-600 transition hover:bg-slate-100"
                        @click="copy('json')"
                    >
                        {{ copied === 'json' ? '¡Copiado!' : 'Copiar JSON' }}
                    </button>
                </div>
            </div>

            <!-- Pestañas -->
            <div class="mt-4 flex flex-wrap gap-1 border-b border-slate-200">
                <button
                    v-for="t in tabs"
                    :key="t.key"
                    class="-mb-px border-b-2 px-4 py-2 text-sm font-medium transition"
                    :class="tab === t.key
                        ? 'border-emerald-500 text-emerald-600'
                        : 'border-transparent text-slate-500 hover:text-slate-700'"
                    @click="tab = t.key"
                >
                    {{ t.label }}
                </button>
            </div>

            <!-- Ranking / Tops -->
            <div v-if="tab === 'ranking'" class="mt-4 flex-1 rounded-lg bg-white p-2 shadow">
                <div class="mb-1 flex items-center gap-2 px-2 pt-2">
                    <label class="text-xs font-semibold text-slate-500">Top de:</label>
                    <select v-model="rankMetric" class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                        <option v-for="opt in rankMetricKeys" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                </div>
                <button
                    v-for="(p, index) in rankedByMetric"
                    :key="p.id"
                    class="flex w-full items-center gap-3 border-b border-slate-100 px-2 py-3 text-left transition last:border-0 hover:bg-slate-50"
                    @click="openPlayer(p)"
                >
                    <span class="w-6 text-center font-mono font-semibold text-slate-400">{{ index + 1 }}</span>
                    <span class="h-2 w-2 shrink-0 rounded-full" :class="teamDot(p.team)"></span>
                    <span class="flex-1">
                        <span class="font-medium text-slate-800">{{ p.name }}</span>
                        <span class="text-xs text-slate-400"> · {{ p.teamName }}</span>
                    </span>
                    <span class="font-mono text-lg font-semibold text-emerald-600">{{ p.metricValue }}</span>
                </button>
            </div>

            <!-- Jugadores (detalle completo por jugador) -->
            <div v-else-if="tab === 'jugadores'" class="mt-4 flex flex-1 flex-col gap-4 md:flex-row">
                <section
                    v-for="group in playerGroups"
                    :key="group.name"
                    class="flex-1 rounded-lg bg-white p-4 shadow"
                >
                    <h3 class="mb-2 text-xl font-bold" :class="group.team === 'a' ? 'text-sky-600' : 'text-amber-600'">
                        {{ group.name }}
                    </h3>
                    <div class="flex flex-col divide-y divide-slate-200">
                        <div v-for="p in group.players" :key="p.id" class="py-3">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-800">{{ p.name }}</span>
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-sm font-semibold text-emerald-700">
                                    {{ p.rating }}
                                </span>
                            </div>
                            <PlayerStatsDetail
                                class="mt-2"
                                :player="p"
                                :players="players"
                                :stat-meta="statMeta"
                            />
                        </div>
                    </div>
                </section>
            </div>

            <!-- Comparar -->
            <div v-else-if="tab === 'comparar'" class="mt-4 flex-1 rounded-lg bg-white p-4 shadow">
                <!-- Selección de jugadores -->
                <div class="mb-3">
                    <div class="mb-2 flex items-center justify-between">
                        <label class="text-xs font-semibold text-slate-500">Jugadores:</label>
                        <div class="flex gap-3 text-xs">
                            <button class="text-emerald-600 hover:underline" @click="selectedIds = players.map((p) => p.id)">Todos</button>
                            <button class="text-slate-400 hover:underline" @click="selectedIds = []">Ninguno</button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="p in players"
                            :key="p.id"
                            class="rounded-full border px-2.5 py-0.5 text-xs transition"
                            :class="selectedIds.includes(p.id)
                                ? (p.team === 'a' ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-amber-500 bg-amber-50 text-amber-700')
                                : 'border-slate-200 text-slate-400 hover:border-slate-300'"
                            @click="togglePlayer(p.id)"
                        >
                            {{ p.name }}
                        </button>
                    </div>
                </div>

                <!-- Estadística a comparar -->
                <div class="mb-3 flex items-center gap-2">
                    <label class="text-xs font-semibold text-slate-500">Estadística:</label>
                    <select v-model="cmpStat" class="rounded-md border border-slate-300 px-2 py-1 text-sm">
                        <option v-for="opt in cmpOptions" :key="opt" :value="opt">{{ opt }}</option>
                    </select>
                </div>

                <p v-if="!cmpRows.length" class="text-sm text-slate-400">Elegí jugadores para comparar.</p>
                <div v-for="row in cmpRows" :key="row.name" class="mb-2.5">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-600">{{ row.name }}</span>
                        <span class="font-mono font-semibold text-slate-700">{{ row.value }}</span>
                    </div>
                    <div class="mt-1 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div
                            class="h-full rounded-full"
                            :class="isNegative(cmpStat) ? 'bg-rose-500' : teamBar(row.team)"
                            :style="{ width: cmpWidth(row.value) + '%' }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Comparativa de equipos (stats de partido) -->
            <div v-else-if="tab === 'comparativa'" class="mt-4 flex-1 rounded-lg bg-white p-4 shadow">
                <div class="mb-3 flex justify-between text-sm font-semibold">
                    <span class="text-sky-600">{{ teamAName }}</span>
                    <span class="text-amber-600">{{ teamBName }}</span>
                </div>

                <!-- Destacadas (derivadas) -->
                <div v-for="row in destacadas" :key="row.label" class="mb-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="w-12 font-mono font-medium text-slate-700">{{ row.a }}{{ row.suffix || '' }}</span>
                        <span class="text-slate-500">{{ row.label }}</span>
                        <span class="w-12 text-right font-mono font-medium text-slate-700">{{ row.b }}{{ row.suffix || '' }}</span>
                    </div>
                    <div class="mt-1 flex h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="bg-sky-500" :style="{ width: barPct(row.a, row.b) + '%' }"></div>
                        <div class="bg-amber-500" :style="{ width: (100 - barPct(row.a, row.b)) + '%' }"></div>
                    </div>
                </div>

                <!-- Todas las estadísticas (desplegable) -->
                <button
                    class="mb-2 mt-5 flex w-full items-center justify-between text-xs font-semibold uppercase tracking-wide text-slate-400 transition hover:text-slate-600"
                    @click="showAllStats = !showAllStats"
                >
                    <span>Todas las estadísticas</span>
                    <span>{{ showAllStats ? '▲' : '▼' }}</span>
                </button>
                <div v-for="row in showAllStats ? allComparison : []" :key="row.label" class="mb-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="w-12 font-mono font-medium text-slate-700">{{ row.a }}</span>
                        <span class="text-slate-500">{{ row.label }}</span>
                        <span class="w-12 text-right font-mono font-medium text-slate-700">{{ row.b }}</span>
                    </div>
                    <div class="mt-1 flex h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="bg-sky-500" :style="{ width: barPct(row.a, row.b) + '%' }"></div>
                        <div class="bg-amber-500" :style="{ width: (100 - barPct(row.a, row.b)) + '%' }"></div>
                    </div>
                </div>
            </div>

            <!-- Cronología -->
            <div v-else-if="tab === 'cronologia'" class="mt-4 flex-1 rounded-lg bg-white p-4 shadow">
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Goles</p>
                <StatTimeline :entries="goalEntries" :team-a-name="teamAName" :team-b-name="teamBName" />

                <button
                    class="mb-2 mt-5 flex w-full items-center justify-between text-xs font-semibold uppercase tracking-wide text-slate-400 transition hover:text-slate-600"
                    @click="showFullHistory = !showFullHistory"
                >
                    <span>Historial completo</span>
                    <span>{{ showFullHistory ? '▲' : '▼' }}</span>
                </button>
                <StatTimeline
                    v-if="showFullHistory"
                    :entries="history"
                    :team-a-name="teamAName"
                    :team-b-name="teamBName"
                />
            </div>
        </div>

        <!-- Modal ficha jugador -->
        <PlayerStatsModal
            v-if="selectedPlayer"
            :player="selectedPlayer"
            :players="players"
            :stat-meta="statMeta"
            @close="selectedPlayer = null"
        />
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import StatTimeline from '@/Components/StatTimeline.vue';
import PlayerStatsModal from '@/Components/PlayerStatsModal.vue';
import PlayerStatsDetail from '@/Components/PlayerStatsDetail.vue';

const props = defineProps({
    game: Object,
    teamA: Array,
    teamB: Array,
    history: { type: Array, default: () => [] },
});

const teamAName = computed(() => props.game.team_a_name || 'Equipo A');
const teamBName = computed(() => props.game.team_b_name || 'Equipo B');

const tabs = [
    { key: 'ranking', label: 'Ranking' },
    { key: 'jugadores', label: 'Jugadores' },
    { key: 'comparar', label: 'Comparar' },
    { key: 'comparativa', label: 'Equipos' },
    { key: 'cronologia', label: 'Cronología' },
];
const tab = ref('ranking');

function teamDot(team) {
    return team === 'a' ? 'bg-sky-500' : 'bg-amber-500';
}
function teamBar(team) {
    return team === 'a' ? 'bg-sky-500' : 'bg-amber-500';
}

const BASE_SCORE = 6.0;

// --- Calificación 0-10 (nueva, para comparar; NO reemplaza el rating total) ---
const RATING_BASE = 6.0; // neto 0 -> 6 (rendimiento neutro)
const RATING_MAX = 10.0; // tope: nunca supera 10
const RATING_FLOOR = 1.0; // piso: nunca baja de acá
const RATING_UMBRAL = 20; // neto de "un partido de 10" (configurable)

function calcRating(neto) {
    const r = RATING_BASE + (RATING_MAX - RATING_BASE) * (neto / RATING_UMBRAL);
    return Number(Math.min(RATING_MAX, Math.max(RATING_FLOOR, r)).toFixed(2));
}

function playerScore(gamePlayer) {
    const total = (gamePlayer.stats ?? []).reduce(
        (sum, s) => sum + s.value * Number(s.stat.points),
        BASE_SCORE
    );
    return Number(total.toFixed(2));
}

// Jugadores normalizados
const players = computed(() => {
    const build = (list, team, teamName) =>
        list.map((gp) => {
            const stats = {};
            (gp.stats ?? []).forEach((s) => {
                stats[s.stat.name] = s.value;
            });
            return {
                id: gp.id,
                name: gp.player.name,
                team,
                teamName,
                rating: playerScore(gp),
                actions: (gp.stats ?? []).reduce((sum, s) => sum + s.value, 0),
                stats,
            };
        });

    const base = [
        ...build(props.teamA, 'a', teamAName.value),
        ...build(props.teamB, 'b', teamBName.value),
    ];

    // Calificación 0-10 a partir del neto (rating total - base)
    return base.map((p) => ({ ...p, calificacion: calcRating(p.rating - BASE_SCORE) }));
});

// Metadata de cada stat (puntos + categoría)
const statMeta = computed(() => {
    const meta = {};
    [...props.teamA, ...props.teamB].forEach((gp) => {
        (gp.stats ?? []).forEach((s) => {
            if (!meta[s.stat.name]) {
                meta[s.stat.name] = {
                    points: Number(s.stat.points),
                    category: s.stat.category?.name ?? null,
                };
            }
        });
    });
    return meta;
});

const allStats = computed(() => Object.keys(statMeta.value).sort());

function isNegative(stat) {
    return (statMeta.value[stat]?.points ?? 0) < 0;
}

// --- Ranking / Tops ---
function sumStats(p, names) {
    return names.reduce((s, n) => s + (p.stats[n] ?? 0), 0);
}

const rankMetrics = {
    Rating: (p) => p.rating,
    'Calificación (0-10)': (p) => p.calificacion,
    Acciones: (p) => p.actions,
    Goles: (p) => p.stats['Gol'] ?? 0,
    Asistencias: (p) => p.stats['Asistencia'] ?? 0,
    Pases: (p) => sumStats(p, ['Pase progresivo', 'Buen pase', 'Mal pase', 'Pase incompleto', 'Asistencia']),
    Remates: (p) => sumStats(p, ['Disparo al arco', 'Disparo afuera', 'Gol']),
    'Duelos ganados': (p) => p.stats['Duelo ganado'] ?? 0,
    Intercepciones: (p) => p.stats['Intercepción'] ?? 0,
    Atajadas: (p) => p.stats['Atajada'] ?? 0,
    Defensa: (p) =>
        Object.entries(p.stats).reduce(
            (s, [name, v]) => s + (statMeta.value[name]?.category === 'Defensa' ? v : 0),
            0
        ),
    Aportes: (p) =>
        Object.entries(p.stats).reduce(
            (s, [name, v]) => s + ((statMeta.value[name]?.points ?? 0) >= 0 ? v : 0),
            0
        ),
    Errores: (p) =>
        Object.entries(p.stats).reduce(
            (s, [name, v]) => s + ((statMeta.value[name]?.points ?? 0) < 0 ? v : 0),
            0
        ),
};
const rankMetricKeys = Object.keys(rankMetrics);
const rankMetric = ref('Rating');

const rankedByMetric = computed(() =>
    players.value
        .map((p) => ({ ...p, metricValue: rankMetrics[rankMetric.value](p) }))
        .sort((a, b) => b.metricValue - a.metricValue)
);

const selectedPlayer = ref(null);
function openPlayer(p) {
    selectedPlayer.value = p;
}

// Jugadores agrupados por equipo (para la pestaña Jugadores)
const playerGroups = computed(() => [
    { name: teamAName.value, team: 'a', players: players.value.filter((p) => p.team === 'a') },
    { name: teamBName.value, team: 'b', players: players.value.filter((p) => p.team === 'b') },
]);

// --- Protagonismo (% de acciones) ---
const matchActions = computed(() => players.value.reduce((s, p) => s + p.actions, 0));
const teamActions = computed(() => ({
    a: players.value.filter((p) => p.team === 'a').reduce((s, p) => s + p.actions, 0),
    b: players.value.filter((p) => p.team === 'b').reduce((s, p) => s + p.actions, 0),
}));
const teamPct = computed(() => {
    const total = teamActions.value.a + teamActions.value.b;
    if (!total) return { a: 0, b: 0 };
    const a = Math.round((teamActions.value.a / total) * 100);
    return { a, b: 100 - a };
});

// --- Comparar (jugadores elegidos, por una stat) ---
const cmpStat = ref('Rating');
const cmpOptions = computed(() => ['Rating', 'Calificación (0-10)', 'Acciones', ...allStats.value]);
const selectedIds = ref(players.value.map((p) => p.id));

function togglePlayer(id) {
    selectedIds.value = selectedIds.value.includes(id)
        ? selectedIds.value.filter((x) => x !== id)
        : [...selectedIds.value, id];
}

function cmpValue(p, stat) {
    if (stat === 'Rating') return p.rating;
    if (stat === 'Calificación (0-10)') return p.calificacion;
    if (stat === 'Acciones') return p.actions;
    return p.stats[stat] ?? 0;
}
const cmpRows = computed(() =>
    players.value
        .filter((p) => selectedIds.value.includes(p.id))
        .map((p) => ({ name: p.name, team: p.team, value: cmpValue(p, cmpStat.value) }))
        .sort((a, b) => b.value - a.value)
);
function cmpWidth(value) {
    const max = Math.max(...cmpRows.value.map((r) => Math.abs(r.value)), 1);
    return (Math.abs(value) / max) * 100;
}

// --- Comparativa de equipos (stats de partido) ---
function teamStatSum(team, name) {
    return players.value
        .filter((p) => p.team === team)
        .reduce((sum, p) => sum + (p.stats[name] ?? 0), 0);
}

// Precisión de pases: buenos / (buenos + malos)
function passAccuracy(team) {
    const good =
        teamStatSum(team, 'Pase progresivo') +
        teamStatSum(team, 'Buen pase') +
        teamStatSum(team, 'Asistencia');
    const bad = teamStatSum(team, 'Mal pase') + teamStatSum(team, 'Pase incompleto');
    const total = good + bad;
    return total ? Math.round((good / total) * 100) : 0;
}

// Rating del equipo = promedio de los ratings de sus jugadores
const teamRating = computed(() => {
    const avg = (team) => {
        const ps = players.value.filter((p) => p.team === team);
        if (!ps.length) return 0;
        return Number((ps.reduce((s, p) => s + p.rating, 0) / ps.length).toFixed(2));
    };
    return { a: avg('a'), b: avg('b') };
});

// Métricas principales de partido
const destacadas = computed(() => {
    const s = teamStatSum;
    return [
        { label: 'Remates al arco', a: s('a', 'Disparo al arco') + s('a', 'Gol'), b: s('b', 'Disparo al arco') + s('b', 'Gol') },
        { label: 'Remates afuera', a: s('a', 'Disparo afuera'), b: s('b', 'Disparo afuera') },
        { label: 'Precisión de pases', a: passAccuracy('a'), b: passAccuracy('b'), suffix: '%' },
        { label: 'Gambetas exitosas', a: s('a', 'Gambeta exitosa'), b: s('b', 'Gambeta exitosa') },
        { label: 'Duelos ganados', a: s('a', 'Duelo ganado'), b: s('b', 'Duelo ganado') },
        { label: 'Intercepciones', a: s('a', 'Intercepción'), b: s('b', 'Intercepción') },
        { label: 'Atajadas', a: s('a', 'Atajada'), b: s('b', 'Atajada') },
        { label: 'Faltas', a: s('a', 'Falta cometida'), b: s('b', 'Falta cometida') },
    ];
});

const showAllStats = ref(false);

// --- Cronología ---
const showFullHistory = ref(false);
const goalEntries = computed(() =>
    props.history.filter((e) => {
        const n = e.stat.toLowerCase();
        return n === 'gol' || n === 'gol en contra';
    })
);

// Todas las estadísticas comparadas A vs B, ordenadas por total
const allComparison = computed(() =>
    allStats.value
        .map((name) => ({ label: name, a: teamStatSum('a', name), b: teamStatSum('b', name) }))
        .sort((x, y) => (y.a + y.b) - (x.a + x.b))
);

function barPct(a, b) {
    const total = a + b;
    return total ? Math.round((a / total) * 100) : 50;
}

// --- Copiar al portapapeles ---
const copied = ref(null);

function buildText() {
    const lines = [`${teamAName.value} ${props.game.score_team_a} - ${props.game.score_team_b} ${teamBName.value}`, ''];
    for (const teamName of [teamAName.value, teamBName.value]) {
        lines.push(`== ${teamName} ==`);
        players.value
            .filter((p) => p.teamName === teamName)
            .forEach((p) => {
                lines.push(`${p.name} (rating ${p.rating}, ${p.actions} acciones)`);
                Object.entries(p.stats)
                    .sort((a, b) => b[1] - a[1])
                    .forEach(([stat, value]) => lines.push(`  ${stat}: ${value}`));
            });
        lines.push('');
    }
    return lines.join('\n').trim();
}

function buildJson() {
    const payload = {
        resultado: {
            equipoA: teamAName.value,
            equipoB: teamBName.value,
            golesA: props.game.score_team_a,
            golesB: props.game.score_team_b,
        },
        equipos: {},
    };
    for (const teamName of [teamAName.value, teamBName.value]) {
        payload.equipos[teamName] = {};
        players.value
            .filter((p) => p.teamName === teamName)
            .forEach((p) => {
                payload.equipos[teamName][p.name] = {
                    rating: p.rating,
                    acciones: p.actions,
                    stats: p.stats,
                };
            });
    }
    return JSON.stringify(payload, null, 2);
}

async function copy(format) {
    const text = format === 'json' ? buildJson() : buildText();
    await navigator.clipboard.writeText(text);
    copied.value = format;
    setTimeout(() => (copied.value = null), 1500);
}
</script>
