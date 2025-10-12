<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import debounce from 'lodash.debounce';

// Menerima props dari CartController
const props = defineProps({
    items: Array,
    total: Number,
});

// Fungsi untuk format harga
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Fungsi untuk update kuantitas item di keranjang
// Menggunakan debounce agar tidak mengirim request terus-menerus
const updateQuantity = debounce((item) => {
    router.patch(route('cart.update', item.id), {
        qty: item.qty,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
}, 300);

// Fungsi untuk menghapus item dari keranjang
const removeItem = (item) => {
    if (confirm('Anda yakin ingin menghapus item ini?')) {
        router.delete(route('cart.destroy', item.id), {
            preserveState: true,
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Keranjang Belanja" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">

                        <div v-if="items.length === 0" class="text-center py-10">
                            <h3 class="text-lg font-medium text-gray-700">Keranjang Anda masih kosong.</h3>
                            <p class="text-gray-500 mt-2">Ayo mulai belanja!</p>
                            <Link
                                :href="route('home')"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Kembali ke Katalog
                            </Link>
                        </div>

                        <div v-else>
                            <h2 class="text-2xl font-bold mb-6">Isi Keranjang Anda</h2>
                            <div class="divide-y divide-gray-200">
                                <div v-for="item in items" :key="item.id" class="py-4 flex flex-col md:flex-row items-center gap-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ item.name }}</h4>
                                        <p class="text-sm text-gray-600">{{ formatCurrency(item.price) }}</p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <label :for="'qty-' + item.id" class="text-sm">Qty:</label>
                                        <input
                                            :id="'qty-' + item.id"
                                            type="number"
                                            min="1"
                                            v-model="item.qty"
                                            @input="updateQuantity(item)"
                                            class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        />
                                    </div>

                                    <div class="w-40 text-right font-medium">
                                        {{ formatCurrency(item.subtotal) }}
                                    </div>

                                    <div>
                                        <DangerButton @click="removeItem(item)">
                                            Hapus
                                        </DangerButton>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t flex flex-col items-end">
                                <div class="text-xl font-bold">
                                    Total: {{ formatCurrency(total) }}
                                </div>
                                <Link
                                    :href="route('checkout.form')"
                                    class="mt-4 inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    Lanjut ke Checkout
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>