<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Administración', href: route('admin.dashboard') },
                { label: 'Partidos' },
            ]" />

            <h1 class="mt-4 text-2xl font-bold text-slate-800">Partidos</h1>

            <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Fecha</th>
                            <th class="px-4 py-3 font-medium">Marcador</th>
                            <th class="px-4 py-3 font-medium">Usuario</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="game in games" :key="game.id" class="border-b border-slate-100 last:border-0">
                            <td class="px-4 py-3 text-slate-600">{{ formatDate(game.date) }}</td>
                            <td class="px-4 py-3 text-slate-800">{{ game.score_team_a }} - {{ game.score_team_b }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ game.user?.name ?? 'Invitado' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <Link
                                        :href="route('games.summary', game.id)"
                                        class="text-sm text-emerald-600 hover:underline"
                                    >
                                        Ver resumen
                                    </Link>
                                    <AppButton variant="danger" size="sm" @click="destroyGame(game)">Eliminar</AppButton>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    games: Array,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString('es-AR');
}

function destroyGame(game) {
    if (confirm('¿Eliminar este partido?')) {
        router.delete(route('admin.games.destroy', game.id), { preserveScroll: true });
    }
}
</script>
