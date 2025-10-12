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
import Pagination from '@/Components/Pagination.vue'; // Gunakan komponen pagination Anda

// Menerima props dari Admin/ProductController
const props = defineProps({
    products: Object,
    categories: Array,
});

// State untuk menampilkan modal form
const showingFormModal = ref(false);
const isEditing = ref(false);

// Form untuk tambah/edit produk
const form = useForm({
    id: null,
    name: '',
    category_id: '',
    price: '',
    stock: '',
    is_active: true,
});

// Fungsi untuk format harga
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

// Fungsi untuk membuka modal tambah produk
const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.is_active = true; // Default value for new product
    showingFormModal.value = true;
};

// Fungsi untuk membuka modal edit produk
const openEditModal = (product) => {
    isEditing.value = true;
    form.id = product.id;
    form.name = product.name;
    form.category_id = product.category_id;
    form.price = product.price;
    form.stock = product.stock;
    form.is_active = product.is_active;
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
        form.put(route('admin.products.update', form.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        // Kirim request store (tambah baru)
        form.post(route('admin.products.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

// Fungsi untuk menghapus produk
const deleteProduct = (product) => {
    if (confirm(`Anda yakin ingin menghapus produk "${product.name}"?`)) {
        router.delete(route('admin.products.destroy', product.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Manajemen Produk" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Produk</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <PrimaryButton @click="openAddModal">Tambah Produk</PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="product in products.data" :key="product.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ product.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ product.category?.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatCurrency(product.price) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ product.stock }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                {{ product.is_active ? 'Aktif' : 'Non-Aktif' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <SecondaryButton @click="openEditModal(product)">Edit</SecondaryButton>
                                            <DangerButton @click="deleteProduct(product)">Hapus</DangerButton>
                                        </td>
                                    </tr>
                                    <tr v-if="products.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada produk.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <Pagination :links="products.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showingFormModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">{{ isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h2>
                <form @submit.prevent="submitForm" class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="name" value="Nama Produk" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="category_id" value="Kategori" />
                        <select id="category_id" v-model="form.category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="" disabled>Pilih kategori</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <InputError :message="form.errors.category_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="price" value="Harga" />
                        <TextInput id="price" v-model="form.price" type="number" step="0.01" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.price" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="stock" value="Stok" />
                        <TextInput id="stock" v-model="form.stock" type="number" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.stock" class="mt-2" />
                    </div>
                    
                    <div class="flex items-center">
                        <input id="is_active" v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktifkan produk</label>
                    </div>

                    <div class="flex justify-end gap-4">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">{{ isEditing ? 'Simpan Perubahan' : 'Buat Produk' }}</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>