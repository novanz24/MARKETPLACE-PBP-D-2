<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

// Menerima props 'total' dari CheckoutController
const props = defineProps({
    total: Number,
});

// Menggunakan useForm untuk mengelola data form
const form = useForm({
    address_text: '',
});

// Fungsi untuk mengirim form checkout
const submitCheckout = () => {
    form.post(route('checkout.store'));
};

// Fungsi untuk format harga
const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Checkout" />

    <AppLayout>
        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900">
                        <h2 class="text-2xl font-bold mb-6">Formulir Checkout</h2>

                        <form @submit.prevent="submitCheckout">
                            <div>
                                <label for="address_text" class="block text-sm font-medium text-gray-700">
                                    Alamat Lengkap Pengiriman
                                </label>
                                <textarea
                                    id="address_text"
                                    v-model="form.address_text"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Contoh: Jl. Pahlawan No. 123, Kelurahan Mugassari, Kecamatan Semarang Selatan, Kota Semarang, Jawa Tengah 50243"
                                    required
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.address_text" />
                            </div>

                            <div class="mt-8 pt-4 border-t">
                                <h3 class="text-lg font-medium">Ringkasan Pesanan</h3>
                                <div class="mt-2 flex justify-between items-center">
                                    <p class="text-gray-600">Total Belanja:</p>
                                    <p class="font-bold text-xl">{{ formatCurrency(total) }}</p>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Buat Pesanan
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>