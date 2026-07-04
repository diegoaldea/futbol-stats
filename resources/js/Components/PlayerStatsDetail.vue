<template>
    <div>
        <!-- Aportes -->
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aportes</p>
        <ul class="mt-1">
            <li
                v-for="[stat, value] in aportes"
                :key="stat"
                class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0"
            >
                <span class="text-sm text-slate-700">{{ stat }}</span>
                <span class="flex items-center gap-3">
                    <span
                        class="rounded-full px-2 py-0.5 text-xs"
                        :class="rankOf(stat)?.pos === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                    >
                        {{ rankLabel(stat) }}
                    </span>
                    <span class="w-8 text-right font-mono font-semibold text-slate-800">{{ value }}</span>
                </span>
            </li>
        </ul>

        <!-- Errores -->
        <template v-if="errores.length">
            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-400">Errores</p>
            <ul class="mt-1">
                <li
                    v-for="[stat, value] in errores"
                    :key="stat"
                    class="flex items-center justify-between border-b border-slate-100 py-2 last:border-0"
                >
                    <span class="text-sm text-slate-700">{{ stat }}</span>
                    <span class="flex items-center gap-3">
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">
                            {{ rankLabel(stat) }}
                        </span>
                        <span class="w-8 text-right font-mono font-semibold text-rose-600">{{ value }}</span>
                    </span>
                </li>
            </ul>
        </template>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    player: { type: Object, required: true },
    players: { type: Array, required: true }, // todos, para el ranking por stat
    statMeta: { type: Object, required: true }, // { statName: { points, category } }
});

const entries = computed(() =>
    Object.entries(props.player.stats).sort((a, b) => b[1] - a[1])
);
const aportes = computed(() => entries.value.filter(([k]) => (props.statMeta[k]?.points ?? 0) >= 0));
const errores = computed(() => entries.value.filter(([k]) => (props.statMeta[k]?.points ?? 0) < 0));

function rankOf(stat) {
    const rows = props.players
        .filter((p) => (p.stats[stat] ?? 0) > 0)
        .sort((a, b) => (b.stats[stat] ?? 0) - (a.stats[stat] ?? 0));
    const idx = rows.findIndex((p) => p.id === props.player.id);
    return idx < 0 ? null : { pos: idx + 1, total: rows.length };
}

function rankLabel(stat) {
    const r = rankOf(stat);
    return r ? `${r.pos}º de ${r.total}` : '-';
}
</script>
