<template>
    <span
        class="group inline-flex items-center gap-2 rounded-full border px-2.5 py-0.5 text-sm cursor-pointer select-none transition hover:brightness-95"
        :class="colorClass"
        @click="$emit('add')"
    >
        <span>{{ name }}</span>

        <!-- El número es el contenedor: el botón se centra sobre él -->
        <span class="relative font-semibold">
            {{ value }}
            <button
                class="absolute left-1/2 top-1/2 flex h-5 w-5 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/30 text-xs leading-none text-white opacity-0 transition hover:bg-black/50 group-hover:opacity-100 pointer-events-none group-hover:pointer-events-auto"
                @click.stop="$emit('subtract')"
            >−</button>
        </span>
    </span>
</template>

<script setup>
defineProps({
    name: { type: String, required: true },
    value: { type: Number, required: true },
    colorClass: { type: String, default: '' },
});

defineEmits(['add', 'subtract']);
</script>
