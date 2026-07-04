<template>
    <div class="flex items-center justify-center gap-3 font-bold text-slate-800">
        <span class="text-right">{{ teamA }}</span>
        <span class="tabular-nums">{{ scoreA }} - {{ scoreB }}</span>
        <span class="text-left">{{ teamB }}</span>
    </div>
</template>

<script setup>
defineProps({
    teamA: { type: String, default: 'Equipo A' },
    teamB: { type: String, default: 'Equipo B' },
    scoreA: { type: [Number, String], default: 0 },
    scoreB: { type: [Number, String], default: 0 },
});
</script>
