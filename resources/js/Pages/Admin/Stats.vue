<template>
    <div class="min-h-screen bg-slate-100">
        <div class="mx-auto max-w-5xl p-4">
            <Breadcrumb :items="[
                { label: 'Inicio', href: route('home') },
                { label: 'Administración', href: route('admin.dashboard') },
                { label: 'Estadísticas' },
            ]" />

            <h1 class="mt-4 text-2xl font-bold text-slate-800">Estadísticas globales</h1>

            <!-- Crear estadística -->
            <form class="mt-4 flex flex-wrap items-center gap-2 rounded-lg bg-white p-4 shadow" @submit.prevent="createStat">
                <input v-model="statForm.name" type="text" placeholder="Nombre" :class="inputClass" />
                <input v-model="statForm.description" type="text" placeholder="Descripción (opcional)" :class="inputClass" />
                <select v-model="statForm.stat_category_id" :class="inputClass">
                    <option :value="null" disabled>Categoría</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <input v-model="statForm.points" type="number" step="0.01" placeholder="Puntos" :class="`${inputClass} w-24`" />
                <AppButton type="submit">Crear</AppButton>
            </form>

            <!-- Listado de estadísticas -->
            <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nombre</th>
                            <th class="px-4 py-3 font-medium">Descripción</th>
                            <th class="px-4 py-3 font-medium">Categoría</th>
                            <th class="px-4 py-3 font-medium">Puntos</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="stat in stats" :key="stat.id" class="border-b border-slate-100 last:border-0">
                            <template v-if="editingStatId === stat.id">
                                <td class="px-4 py-2"><input v-model="editStatForm.name" type="text" :class="inputClass" /></td>
                                <td class="px-4 py-2"><input v-model="editStatForm.description" type="text" :class="inputClass" /></td>
                                <td class="px-4 py-2">
                                    <select v-model="editStatForm.stat_category_id" :class="inputClass">
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </td>
                                <td class="px-4 py-2"><input v-model="editStatForm.points" type="number" step="0.01" :class="`${inputClass} w-20`" /></td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-2">
                                        <AppButton size="sm" @click="updateStat(stat)">Guardar</AppButton>
                                        <AppButton size="sm" variant="ghost" @click="editingStatId = null">Cancelar</AppButton>
                                    </div>
                                </td>
                            </template>
                            <template v-else>
                                <td class="px-4 py-3 text-slate-800">{{ stat.name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ stat.description }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ stat.category?.name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ stat.points }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <AppButton size="sm" variant="ghost" @click="startEditStat(stat)">Editar</AppButton>
                                        <AppButton size="sm" variant="danger" @click="destroyStat(stat)">Eliminar</AppButton>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h1 class="mt-8 text-2xl font-bold text-slate-800">Categorías</h1>

            <!-- Crear categoría -->
            <form class="mt-4 flex flex-wrap items-center gap-2 rounded-lg bg-white p-4 shadow" @submit.prevent="createCategory">
                <input v-model="categoryForm.name" type="text" placeholder="Nombre" :class="inputClass" />
                <input v-model="categoryForm.description" type="text" placeholder="Descripción (opcional)" :class="inputClass" />
                <AppButton type="submit">Crear</AppButton>
            </form>

            <!-- Listado de categorías -->
            <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nombre</th>
                            <th class="px-4 py-3 font-medium">Descripción</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="category in categories" :key="category.id" class="border-b border-slate-100 last:border-0">
                            <template v-if="editingCategoryId === category.id">
                                <td class="px-4 py-2"><input v-model="editCategoryForm.name" type="text" :class="inputClass" /></td>
                                <td class="px-4 py-2"><input v-model="editCategoryForm.description" type="text" :class="inputClass" /></td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-2">
                                        <AppButton size="sm" @click="updateCategory(category)">Guardar</AppButton>
                                        <AppButton size="sm" variant="ghost" @click="editingCategoryId = null">Cancelar</AppButton>
                                    </div>
                                </td>
                            </template>
                            <template v-else>
                                <td class="px-4 py-3 text-slate-800">{{ category.name }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ category.description }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <AppButton size="sm" variant="ghost" @click="startEditCategory(category)">Editar</AppButton>
                                        <AppButton size="sm" variant="danger" @click="destroyCategory(category)">Eliminar</AppButton>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppButton from '@/Components/AppButton.vue';

defineProps({
    stats: Array,
    categories: Array,
});

const inputClass = 'rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none';

// --- Estadísticas ---
const statForm = useForm({
    name: '',
    description: '',
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
const editStatForm = reactive({ name: '', description: '', stat_category_id: null, points: 0 });

function startEditStat(stat) {
    editingStatId.value = stat.id;
    editStatForm.name = stat.name;
    editStatForm.description = stat.description ?? '';
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
