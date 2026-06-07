<template>
    <div>
        <Link :href="route('games.create')" class="bg-blue-400 p-2">Crear Partido</Link>

        <div v-if="!isGuest">
            <h1>Mis Partidos</h1>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

const page = usePage();
const isGuest = computed(() => !page.props.auth.user);

</script>