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
                <span v-for="player in playerList" :key="player.id" @click="selectPlayer(player)" :style="selectedPlayer?.id === player.id ? 'background: lightblue' : ''">
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
            <div @click="assignToTeam('a')">
                <h2>Equipo A</h2>
                <span v-for="player in teamA" :key="player.id" @click.stop="removeFromTeam(player, 'a')">
                    {{ player.name }}
                </span>
            </div>
            <div @click="assignToTeam('b')">
                <h2>Equipo B</h2>
                <span v-for="player in teamB" :key="player.id" @click.stop="removeFromTeam(player, 'b')">
                    {{ player.name }}
                </span>
            </div>
        </div>

        <!-- comenzar -->
        <button @click="startGame">Comenzar partido</button>
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

const selectedPlayer = ref(null);

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

function selectPlayer(player){
    if (selectedPlayer.value?.id === player.id){
        selectedPlayer.value = null;
    }
    else {
        selectedPlayer.value = player;
    }
}

function assignToTeam(team){
    if (!selectedPlayer.value) return;

    if(team === 'a'){
        teamA.value.push(selectedPlayer.value);
    }
    else{
        teamB.value.push(selectedPlayer.value);
    }

    playerList.value = playerList.value.filter(p => p.id !== selectedPlayer.value.id);
    selectedPlayer.value = null;
}

function removeFromTeam(player, team) {
    if (team === 'a') {
        teamA.value = teamA.value.filter(p => p.id !== player.id);
    } else {
        teamB.value = teamB.value.filter(p => p.id !== player.id);
    }
    playerList.value.push(player);
}

function startGame(){
    if (!date.value) {
        alert('Seleccioná una fecha')
        return;
    }

    if (teamA.value.length === 0 || teamB.value.length === 0){
        alert('Faltan jugadores en los equipos');
        return;
    }

    router.post(route('games.store'), {
        date: date.value,
        team_a: teamA.value,
        team_b: teamB.value,
    });
}
</script>