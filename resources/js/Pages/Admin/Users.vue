<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Administración', href: route('admin.dashboard') },
                { label: 'Usuarios' },
            ]" />

            <h1 class="mt-4 text-2xl font-bold text-slate-800">Usuarios</h1>

            <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nombre</th>
                            <th class="px-4 py-3 font-medium">Email</th>
                            <th class="px-4 py-3 font-medium">Rol</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b border-slate-100 last:border-0">
                            <td class="px-4 py-3 text-slate-800">{{ user.name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <select
                                    :value="user.role"
                                    class="rounded-md border border-slate-300 px-2 py-1"
                                    @change="changeRole(user, $event.target.value)"
                                >
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <AppButton variant="danger" size="sm" @click="destroyUser(user)">Eliminar</AppButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    users: Array,
});

function changeRole(user, role) {
    router.put(route('admin.users.update', user.id), { role }, { preserveScroll: true });
}

function destroyUser(user) {
    if (confirm(`¿Eliminar al usuario "${user.name}"? Se borrarán también sus partidos y jugadores.`)) {
        router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
    }
}
</script>
