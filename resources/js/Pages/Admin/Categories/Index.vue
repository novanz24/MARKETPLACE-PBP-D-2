<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

// Menerima props dari Admin/CategoryController
defineProps({
    categories: Array,
});

// State untuk menampilkan modal form
const showingFormModal = ref(false);
const isEditing = ref(false);

// Form untuk tambah/edit kategori
const form = useForm({
    id: null,
    name: '',
});

// Fungsi untuk membuka modal tambah kategori
const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    showingFormModal.value = true;
};

// Fungsi untuk membuka modal edit kategori
const openEditModal = (category) => {
    isEditing.value = true;
    form.id = category.id;
    form.name = category.name;
    showingFormModal.value = true;
};

// Fungsi untuk menutup modal
const closeModal = () => {
    showingFormModal.value = false;
};

// Fungsi untuk submit form (tambah atau edit)
const submitForm = () => {
    if (isEditing.value) {
        // Kirim request update
        form.put(route('admin.categories.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // Kirim request store (tambah baru)
        form.post(route('admin.categories.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Fungsi untuk menghapus kategori
const deleteCategory = (category) => {
    if (confirm(`Anda yakin ingin menghapus kategori "${category.name}"?`)) {
        router.delete(route('admin.categories.destroy', category.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Manajemen Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Kategori</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <PrimaryButton @click="openAddModal">Tambah Kategori</PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="category in categories" :key="category.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ category.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <SecondaryButton @click="openEditModal(category)">Edit</SecondaryButton>
                                            <DangerButton @click="deleteCategory(category)">Hapus</DangerButton>
                                        </td>
                                    </tr>
                                    <tr v-if="categories.length === 0">
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500">Belum ada kategori.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showingFormModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ isEditing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h2>
                <form @submit.prevent="submitForm" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Nama Kategori" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">{{ isEditing ? 'Simpan Perubahan' : 'Buat Kategori' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>