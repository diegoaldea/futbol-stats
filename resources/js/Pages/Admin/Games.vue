<template>
    <div>
        <Link :href="route('admin.dashboard')">← Administración</Link>

        <h1>Partidos</h1>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Marcador</th>
                    <th>Usuario</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="game in games" :key="game.id">
                    <td>{{ formatDate(game.date) }}</td>
                    <td>{{ game.score_team_a }} - {{ game.score_team_b }}</td>
                    <td>{{ game.user?.name ?? 'Invitado' }}</td>
                    <td>
                        <Link :href="route('games.summary', game.id)">Ver resumen</Link>
                        <button @click="destroyGame(game)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

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
