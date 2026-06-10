<template>
    <div>
        <Link :href="route('admin.dashboard')">← Administración</Link>

        <h1>Usuarios</h1>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users" :key="user.id">
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                        <select :value="user.role" @change="changeRole(user, $event.target.value)">
                            <option value="user">user</option>
                            <option value="admin">admin</option>
                        </select>
                    </td>
                    <td>
                        <button @click="destroyUser(user)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

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
