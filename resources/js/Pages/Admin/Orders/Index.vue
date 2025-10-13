<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';

// Menerima props dari Admin/OrderController
const props = defineProps({
    orders: Object,
});

// Daftar status yang bisa dipilih
const statuses = ['diproses', 'dikirim', 'selesai', 'batal'];

// Fungsi untuk mengubah status pesanan
const updateStatus = (order, newStatus) => {
    router.patch(route('admin.orders.updateStatus', order.id), {
        status: newStatus
    }, {
        preserveScroll: true,
    });
};

// Fungsi untuk format harga
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

// Fungsi untuk format tanggal
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};
</script>

<template>
    <Head title="Manajemen Pesanan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pesanan</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Daftar Pesanan Masuk</h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembeli</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="order in orders.data" :key="order.id">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">#{{ order.id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ order.user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDate(order.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ formatCurrency(order.total) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select
                                                :value="order.status"
                                                @change="updateStatus(order, $event.target.value)"
                                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                            >
                                                <option v-for="status in statuses" :key="status" :value="status" class="capitalize">
                                                    {{ status }}
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr v-if="orders.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada pesanan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <Pagination :links="orders.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>