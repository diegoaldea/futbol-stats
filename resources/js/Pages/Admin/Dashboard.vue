<template>
    <div>
        <h1>Administración</h1>

        <ul>
            <li>Usuarios: {{ counts.users }}</li>
            <li>Partidos: {{ counts.games }}</li>
            <li>Estadísticas globales: {{ counts.globalStats }}</li>
        </ul>

        <nav>
            <Link :href="route('admin.stats.index')">Estadísticas y categorías</Link>
            <Link :href="route('admin.users.index')">Usuarios</Link>
            <Link :href="route('admin.games.index')">Partidos</Link>
            <Link :href="route('home')">Volver al inicio</Link>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    counts: Object,
});
</script>
