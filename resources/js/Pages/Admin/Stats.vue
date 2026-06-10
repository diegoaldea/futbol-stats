<template>
    <div>
        <Link :href="route('admin.dashboard')">← Administración</Link>

        <h1>Estadísticas globales</h1>

        <!-- Crear estadística -->
        <form @submit.prevent="createStat">
            <input v-model="statForm.name" type="text" placeholder="Nombre" />
            <select v-model="statForm.stat_category_id">
                <option :value="null" disabled>Categoría</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
            <input v-model="statForm.points" type="number" step="0.01" placeholder="Puntos" />
            <button type="submit">Crear</button>
        </form>

        <!-- Listado de estadísticas -->
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Puntos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="stat in stats" :key="stat.id">
                    <template v-if="editingStatId === stat.id">
                        <td><input v-model="editStatForm.name" type="text" /></td>
                        <td>
                            <select v-model="editStatForm.stat_category_id">
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </td>
                        <td><input v-model="editStatForm.points" type="number" step="0.01" /></td>
                        <td>
                            <button @click="updateStat(stat)">Guardar</button>
                            <button @click="editingStatId = null">Cancelar</button>
                        </td>
                    </template>
                    <template v-else>
                        <td>{{ stat.name }}</td>
                        <td>{{ stat.category?.name }}</td>
                        <td>{{ stat.points }}</td>
                        <td>
                            <button @click="startEditStat(stat)">Editar</button>
                            <button @click="destroyStat(stat)">Eliminar</button>
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>

        <h1>Categorías</h1>

        <!-- Crear categoría -->
        <form @submit.prevent="createCategory">
            <input v-model="categoryForm.name" type="text" placeholder="Nombre" />
            <input v-model="categoryForm.description" type="text" placeholder="Descripción (opcional)" />
            <button type="submit">Crear</button>
        </form>

        <!-- Listado de categorías -->
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="category in categories" :key="category.id">
                    <template v-if="editingCategoryId === category.id">
                        <td><input v-model="editCategoryForm.name" type="text" /></td>
                        <td><input v-model="editCategoryForm.description" type="text" /></td>
                        <td>
                            <button @click="updateCategory(category)">Guardar</button>
                            <button @click="editingCategoryId = null">Cancelar</button>
                        </td>
                    </template>
                    <template v-else>
                        <td>{{ category.name }}</td>
                        <td>{{ category.description }}</td>
                        <td>
                            <button @click="startEditCategory(category)">Editar</button>
                            <button @click="destroyCategory(category)">Eliminar</button>
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    stats: Array,
    categories: Array,
});

// --- Estadísticas ---
const statForm = useForm({
    name: '',
    stat_category_id: null,
    points: 0,
});

function createStat() {
    statForm.post(route('admin.stats.store'), {
        preserveScroll: true,
        onSuccess: () => statForm.reset(),
    });
}

const editingStatId = ref(null);
const editStatForm = reactive({ name: '', stat_category_id: null, points: 0 });

function startEditStat(stat) {
    editingStatId.value = stat.id;
    editStatForm.name = stat.name;
    editStatForm.stat_category_id = stat.stat_category_id;
    editStatForm.points = stat.points;
}

function updateStat(stat) {
    router.put(route('admin.stats.update', stat.id), { ...editStatForm }, {
        preserveScroll: true,
        onSuccess: () => (editingStatId.value = null),
    });
}

function destroyStat(stat) {
    if (confirm(`¿Eliminar la estadística "${stat.name}"?`)) {
        router.delete(route('admin.stats.destroy', stat.id), { preserveScroll: true });
    }
}

// --- Categorías ---
const categoryForm = useForm({
    name: '',
    description: '',
});

function createCategory() {
    categoryForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => categoryForm.reset(),
    });
}

const editingCategoryId = ref(null);
const editCategoryForm = reactive({ name: '', description: '' });

function startEditCategory(category) {
    editingCategoryId.value = category.id;
    editCategoryForm.name = category.name;
    editCategoryForm.description = category.description ?? '';
}

function updateCategory(category) {
    router.put(route('admin.categories.update', category.id), { ...editCategoryForm }, {
        preserveScroll: true,
        onSuccess: () => (editingCategoryId.value = null),
    });
}

function destroyCategory(category) {
    if (confirm(`¿Eliminar la categoría "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.id), { preserveScroll: true });
    }
}
</script>
