<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Administración' },
            ]" />

            <h1 class="mt-4 text-2xl font-bold text-slate-800">Administración</h1>

            <!-- Contadores -->
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-sm text-slate-500">Usuarios</p>
                    <p class="text-3xl font-bold text-slate-800">{{ counts.users }}</p>
                </div>
                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-sm text-slate-500">Partidos</p>
                    <p class="text-3xl font-bold text-slate-800">{{ counts.games }}</p>
                </div>
                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-sm text-slate-500">Estadísticas globales</p>
                    <p class="text-3xl font-bold text-slate-800">{{ counts.globalStats }}</p>
                </div>
            </div>

            <!-- Navegación -->
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Link
                    :href="route('admin.stats.index')"
                    class="rounded-lg bg-white p-4 text-center font-medium text-slate-800 shadow transition hover:shadow-md"
                >
                    Estadísticas y categorías
                </Link>
                <Link
                    :href="route('admin.users.index')"
                    class="rounded-lg bg-white p-4 text-center font-medium text-slate-800 shadow transition hover:shadow-md"
                >
                    Usuarios
                </Link>
                <Link
                    :href="route('admin.games.index')"
                    class="rounded-lg bg-white p-4 text-center font-medium text-slate-800 shadow transition hover:shadow-md"
                >
                    Partidos
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';

defineProps({
    counts: Object,
});
</script>
