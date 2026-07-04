<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Crear partido' },
            ]" />

            <!-- Encabezado -->
            <h1 class="mt-4 text-2xl font-bold text-slate-800">Configuración de partido</h1>

            <!-- Barra de configuración -->
            <div class="mt-4 flex flex-wrap gap-4 rounded-lg bg-white p-4 shadow">
                <div class="flex flex-col">
                    <label class="mb-1 text-sm font-medium text-slate-600">Fecha del partido</label>
                    <input
                        v-model="date"
                        type="date"
                        class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                    />
                </div>
                <div class="flex flex-col">
                    <label class="mb-1 text-sm font-medium text-slate-600">Equipo A</label>
                    <input
                        v-model="teamAName"
                        type="text"
                        placeholder="Equipo A"
                        class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                    />
                    <select
                        v-if="savedTeams.length"
                        class="mt-1 rounded-md border border-slate-300 px-2 py-1 text-sm"
                        @change="onSelectTeam($event, 'a')"
                    >
                        <option value="">Cargar equipo guardado</option>
                        <option v-for="team in savedTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="mb-1 text-sm font-medium text-slate-600">Equipo B</label>
                    <input
                        v-model="teamBName"
                        type="text"
                        placeholder="Equipo B"
                        class="rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                    />
                    <select
                        v-if="savedTeams.length"
                        class="mt-1 rounded-md border border-slate-300 px-2 py-1 text-sm"
                        @change="onSelectTeam($event, 'b')"
                    >
                        <option value="">Cargar equipo guardado</option>
                        <option v-for="team in savedTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Jugadores + equipos -->
            <div class="mt-4 flex flex-col gap-4 md:flex-row">
                <!-- Lista de jugadores -->
                <aside class="w-full rounded-lg bg-white p-4 shadow md:w-64 md:shrink-0">
                    <h2 class="mb-3 text-lg font-semibold text-slate-800">Jugadores</h2>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="player in playerList"
                            :key="player.id"
                            class="rounded-md border px-3 py-1 text-sm transition"
                            :class="selectedPlayer?.id === player.id
                                ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                : 'border-slate-300 text-slate-700 hover:bg-slate-50'"
                            @click="selectPlayer(player)"
                        >
                            {{ player.name }}
                        </button>
                    </div>

                    <div v-if="showAddPlayer" class="mt-3 flex flex-wrap gap-2">
                        <input
                            v-model="newPlayerName"
                            type="text"
                            placeholder="Nombre del jugador"
                            class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        />
                        <AppButton size="sm" @click="addPlayer">Agregar</AppButton>
                        <AppButton size="sm" variant="ghost" @click="showAddPlayer = false">Cancelar</AppButton>
                    </div>
                    <AppButton v-else size="sm" variant="ghost" class="mt-3" @click="showAddPlayer = true">
                        + Agregar jugador
                    </AppButton>
                </aside>

                <!-- Equipos -->
                <div class="flex flex-1 flex-col gap-4 sm:flex-row">
                    <div
                        class="flex-1 cursor-pointer rounded-lg bg-white p-4 shadow"
                        @click="assignToTeam('a')"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">{{ teamAName || 'Equipo A' }}</h2>
                            <AppButton size="sm" variant="ghost" @click.stop="saveTeam('a')">Guardar equipo</AppButton>
                        </div>
                        <p class="mb-2 text-xs text-slate-400">Clic acá para asignar el jugador seleccionado</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="player in teamA"
                                :key="player.id"
                                class="rounded-md bg-slate-800 px-3 py-1 text-sm text-white hover:bg-rose-500"
                                @click.stop="removeFromTeam(player, 'a')"
                            >
                                {{ player.name }}
                            </button>
                        </div>
                    </div>

                    <div
                        class="flex-1 cursor-pointer rounded-lg bg-white p-4 shadow"
                        @click="assignToTeam('b')"
                    >
                        <div class="mb-3 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-800">{{ teamBName || 'Equipo B' }}</h2>
                            <AppButton size="sm" variant="ghost" @click.stop="saveTeam('b')">Guardar equipo</AppButton>
                        </div>
                        <p class="mb-2 text-xs text-slate-400">Clic acá para asignar el jugador seleccionado</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="player in teamB"
                                :key="player.id"
                                class="rounded-md bg-slate-800 px-3 py-1 text-sm text-white hover:bg-rose-500"
                                @click.stop="removeFromTeam(player, 'b')"
                            >
                                {{ player.name }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comenzar -->
            <div class="mt-4">
                <AppButton @click="startGame">Comenzar partido</AppButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppButton from '@/Components/AppButton.vue';

const date = ref('')
const props = defineProps({
    players: Array,
    teams: {
        type: Array,
        default: () => [],
    },
});

const playerList = ref([...props.players]);
const teamA = ref([]);
const teamB = ref([]);
const teamAName = ref('');
const teamBName = ref('');
const savedTeams = ref([...props.teams]);

const showAddPlayer = ref(false);
const newPlayerName = ref('');

const selectedPlayer = ref(null);

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

function onSelectTeam(event, side) {
    const id = Number(event.target.value);
    event.target.value = ''; // resetear el select
    if (!id) return;

    const team = savedTeams.value.find(t => t.id === id);
    if (team) selectTeam(team, side);
}

function selectTeam(team, side) {
    const sideRef = side === 'a' ? teamA : teamB;
    const otherRef = side === 'a' ? teamB : teamA;

    // devolver a la lista los jugadores que ya estaban en este lado
    playerList.value.push(...sideRef.value);

    // setear el nombre del equipo
    if (side === 'a') teamAName.value = team.name;
    else teamBName.value = team.name;

    // sacar a los jugadores del equipo de donde estén (lista u otro lado)
    const ids = team.players.map(p => p.id);
    playerList.value = playerList.value.filter(p => !ids.includes(p.id));
    otherRef.value = otherRef.value.filter(p => !ids.includes(p.id));

    // cargar este lado con los jugadores del equipo guardado
    sideRef.value = team.players.map(p => ({ id: p.id, name: p.name }));
}

async function saveTeam(side) {
    const name = (side === 'a' ? teamAName : teamBName).value.trim();
    const players = side === 'a' ? teamA.value : teamB.value;

    if (!name) {
        alert('Ponele un nombre al equipo antes de guardarlo');
        return;
    }
    if (players.length === 0) {
        alert('Agregá jugadores al equipo antes de guardarlo');
        return;
    }

    try {
        const response = await axios.post(route('teams.store'), {
            name,
            player_ids: players.map(p => p.id),
        });
        savedTeams.value.push(response.data);
        alert('Equipo guardado');
    } catch (error) {
        alert(error.response?.data?.message ?? 'No se pudo guardar el equipo');
    }
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
        team_a_name: teamAName.value,
        team_b_name: teamBName.value,
    });
}
</script>
