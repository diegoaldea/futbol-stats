<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto flex min-h-screen max-w-3xl flex-col p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Rendimiento' },
            ]" />

            <h1 class="mt-4 text-2xl font-bold text-slate-800">Rendimiento de jugadores</h1>
            <p class="text-sm text-slate-400">
                {{ gamesCount }} partidos · {{ agg.length }} jugadores · {{ totalGoles }} goles en total
            </p>

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

            <!-- Ranking -->
            <div v-if="tab === 'ranking'" class="mt-4 flex-1">
                <div class="mb-3 inline-flex rounded-lg border border-slate-200 bg-white p-1 text-xs font-medium">
                    <button
                        class="rounded-md px-3 py-1"
                        :class="rankMode === 'prom' ? 'bg-slate-100 text-slate-800' : 'text-slate-500'"
                        @click="rankMode = 'prom'"
                    >Promedio</button>
                    <button
                        class="rounded-md px-3 py-1"
                        :class="rankMode === 'tot' ? 'bg-slate-100 text-slate-800' : 'text-slate-500'"
                        @click="rankMode = 'tot'"
                    >Total acumulado</button>
                </div>

                <div class="rounded-lg bg-white p-2 shadow">
                    <button
                        v-for="(a, index) in ranking"
                        :key="a.id"
                        class="flex w-full items-center gap-3 border-b border-slate-100 px-2 py-3 text-left transition last:border-0 hover:bg-slate-50"
                        @click="openPlayer(a)"
                    >
                        <span class="w-6 text-center font-mono font-semibold text-slate-400">{{ index + 1 }}</span>
                        <span class="flex-1 font-medium text-slate-800">{{ a.name }}</span>
                        <span class="text-right">
                            <span class="block font-mono text-lg font-semibold text-emerald-600">
                                {{ rankMode === 'prom' ? a.ratingProm.toFixed(2) : a.ratingTot.toFixed(1) }}
                            </span>
                            <span class="block text-xs text-slate-400">{{ a.pj }} PJ · {{ rankMode === 'prom' ? 'prom' : 'total' }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Goleadores -->
            <div v-else-if="tab === 'goleadores'" class="mt-4 flex-1 overflow-x-auto rounded-lg bg-white p-2 shadow">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-2 py-2 font-medium">#</th>
                            <th class="px-2 py-2 font-medium">Jugador</th>
                            <th class="px-2 py-2 text-right font-medium">Goles</th>
                            <th class="px-2 py-2 text-right font-medium">Asist.</th>
                            <th class="px-2 py-2 text-right font-medium">G+A</th>
                            <th class="px-2 py-2 text-right font-medium">G/part</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(r, index) in goleadores"
                            :key="r.id"
                            class="cursor-pointer border-b border-slate-100 last:border-0 hover:bg-slate-50"
                            @click="openPlayer(r.agg)"
                        >
                            <td class="px-2 py-2 text-slate-400">{{ index + 1 }}</td>
                            <td class="px-2 py-2 font-medium text-slate-800">{{ r.name }}</td>
                            <td class="px-2 py-2 text-right font-mono">{{ r.g }}</td>
                            <td class="px-2 py-2 text-right font-mono">{{ r.a }}</td>
                            <td class="px-2 py-2 text-right font-mono font-semibold text-emerald-600">{{ r.g + r.a }}</td>
                            <td class="px-2 py-2 text-right font-mono text-slate-500">{{ (r.g / r.pj).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Por estadística -->
            <div v-else-if="tab === 'tops'" class="mt-4 flex-1 rounded-lg bg-white p-4 shadow">
                <select v-model="statSel" class="mb-3 w-full rounded-md border border-slate-300 px-2 py-2 text-sm">
                    <option v-for="s in allStats" :key="s" :value="s">{{ s }}</option>
                </select>
                <div class="mb-3 inline-flex rounded-lg border border-slate-200 p-1 text-xs font-medium">
                    <button
                        class="rounded-md px-3 py-1"
                        :class="lidMode === 'tot' ? 'bg-slate-100 text-slate-800' : 'text-slate-500'"
                        @click="lidMode = 'tot'"
                    >Total</button>
                    <button
                        class="rounded-md px-3 py-1"
                        :class="lidMode === 'prom' ? 'bg-slate-100 text-slate-800' : 'text-slate-500'"
                        @click="lidMode = 'prom'"
                    >Promedio/partido</button>
                </div>
                <template v-if="lideres.length">
                    <div v-for="row in lideres" :key="row.id" class="mb-2.5">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-600">{{ row.name }}</span>
                            <span class="font-mono font-semibold text-slate-700">{{ row.display }}</span>
                        </div>
                        <div class="mt-1 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div
                                class="h-full rounded-full"
                                :class="isNeg(statSel) ? 'bg-rose-500' : 'bg-emerald-500'"
                                :style="{ width: (row.value / lideresMax) * 100 + '%' }"
                            ></div>
                        </div>
                    </div>
                </template>
                <p v-else class="text-sm text-slate-400">Sin registros.</p>
            </div>

            <!-- Comparar -->
            <div v-else-if="tab === 'comparar'" class="mt-4 flex-1 rounded-lg bg-white p-4 shadow">
                <div class="mb-4 flex gap-2">
                    <select v-model="cmpA" class="w-1/2 rounded-md border border-slate-300 px-2 py-2 text-sm">
                        <option v-for="a in agg" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                    <select v-model="cmpB" class="w-1/2 rounded-md border border-slate-300 px-2 py-2 text-sm">
                        <option v-for="a in agg" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                </div>
                <template v-if="cmpPlayers.a && cmpPlayers.b">
                    <div class="mb-3 flex justify-between text-sm font-semibold">
                        <span class="text-sky-600">{{ cmpPlayers.a.name }}</span>
                        <span class="text-amber-600">{{ cmpPlayers.b.name }}</span>
                    </div>
                    <div v-for="row in cmpMetrics" :key="row.label" class="mb-3">
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
                </template>
            </div>
        </div>

        <!-- Modal ficha (temporada) -->
        <div
            v-if="selected"
            class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center"
            @click.self="selected = null"
        >
            <div class="max-h-[88vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white p-5 shadow-xl sm:rounded-2xl">
                <div class="flex items-start justify-between">
                    <h2 class="text-2xl font-bold text-slate-800">{{ selected.name }}</h2>
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200"
                        @click="selected = null"
                    >✕</button>
                </div>

                <!-- Métricas -->
                <div class="mt-4 grid grid-cols-3 gap-2">
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-emerald-600">{{ selected.ratingProm.toFixed(2) }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Rating prom</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-slate-800">{{ selected.pj }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Partidos</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-slate-800">{{ selected.accProm }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Acc/partido</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-emerald-600">{{ selected.ratingMax.toFixed(1) }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Mejor</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-rose-600">{{ selected.ratingMin.toFixed(1) }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Peor</div>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <div class="font-mono text-xl font-semibold text-slate-800">{{ selected.accTot }}</div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400">Acc totales</div>
                    </div>
                </div>

                <!-- Evolución del rating -->
                <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-400">Rating por partido</p>
                <div class="mt-2 flex h-24 items-end gap-1.5 rounded-lg bg-slate-50 p-3">
                    <div
                        v-for="(r, i) in selected.ratings"
                        :key="i"
                        class="flex-1 rounded-t bg-emerald-500"
                        :style="{ height: Math.max((r / selected.ratingMax) * 100, 4) + '%' }"
                        :title="`P${i + 1}: ${r}`"
                    ></div>
                </div>

                <!-- Aportes -->
                <p class="mt-5 text-xs font-semibold uppercase tracking-wide text-slate-400">Aportes (total y promedio)</p>
                <ul class="mt-1">
                    <li v-for="[stat, value] in selectedAportes" :key="stat" class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0">
                        <span class="text-sm text-slate-700">{{ stat }}</span>
                        <span class="flex items-center gap-3">
                            <span class="rounded-full px-2 py-0.5 text-xs" :class="rankOf(selected, stat)?.pos === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">{{ rankLabel(selected, stat) }}</span>
                            <span class="font-mono font-semibold text-slate-800">{{ value }} <span class="text-xs font-normal text-slate-400">({{ (value / selected.pj).toFixed(1) }}/p)</span></span>
                        </span>
                    </li>
                </ul>

                <!-- Errores -->
                <template v-if="selectedErrores.length">
                    <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Errores</p>
                    <ul class="mt-1">
                        <li v-for="[stat, value] in selectedErrores" :key="stat" class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0">
                            <span class="text-sm text-slate-700">{{ stat }}</span>
                            <span class="flex items-center gap-3">
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ rankLabel(selected, stat) }}</span>
                                <span class="font-mono font-semibold text-rose-600">{{ value }} <span class="text-xs font-normal text-slate-400">({{ (value / selected.pj).toFixed(1) }}/p)</span></span>
                            </span>
                        </li>
                    </ul>
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

const props = defineProps({
    players: { type: Array, default: () => [] },
    statMeta: { type: Object, default: () => ({}) },
    gamesCount: { type: Number, default: 0 },
});

const tabs = [
    { key: 'ranking', label: 'Ranking' },
    { key: 'goleadores', label: 'Goleadores' },
    { key: 'tops', label: 'Por estadística' },
    { key: 'comparar', label: 'Comparar' },
];
const tab = ref('ranking');

function isNeg(stat) {
    return (props.statMeta[stat] ?? 0) < 0;
}

// Agregados por jugador
const agg = computed(() =>
    props.players.map((p) => {
        const totals = {};
        p.matches.forEach((m) => {
            for (const [k, v] of Object.entries(m.stats)) totals[k] = (totals[k] || 0) + v;
        });
        const ratings = p.matches.map((m) => m.rating);
        const accs = p.matches.map((m) => m.acciones);
        const pj = p.matches.length || 1;
        return {
            id: p.id,
            name: p.name,
            pj: p.matches.length,
            totals,
            ratings,
            ratingProm: Number((ratings.reduce((a, b) => a + b, 0) / pj).toFixed(2)),
            ratingTot: Number(ratings.reduce((a, b) => a + b, 0).toFixed(2)),
            ratingMax: ratings.length ? Math.max(...ratings) : 0,
            ratingMin: ratings.length ? Math.min(...ratings) : 0,
            accProm: Number((accs.reduce((a, b) => a + b, 0) / pj).toFixed(1)),
            accTot: accs.reduce((a, b) => a + b, 0),
        };
    })
);

const allStats = computed(() =>
    [...new Set(agg.value.flatMap((a) => Object.keys(a.totals)))].sort()
);
const totalGoles = computed(() => agg.value.reduce((s, a) => s + (a.totals['Gol'] || 0), 0));

// --- Ranking ---
const rankMode = ref('prom');
const ranking = computed(() =>
    [...agg.value].sort((x, y) =>
        rankMode.value === 'prom' ? y.ratingProm - x.ratingProm : y.ratingTot - x.ratingTot
    )
);

// --- Goleadores ---
const goleadores = computed(() =>
    agg.value
        .map((a) => ({ id: a.id, name: a.name, g: a.totals['Gol'] || 0, a: a.totals['Asistencia'] || 0, pj: a.pj, agg: a }))
        .sort((x, y) => (y.g + y.a) - (x.g + x.a) || y.g - x.g)
);

// --- Por estadística ---
const statSel = ref('Gol');
const lidMode = ref('tot');
const lideres = computed(() => {
    const rows = agg.value
        .map((a) => {
            const tot = a.totals[statSel.value] || 0;
            const prom = tot / a.pj;
            return { id: a.id, name: a.name, tot, prom, value: lidMode.value === 'tot' ? tot : prom };
        })
        .filter((r) => r.tot > 0)
        .sort((x, y) => y.value - x.value);
    return rows.map((r) => ({ ...r, display: lidMode.value === 'tot' ? r.tot : r.prom.toFixed(2) }));
});
const lideresMax = computed(() => (lideres.value.length ? lideres.value[0].value : 1));

// --- Comparar ---
const cmpA = ref(props.players[0]?.id ?? null);
const cmpB = ref(props.players[1]?.id ?? props.players[0]?.id ?? null);
const cmpPlayers = computed(() => ({
    a: agg.value.find((x) => x.id === cmpA.value) ?? null,
    b: agg.value.find((x) => x.id === cmpB.value) ?? null,
}));
const cmpMetrics = computed(() => {
    const A = cmpPlayers.value.a;
    const B = cmpPlayers.value.b;
    if (!A || !B) return [];
    return [
        { label: 'Rating prom', a: A.ratingProm, b: B.ratingProm },
        { label: 'Partidos', a: A.pj, b: B.pj },
        { label: 'Goles', a: A.totals['Gol'] || 0, b: B.totals['Gol'] || 0 },
        { label: 'Asistencias', a: A.totals['Asistencia'] || 0, b: B.totals['Asistencia'] || 0 },
        { label: 'Acc/partido', a: A.accProm, b: B.accProm },
    ];
});
function barPct(a, b) {
    const total = a + b;
    return total ? Math.round((a / total) * 100) : 50;
}

// --- Ficha (modal) ---
const selected = ref(null);
function openPlayer(a) {
    selected.value = a;
}
const selectedEntries = computed(() =>
    selected.value ? Object.entries(selected.value.totals).sort((x, y) => y[1] - x[1]) : []
);
const selectedAportes = computed(() => selectedEntries.value.filter(([k]) => (props.statMeta[k] ?? 0) >= 0));
const selectedErrores = computed(() => selectedEntries.value.filter(([k]) => (props.statMeta[k] ?? 0) < 0));

function rankOf(player, stat) {
    const rows = agg.value
        .filter((a) => (a.totals[stat] || 0) > 0)
        .sort((x, y) => (y.totals[stat] || 0) - (x.totals[stat] || 0));
    const idx = rows.findIndex((a) => a.id === player.id);
    return idx < 0 ? null : { pos: idx + 1, total: rows.length };
}
function rankLabel(player, stat) {
    const r = rankOf(player, stat);
    return r ? `${r.pos}º de ${r.total}` : '-';
}
</script>
