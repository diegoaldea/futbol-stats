<template>
    <button :class="classes">
        <slot />
    </button>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary | secondary | danger | ghost
    size: { type: String, default: 'md' }, // sm | md
});

const variants = {
    primary: 'bg-emerald-500 text-white hover:bg-emerald-600',
    secondary: 'bg-slate-800 text-white hover:bg-slate-700',
    danger: 'bg-rose-500 text-white hover:bg-rose-600',
    ghost: 'border border-slate-300 text-slate-700 hover:bg-slate-100',
};

const sizes = {
    sm: 'px-2.5 py-1 text-xs',
    md: 'px-4 py-2 text-sm',
};

const classes = computed(
    () =>
        `rounded-md font-medium transition disabled:opacity-50 disabled:cursor-not-allowed ${variants[props.variant]} ${sizes[props.size]}`
);
</script>
