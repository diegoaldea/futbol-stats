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
            <div>
                <span v-for="player in playerList" :key="player.id">
                    {{ player.name }}
                </span>
            </div>
            <div v-if="showAddPlayer">
                <input v-model="newPlayerName" type="text" placeholder="Nombre del jugador" />
                <button @click="addPlayer">Agregar</button>
                <button @click="showAddPlayer = false">Cancelar</button>
            </div>
            <button v-else @click="showAddPlayer = true">+ Agregar jugador</button>
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
const props = defineProps({
    players: Array,
});

const playerList = ref([...props.players]);
const teamA = ref([]);
const teamB = ref([]);

const showAddPlayer = ref(false);
const newPlayerName = ref('');

function confirmCancel(){
    if(confirm('¿Estas seguro que queres salir?')){
        router.visit(route('home'));
    }
}

async function addPlayer() {
    if (newPlayerName.value.trim() === '') return;

    const response = await axios.post(route('players.store'), {
        name: newPlayerName.value.trim(),
    });

    playerList.value.push(response.data);
    newPlayerName.value = '';
    showAddPlayer.value = false;
}

</script>