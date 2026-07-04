<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[{ label: 'Inicio' }]" />

            <!-- Encabezado + acciones -->
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-bold text-slate-800">Mis Partidos</h1>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        v-if="isGuest"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                        @click="showLoginModal = true"
                    >
                        Iniciar sesión
                    </button>

                    <Link
                        v-if="!isGuest"
                        :href="route('rendimiento')"
                        class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                    >
                        Rendimiento
                    </Link>

                    <Link
                        v-if="isAdmin"
                        :href="route('admin.dashboard')"
                        class="rounded-md bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700"
                    >
                        Panel de administración
                    </Link>

                    <Link
                        :href="route('games.create')"
                        class="rounded-md bg-emerald-500 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-600"
                    >
                        + Crear partido
                    </Link>
                </div>
            </div>

            <!-- Listado de partidos -->
            <div v-if="!isGuest" class="mt-6">
                <p v-if="games.length === 0" class="text-slate-500">Todavía no tenés partidos.</p>

                <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <GameCard v-for="game in games" :key="game.id" :game="game" />
                </div>
            </div>

            <p v-else class="mt-6 text-slate-500">
                Iniciá sesión para ver y guardar tus partidos.
            </p>
        </div>

        <!-- Modal de login -->
        <div
            v-if="showLoginModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            @click.self="closeLoginModal"
        >
            <div class="w-full max-w-sm rounded-lg bg-white p-5 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-800">Iniciar sesión</h2>
                    <button class="text-slate-400 hover:text-slate-600" @click="closeLoginModal">✕</button>
                </div>

                <form class="flex flex-col gap-3" @submit.prevent="submitLogin">
                    <div>
                        <input
                            v-model="loginForm.email"
                            type="email"
                            placeholder="Email"
                            required
                            autofocus
                            class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                        />
                        <span v-if="loginForm.errors.email" class="text-sm text-rose-600">
                            {{ loginForm.errors.email }}
                        </span>
                    </div>

                    <div>
                        <input
                            v-model="loginForm.password"
                            type="password"
                            placeholder="Contraseña"
                            required
                            class="w-full rounded-md border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
                        />
                        <span v-if="loginForm.errors.password" class="text-sm text-rose-600">
                            {{ loginForm.errors.password }}
                        </span>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input v-model="loginForm.remember" type="checkbox" /> Recordarme
                    </label>

                    <div class="mt-1 flex justify-end gap-2">
                        <AppButton type="button" variant="ghost" @click="closeLoginModal">Cancelar</AppButton>
                        <AppButton type="submit" :disabled="loginForm.processing">Ingresar</AppButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import GameCard from '@/Components/GameCard.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    games: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isGuest = computed(() => !page.props.auth.user);
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const showLoginModal = ref(false);
const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

function submitLogin() {
    loginForm.post(route('login'), {
        onSuccess: () => closeLoginModal(),
        onFinish: () => loginForm.reset('password'),
    });
}

function closeLoginModal() {
    showLoginModal.value = false;
    loginForm.reset();
    loginForm.clearErrors();
}
</script>
