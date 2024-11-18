<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
const file_location = ref("");


const handleFileChange = (event) => {
    const file = event.target.files[0];
    alert(file.name);
    if (file) {
        file_location.value = file.name;
    }
};

const triggerFileInput = () => {
    document.getElementById("fileInput").click();
};

const purchase = ref(true);
const visit = ref(false);

const setMode = (mode) => {
    if (mode === "purchase") {
        purchase.value = true;
        visit.value = false;
    } else if (mode === "visit") {
        purchase.value = false;
        visit.value = true;
    }
};

// $settings = OrderBotSettings::create([
//     'order_range_min' =>$request->itemsPerOrder,
//     'order_range_max' => $request->itemsPerOrderMin,
//     'order_value_min' => $request->orderValueMin,
//     'order_value_max' => $request->orderValueMax,
//     'items_per_order_min' => 1,
//     'items_per_order_max' => 3,
//     'one_item_order_chance_min' => 20,
//     'one_item_order_chance_max' => 80,
//     'order_speed_min' => $request->orderSpeedMin,
//     'order_speed_max' => $request->orderSpeed,
//     'order_speed_unit' => $request->orderFrequency,
//     'telegramBot' => false,
//     'unlimitedOrders' => $request->unlimitedOrders,
//     'csv_file_path' => null
// ]);


const  form = useForm({
    // orders: '',  // Initialize form.orders
    unlimited_orders: '',   //1
    order_range_min: '',    //2
    order_range_max: '',    //3
    order_value_min: '',    //4
    order_value_max: '',    //5
    items_per_order_min: '',    //6
    items_per_order_max: '',    //7
    one_item_chance_min: '',    //8
    one_item_chance_max: '',    //9
    order_speed_min: '',    //10
    order_speed_max: '',    //11
    order_frequency: 'second',  //12
    file_location: '',  //13
    telegramBot: false, //14
});

const submitForm = () => {
    // console.log(form);  // Ensure form.orders is defined
    form.post(route("shopifybot.store"), {
        onSuccess: () => {
            console.log("Form submitted successfully");
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>
    <AppLayout title="Shopify Bot">
        <div class="flex items-center justify-center min-h-screen bg-gray-900 text-white px-6" v-if="purchase">
            <div class="bg-gray-800 rounded-lg shadow-lg p-8 w-50">
                <h2 class="text-2xl font-semibold mb-8">Shopify Bot</h2>
                <div class="mb-8">
                    <label class="block text-sm font-medium mb-2">Mode</label>
                    <div class="flex gap-4">
                        <button @click="setMode('purchase')" :class="{
                            'bg-blue-800': purchase,
                            'bg-gray-600': !purchase,
                        }"
                                class="w-60 px-4 py-2 focus:bg-blue-800 active:bg-blue-900 rounded-2xl text-white text-center">
                            Purchase
                        </button>
                        <button @click="setMode('visit')" :class="{
                            'bg-blue-800': visit,
                            'bg-gray-600': !visit,
                        }"
                                class="w-60 px-4 py-2 focus:bg-blue-800 active:bg-blue-900 rounded-2xl text-white text-center">
                            Visit & Purchase
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submitForm">
                    <!-- First Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">Orders</label>
                            <div class="flex gap-2">

                                <input type="radio" v-model="form.unlimited_orders" id="unlimited" name="orders" value="unlimited" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                                <label for="unlimited" class="text-sm">Unlimited</label>
                            </div>
                            <input type="text" v-model="form.order_range_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.order_range_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">Order Value ($)</label>
                            <input type="text" v-model="form.order_value_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.order_value_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>

                    <!-- Second Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">Items per order</label>
                            <input type="text" v-model="form.items_per_order_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.items_per_order_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">1 item per order chance (%)</label>
                            <input type="text" v-model="form.one_item_chance_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.one_item_chance_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>

                    <!-- Third Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">Order Speed</label>
                            <input type="text" v-model="form.order_speed_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.order_speed_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7 col-span-2">
                            <select v-model="form.order_frequency" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white">
                                <option disabled selected>Choose Frequency</option>
                                <option value="second">order per second</option>
                                <option value="minute">order per minute</option>
                                <option value="hour">order per hour</option>
                                <option value="day">order per day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fourth Row -->
                    <div class="mb-5">
                        <label class="block text-white text-sm font-bold mb-2">Customers Data (Name, Email, Address)</label>
                        <input type="file" id="fileInput" class="block w-full text-sm bg-gray-700 border border-gray-300 rounded-lg cursor-pointer text-white focus:outline-none focus:shadow-none p-2 mb-3" @change="handleFileChange" style="display: none" />
                        <div class="flex items-center bg-gray-700 border rounded-2xl p-1 text-white">
                            <span class="mr-2 w-36">File Location:</span>
                            <input type="text" v-model="file_location" class="bg-transparent border-none text-white w-full focus:outline-none" readonly @click="triggerFileInput" />
                        </div>
                    </div>

                    <!-- Fifth Row -->
                    <div class="flex gap-2 mb-5">
                        <input type="radio" v-model="form.telegram_bot" id="telegramBot" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                        <label for="telegramBot" class="text-sm">Telegram Bot</label>
                    </div>

                    <!-- Footer -->
                    <div class="mb-3">
                        <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-white">
                            Start
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

