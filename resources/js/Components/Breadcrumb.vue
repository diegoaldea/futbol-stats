<template>
    <nav class="flex flex-wrap items-center gap-1 text-sm text-slate-500">
        <template v-for="(item, index) in items" :key="index">
            <!-- Items con link (todos menos el último) -->
            <Link
                v-if="item.href && index < items.length - 1"
                :href="item.href"
                class="hover:text-slate-700 hover:underline"
            >
                {{ item.label }}
            </Link>
            <!-- Pantalla actual (último item, sin link) -->
            <span v-else class="font-medium text-slate-700">{{ item.label }}</span>

            <!-- Separador entre items -->
            <span v-if="index < items.length - 1" class="text-slate-300">/</span>
        </template>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    // Array de { label: string, href?: string }
    // Ej: [{ label: 'Inicio', href: route('home') }, { label: 'Crear partido' }]
    items: {
        type: Array,
        required: true,
    },
});
</script>
