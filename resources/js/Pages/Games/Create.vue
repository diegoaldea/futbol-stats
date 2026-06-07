<template>
    <div>
        <button @click="confirmCancel">Volver</button>
        <h1>Configuracion de partido</h1>

        <!-- barra de configuraciones -->
        <div>
            <label>Fecha del partido</label>
            <input type="date" v-model="date" />
        </div>

        <!-- lista de jugadores -->
        <div>
            <h2>Jugadores</h2>
            <button>Agregar jugador</button>
        </div>

        <!-- equipos -->
        <div>
            <div>
                <h2>Equipo A</h2>
            </div>
            <div>
                <h2>Equipo B</h2>
            </div>
        </div>

        <!-- comenzar -->
        <button>Comenzar partido</button>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const date = ref('')

function confirmCancel(){
    if(confirm('¿Estas seguro que queres salir?')){
        router.visit(route('home'));
    }
}

</script>