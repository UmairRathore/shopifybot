<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";

const fileLocation = ref("");
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        fileLocation.value = file.name;
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


const refreshButtonText = ref("Refresh Token");
const isRefreshDisabled = ref(false);
const isTokenRefreshed = ref(false);
const remainingTime = ref(0); 
const errorMessage = ref("");
let timerInterval;
const timerMinutes = computed(() => Math.floor(remainingTime.value / 60));
const timerSeconds = computed(() => remainingTime.value % 60);
const refreshToken = async () => {
    refreshButtonText.value = "Processing...";
    isRefreshDisabled.value = true;
    errorMessage.value = "";
    try {
        const response = await fetch("/api/refresh-token", { method: "POST" });
        if (!response.ok) throw new Error("Failed to refresh token");
        isTokenRefreshed.value = true;
        refreshButtonText.value = "Token Obtained";

        // Start the 1-hour timer
        remainingTime.value = 3600; 
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            if (remainingTime.value > 0) {
                remainingTime.value -= 1;
            } else {
                clearInterval(timerInterval);
                isTokenRefreshed.value = false; 
                refreshButtonText.value = "Refresh Token";
                isRefreshDisabled.value = false;
            }
        }, 1000);
    } catch (error) {
        console.error(error);
        refreshButtonText.value = "Try Again";
        isRefreshDisabled.value = false;
    }
};
const startProcess = () => {
    if (!isTokenRefreshed.value) {
        errorMessage.value = "Please refresh the token first.";
        return;
    }
    console.log("Process started successfully!");
    errorMessage.value = ""; 
};
</script>

<template>
    <AppLayout title="Shopify Bot">
        <!-- Purchase -->
        <div class="flex items-center justify-center min-h-screen bg-gray-900 text-white px-6" v-if="purchase">
            <div class="bg-gray-800 rounded-lg shadow-lg p-8 w-50">
                <div class="mb-3">
                    <button :disabled="isRefreshDisabled" @click="refreshToken"
                        class="w-60 py-2 bg-blue-600 hover:bg-blue-700 rounded-2xl text-white">
                        {{ refreshButtonText }}
                    </button>
                </div>
                <div v-if="remainingTime > 0" class="text-center text-gray-800 font-bold">
                    Next token refresh in: {{ timerMinutes }}m {{ timerSeconds }}s
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
                <!-- First row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="flex gap-2">
                            <label class="block text-sm font-medium mb-2">Orders</label>
                            <input type="radio" id="unlimited" name="orders"
                                class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                            <label for="unlimited" class="text-sm">Unlimited</label>
                        </div>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 ps-5">Order Value ($)</label>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                </div>
                <!-- second row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="">
                            <label class="block text-sm font-medium mb-2">Items per order</label>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 ps-5">1 item per order chance (%)</label>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>

                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                </div>
                <!-- Third Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="">
                            <label class="block text-sm font-medium mb-2">Order Speed</label>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7 col-span-2">
                        <div class="flex items-center">
                            <select name="" id="" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white">
                                <option value="1">order per second</option>
                                <option value="1">order per minute</option>
                                <option value="1">order per hour</option>
                                <option value="1">order per day</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- fourth row -->
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
                <!-- fifth row -->
                <div class="flex gap-2 mb-3">
                    <input type="radio" id="unlimited" name="orders"
                        class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                    <label for="unlimited" class="text-sm">Telegram Bot</label>
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
            </div>
        </div>
        <!-- End -->

        <!-- Visit -->
        <div class="flex items-center justify-center min-h-screen bg-gray-900 text-white px-6" v-if="visit">
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
                <!-- First row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="flex gap-2">
                            <label class="block text-sm font-medium mb-2">Orders</label>
                            <input type="radio" id="unlimited" name="orders"
                                class="w-5 h-5 text-blue-600 bg-gray-700 border-gray-600" />
                            <label for="unlimited" class="text-sm">Unlimited</label>
                        </div>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 ps-5">Order Value ($)</label>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                </div>
                <!-- second row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="">
                            <label class="block text-sm font-medium mb-2">Items per order</label>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 ps-5">1 item per order chance (%)</label>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>

                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                </div>
                <!-- Third Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                    <div>
                        <div class="">
                            <label class="block text-sm font-medium mb-2">Order Speed</label>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7 col-span-2">
                        <div class="flex items-center">
                            <select name="" id="" class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white">
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
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2 ps-5">Purchase conversion rate (%)</label>
                        <div class="flex items-center">
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>

                    <div class="mt-7">
                        <div class="flex items-center">
                            <span class="text-white mr-3">-</span>
                            <input type="text" placeholder="10"
                                class="w-full px-3 py-2 bg-gray-700 rounded-2xl text-white" />
                        </div>
                    </div>
                </div>
                <!-- fifth row -->
                <div class="mb-5">
                    <label class="block text-white text-sm font-bold mb-2">
                        Proxies
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
            </div>
        </div>
        <!-- End -->
    </AppLayout>
</template>
