<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";

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


const visitForm = useForm({
        visit_order_unlimited: false,
        visit_order_range_min: '',    //2
        visit_order_range_max: '',    //3
        visit_order_value_min: '',    //4
        visit_order_value_max: '',    //5
        visit_items_per_order_min: '',    //6
        visit_items_per_order_max: '',    //7
        visit_one_item_chance_min: '',    //8
        visit_one_item_chance_max: '',    //9
        visit_order_speed_min: '',    //10
        visit_order_speed_max: '',    //11
        visit_order_frequency: 'second',  //12
        file_location: '',  //13
    })

const  form = useForm({
    // orders: '',  // Initialize form.orders
    unlimited_orders: false,   //1
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
    telegram_bot: false, //14
});
const botStatus = ref(false); // Reactive property for bot status

const submitForm = () => {
    form.post(route("shopifybot.store"), {
        onSuccess: () => {
            console.log("Form submitted successfully");
        },
        onError: (errors) => {
            console.error("Form submission errors:", errors);
        },
    });
};

const submitVisitForm = () => {
    // console.log(form);  // Ensure form.orders is defined
    form.post(route("shopifybot.visit_store"), {
        onSuccess: () => {
            console.log("Form submitted successfully");
        },
        onError: (errors) => {
            console.log(errors);
        },
    });
};

//
// const updateBotStatus = () => {
//     form.get(route("shopifybot.visit_store"), {
//         onSuccess: (response) => {
//             if (response.props.shopify_bot !== undefined) {
//                 this.botStatus = response.props.shopify_bot;
//             }
//             if (response.props.message) {
//                 this.message = response.props.message;
//             }
//         },
//         onError: (errors) => {
//             console.error('Error updating bot status:', errors);
//         },
//     });
// }





</script>
<template>
    <AppLayout title="Shopify Bot">
        <div class="flex items-center justify-center min-h-screen bg-gray-900 text-white px-6">
            <div class="bg-gray-800 rounded-lg shadow-lg p-8 w-50">

                <div class="mb-3 flex justify-between">
                    <h4> Shopify Bot</h4>
                   <div class="flex gap-5">
                       <button @click="updateBotStatus()" class="bg-blue-800 w-40 px-4 py-2 focus:bg-blue-800 active:bg-blue-900 rounded-md text-white text-center">
                           {{ botStatus ? 'Stop Bot' : 'Run Bot' }}
                       </button>
<!--                       <button @click="refreshToken()" class="bg-blue-800 w-40 px-4 py-2 focus:bg-blue-800 active:bg-blue-900 rounded-md text-white text-center">-->
<!--                           Refresh Token</button>-->
                   </div>
                </div>

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

                <div v-if="purchase">
                <form @submit.prevent="submitForm">
                    <!-- First Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
