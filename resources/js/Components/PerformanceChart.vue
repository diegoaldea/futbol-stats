<template>
    <svg :viewBox="`0 0 ${W} ${H}`" class="w-full" role="img" aria-label="Evolución del rendimiento en el partido">
        <!-- Grilla + etiquetas del eje Y -->
        <g>
            <line
                v-for="(t, i) in ticks"
                :key="'g' + i"
                :x1="padL"
                :x2="W - padR"
                :y1="t.y"
                :y2="t.y"
                stroke="#e2e8f0"
                stroke-width="1"
            />
            <text
                v-for="(t, i) in ticks"
                :key="'l' + i"
                :x="padL - 5"
                :y="t.y + 3"
                fill="#94a3b8"
                font-size="9"
                text-anchor="end"
            >
                {{ t.label }}
            </text>
        </g>

        <!-- Línea base (rendimiento neutro = 6) -->
        <line :x1="padL" :x2="W - padR" :y1="baseY" :y2="baseY" stroke="#94a3b8" stroke-width="1.5" stroke-dasharray="4 3" />

        <!-- Área bajo la curva -->
        <path :d="areaD" fill="#10b981" fill-opacity="0.08" />

        <!-- Tramos: rojo si la acción fue negativa (bajón), verde si sumó -->
        <line
            v-for="(seg, i) in segments"
            :key="i"
            :x1="seg.x1"
            :y1="seg.y1"
            :x2="seg.x2"
            :y2="seg.y2"
            :stroke="seg.negative ? '#f43f5e' : '#10b981'"
            stroke-width="2.5"
            stroke-linecap="round"
        />

        <!-- Zonas invisibles para tooltip (sin puntos visibles) -->
        <circle v-for="(c, i) in coords" :key="'h' + i" :cx="c.x" :cy="c.y" r="7" fill="transparent">
            <title>{{ c.label }} · {{ c.value }}</title>
        </circle>

        <!-- Valor final -->
        <text
            v-if="coords.length"
            :x="coords[coords.length - 1].x - 2"
            :y="coords[coords.length - 1].y - 7"
            fill="#334155"
            font-size="12"
            font-weight="700"
            text-anchor="end"
        >
            {{ coords[coords.length - 1].value }}
        </text>
    </svg>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    // [{ label, value, negative }] en orden cronológico
    points: { type: Array, default: () => [] },
    baseline: { type: Number, default: 6 },
});

const W = 340;
const H = 190;
const padL = 26;
const padR = 14;
const padT = 16;
const padB = 18;

const domain = computed(() => {
    const vals = props.points.map((p) => p.value).concat([props.baseline]);
    let min = Math.min(...vals);
    let max = Math.max(...vals);
    if (min === max) {
        min -= 1;
        max += 1;
    }
    const pad = (max - min) * 0.15;
    return { min: min - pad, max: max + pad };
});

function x(i) {
    const n = props.points.length;
    if (n <= 1) return padL;
    return padL + (i / (n - 1)) * (W - padL - padR);
}
function y(v) {
    const { min, max } = domain.value;
    return padT + (1 - (v - min) / (max - min)) * (H - padT - padB);
}

const coords = computed(() =>
    props.points.map((p, i) => ({ x: x(i), y: y(p.value), label: p.label, value: p.value, negative: p.negative }))
);

const segments = computed(() =>
    coords.value.slice(1).map((c, i) => ({
        x1: coords.value[i].x,
        y1: coords.value[i].y,
        x2: c.x,
        y2: c.y,
        negative: c.negative,
    }))
);

const baseY = computed(() => y(props.baseline));

const areaD = computed(() => {
    if (!coords.value.length) return '';
    const bottom = H - padB;
    let d = `M ${coords.value[0].x} ${bottom}`;
    coords.value.forEach((c) => {
        d += ` L ${c.x} ${c.y}`;
    });
    d += ` L ${coords.value[coords.value.length - 1].x} ${bottom} Z`;
    return d;
});

// 4 líneas de grilla con su etiqueta de rating
const ticks = computed(() => {
    const { min, max } = domain.value;
    const n = 4;
    return Array.from({ length: n }, (_, i) => {
        const v = min + (i / (n - 1)) * (max - min);
        return { y: y(v), label: v.toFixed(1) };
    });
});
</script>