<!--                            <label class="block text-sm font-medium mb-2">Orders</label>-->
                            <div class="flex gap-2">
                                <label class="block text-sm font-medium mb-2">Orders</label>
                                <input type="radio" v-model="form.unlimited_orders" id="unlimited" name="orders" value="unlimited" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                                <label for="unlimited" class="text-sm">Unlimited</label>
                            </div>
                            <input type="text" v-model="form.order_range_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7 flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.order_range_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">Order Value ($)</label>
                            <input type="text" v-model="form.order_value_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7 flex items-center">
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
                        <div class="mt-7 flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" v-model="form.items_per_order_max" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">1 item per order chance (%)</label>
                            <input type="text" v-model="form.one_item_chance_min" placeholder="10" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                        <div class="mt-7 flex items-center">
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
                        <div class="mt-7 flex items-center">
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
                        <input type="radio" v-model="form.telegram_bot" id="telegram_bot" class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                        <label for="telegramBot" class="text-sm">Telegram Bot</label>
                    </div>

                    <!-- Footer -->
                <div>
                    <button @click="startProcess"
                        class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-white">
                        Start
                    </button>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="text-red-500 text-sm font-semibold">
                    {{ errorMessage }}
                </div>
                </form>
                </div>

                    <div  v-if="visit">
                <form @submit.prevent="submitVisitForm">
                    <!-- First row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <div class="flex gap-2">
                                <label class="block text-sm font-medium mb-2">Orders</label>
                                <input type="radio" v-model="visitForm.visit_order_unlimited" id="unlimited" name="orders"
                                       class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                                <label for="unlimited" class="text-sm">Unlimited</label>
                            </div>
                            <div class="flex items-center">
                                <input type="text" v-model="visitForm.visit_order_range_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_order_range_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">Order Value ($)</label>
                            <div class="flex items-center">
                                <input type="text" v-model="visitForm.visit_order_value_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_order_value_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                    </div>
                    <!-- second row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <div class="">
                                <label class="block text-sm font-medium mb-2">Items per order</label>
                                <input type="text" v-model="visitForm.visit_items_per_order_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_items_per_order_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">1 item per order chance (%)</label>
                            <div class="flex items-center">
                                <input type="text" v-model="visitForm.visit_one_item_chance_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>

                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_one_item_chance_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                    </div>
                    <!-- Third Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <div class="">
                                <label class="block text-sm font-medium mb-2">Order Speed</label>
                                <input type="text" v-model="visitForm.visit_order_speed_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_order_speed_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7 col-span-2">
                            <div class="flex items-center">
                                <select name="" id="" v-model="visitForm.visit_order_frequency" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white">
                                    <option value="1">order per second</option>
                                    <option value="1">order per minute</option>
                                    <option value="1">order per hour</option>
                                    <option value="1">order per day</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- fourth row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <div class="">
                                <label class="block text-sm font-medium mb-2">Add to cart rate (%)</label>
                                <input type="text" v-model="visitForm.visit_cart_rate_min" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_cart_rate_max" placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2 ps-5">Purchase conversion rate (%)</label>
                            <div class="flex items-center">
                                <input type="text" v-model="visitForm.visit_purchase_conversion_rate_max"  placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>

                        <div class="mt-7">
                            <div class="flex items-center">
                                <span class="text-white mr-3">-</span>
                                <input type="text" v-model="visitForm.visit_purchase_conversion_rate_max"  placeholder="10"
                                       class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                            </div>
                        </div>
                    </div>
                    <!-- fifth row -->
                    <div class="mb-5">
                        <label class="block text-white text-sm font-bold mb-2">
                            Proxies
                        </label>
                        <!--                    <div class="mb-5">-->
                        <label class="block text-white text-sm font-bold mb-2">Customers Data (Name, Email, Address)</label>
                        <input type="file" id="fileInput" class="block w-full text-sm bg-gray-700 border border-gray-300 rounded-lg cursor-pointer text-white focus:outline-none focus:shadow-none p-2 mb-3" @change="handleFileChange" style="display: none" />
                        <div class="flex items-center bg-gray-700 border rounded-2xl p-1 text-white">
                            <span class="mr-2 w-36">File Location:</span>
                            <input type="text" v-model="file_location" class="bg-transparent border-none text-white w-full focus:outline-none" readonly @click="triggerFileInput" />
                        </div>
                        <!--                    </div>-->
                    </div>
                    <!-- sixth row -->
                    <div class="mb-5">
                        <label class="block text-white text-sm font-bold mb-2">
                            Customers Data (Name, Email, Address)
                        </label>
                        <input type="file" id="fileInput"
                               class="block w-full text-sm bg-gray-700 border border-gray-300 rounded-lg cursor-pointer text-white focus:outline-none focus:shadow-none p-2 mb-3"
                               @change="handleFileChange" style="display: none" />
                        <div class="flex items-center bg-gray-700 border rounded-2xl p-1 text-white">
                            <span class="mr-2 w-36">File Location:</span>
                            <input type="text" id="fileLocation" :value="fileLocation"
                                   class="bg-transparent border-none text-white w-full focus:outline-none focus:border-none focus:shadow-none"
                                   readonly @click="triggerFileInput" />
                        </div>
                    </div>
                    <!-- seven row -->
                    <div class="flex gap-2 mb-5">
                        <input type="radio" id="unlimited" name="orders"
                               class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                        <label for="unlimited" class="text-sm">Telegram Bot</label>
                    </div>
                    <!-- Footer -->
                    <div class="mb-3">
                        <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-white">
                            Start
                        </button>
                    </div>
                    <p class="text-center text-sm text-gray-400">
                        Add to cart delay?
                    </p>
                </form>
                    </div>
            </div>
        </div>

        <!-- Visit -->


    </AppLayout>
</template>

