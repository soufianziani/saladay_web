<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold text-gray-900">Order Management</h1>
                <Link
                    href="/orders"
                    class="flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none"
                >
                    <FileText class="mr-2 h-5 w-5" />
                    Orders History
                </Link>
            </div>
        </header>

        <main class="mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Products and Categories Section -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Categories -->
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h2 class="mb-3 text-lg font-medium">Categories</h2>
                        <div class="flex space-x-2 overflow-x-auto pb-2">
                            <button
                                @click="selectedCategory = null"
                                :class="[
                                    'rounded-full px-4 py-2 text-sm font-medium whitespace-nowrap',
                                    selectedCategory === null ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                                ]"
                            >
                                All
                            </button>
                            <button
                                v-for="category in categories"
                                :key="category.id"
                                @click="selectedCategory = category.id"
                                :class="[
                                    'rounded-full px-4 py-2 text-sm font-medium whitespace-nowrap',
                                    selectedCategory === category.id ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800 hover:bg-gray-200',
                                ]"
                            >
                                {{ category.name }}
                            </button>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="overflow-y-auto rounded-lg bg-white p-4 shadow-sm" style="max-height: 50vh">
                        <h2 class="mb-4 text-lg font-medium">Products</h2>
                        <div class="grid grid-cols-4 gap-3 sm:grid-cols-5 md:grid-cols-6">
                            <div
                                v-for="product in filteredProducts"
                                :key="product.id"
                                class="relative cursor-pointer overflow-hidden rounded-lg border bg-white transition-shadow hover:shadow-md"
                            >
                                <!-- Remove from cart button (top-left) -->
                                <button
                                    v-if="getProductQuantity(product.id) > 0"
                                    @click.stop="removeFromCart(product)"
                                    class="absolute top-1 left-1 z-10 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white hover:bg-red-600"
                                >
                                    <X class="h-3 w-3" />
                                </button>

                                <!-- Product quantity badge (top-right) -->
                                <div
                                    v-if="getProductQuantity(product.id) > 0"
                                    class="absolute top-1 right-1 z-10 flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-xs font-bold text-white"
                                >
                                    {{ getProductQuantity(product.id) }}
                                </div>

                                <div @click="addToCart(product)" class="aspect-w-1 aspect-h-1 w-full">
                                    <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="h-20 w-full object-cover" />
                                    <div v-else class="flex h-20 w-full items-center justify-center bg-gray-200">
                                        <Image class="h-8 w-8 text-gray-400" />
                                    </div>
                                </div>
                                <div class="p-2">
                                    <h3 class="truncate text-xs font-medium text-gray-900">{{ product.name }}</h3>
                                    <div class="mt-1 flex items-center justify-between">
                                        <p class="text-xs font-medium text-gray-900">{{ formatPrice(product.price) }}</p>
                                    </div>
                                    <!-- Quantity controls -->
                                    <div v-if="getProductQuantity(product.id) > 0" class="mt-1 flex items-center justify-between">
                                        <button @click.stop="decrementProductQuantity(product)" class="p-0.5 text-xs">
                                            <Minus class="h-3 w-3" />
                                        </button>
                                        <span class="text-xs font-medium">{{ getProductQuantity(product.id) }}</span>
                                        <button @click.stop="incrementProductQuantity(product)" class="p-0.5 text-xs">
                                            <Plus class="h-3 w-3" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart and Payment Section -->
                <div class="sticky top-6 space-y-6">
                    <!-- Cart -->
                    <div class="rounded-xl border border-gray-100 bg-white p-5 text-black shadow-xs">
                        <h2 class="mb-4 flex items-center text-xl font-semibold">
                            <ShoppingCart class="mr-2 h-5 w-5 text-gray-500" />
                            Shopping Cart
                        </h2>

                        <div v-if="cart.length === 0" class="py-8 text-center">
                            <ShoppingCart class="mx-auto h-12 w-12 text-gray-300" />
                            <p class="mt-3 text-gray-500">Your cart is empty</p>
                        </div>

                        <transition-group name="fade" tag="div" class="space-y-4">
                            <div v-for="item in cart" :key="item.product.id" class="flex items-center justify-between border-b border-gray-100 pb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-16 w-16 overflow-hidden rounded-lg bg-gray-100">
                                        <img
                                            v-if="item.product.image_url"
                                            :src="item.product.image_url"
                                            :alt="item.product.name"
                                            class="h-full w-full object-cover"
                                        />
                                        <div v-else class="flex h-full w-full items-center justify-center">
                                            <Image class="h-8 w-8 text-gray-400" />
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">{{ item.product.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ formatPrice(item.product.price) }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <button
                                        @click.stop="decrementQuantity(item)"
                                        class="rounded-full p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                                    >
                                        <Minus class="h-5 w-5" />
                                    </button>
                                    <span class="w-6 text-center text-sm font-medium">{{ item.quantity }}</span>
                                    <button
                                        @click.stop="incrementQuantity(item)"
                                        class="rounded-full p-1.5 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700"
                                    >
                                        <Plus class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </transition-group>

                        <!-- Discount Toggles -->
                        <div class="space-y-3 pt-3">
                            <!-- Custom Discount -->
                            <div class="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100">
                                        <Tag class="h-4 w-4 text-green-600" />
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-sm font-medium">Custom Discount</span>
                                        <div class="flex items-center">
                                            <input
                                                type="number"
                                                v-model="customDiscountPercentage"
                                                :disabled="!customDiscount"
                                                class="mr-1 w-12 rounded border border-gray-300 p-1 text-xs"
                                                min="0"
                                                max="100"
                                                step="1"
                                            />
                                            <span class="text-xs text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div @click="toggleCustomDiscount" class="cursor-pointer">
                                    <div
                                        :class="[
                                            'flex h-5 w-10 items-center rounded-full transition-colors duration-300',
                                            customDiscount ? 'justify-end bg-green-500' : 'justify-start bg-gray-200',
                                        ]"
                                    >
                                        <div class="mx-0.5 h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-300"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <img src="/assets/canzi.png" alt="Canzi" class="h-6 w-6 object-contain" />
                                    <div class="space-y-1">
                                        <span class="text-sm font-medium">{{ canziDiscount ? 'Canzi Commande' : 'Canzi' }}</span>
                                        <div class="flex items-center">
                                            <input
                                                type="number"
                                                v-model="canziDiscountPercentage"
                                                class="mr-1 w-12 rounded border border-gray-300 p-1 text-xs"
                                                min="0"
                                                max="100"
                                                step="1"
                                            />
                                            <span class="text-xs text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div @click="toggleCanziDiscount" class="cursor-pointer">
                                    <div
                                        :class="[
                                            'flex h-5 w-10 items-center rounded-full transition-colors duration-300',
                                            canziDiscount ? 'justify-end bg-green-500' : 'justify-start bg-gray-200',
                                        ]"
                                    >
                                        <div class="mx-0.5 h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-300"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between rounded-lg p-3 transition-colors hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <img src="/assets/glovo.png" alt="Glovo" class="h-6 w-6 object-contain" />
                                    <div class="space-y-1">
                                        <span class="text-sm font-medium">{{ glovoDiscount ? 'Glovo Commande' : 'Glovo' }}</span>
                                        <div class="flex items-center">
                                            <input
                                                type="number"
                                                v-model="glovoDiscountPercentage"
                                                class="mr-1 w-12 rounded border border-gray-300 p-1 text-xs"
                                                min="0"
                                                max="100"
                                                step="1"
                                            />
                                            <span class="text-xs text-gray-500">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div @click="toggleGlovoDiscount" class="cursor-pointer">
                                    <div
                                        :class="[
                                            'flex h-5 w-10 items-center rounded-full transition-colors duration-300',
                                            glovoDiscount ? 'justify-end bg-green-500' : 'justify-start bg-gray-200',
                                        ]"
                                    >
                                        <div class="mx-0.5 h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-300"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="space-y-3 pt-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium">{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div v-if="canziDiscount || glovoDiscount || customDiscount" class="flex justify-between text-sm text-green-600">
                                <span>Discount</span>
                                <span>-{{ formatPrice(discountAmount) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium">
                                <span class="text-gray-500">Total</span>
                                <span class="font-medium">{{ formatPrice(total) }}</span>
                            </div>
                            <div v-if="canziDiscount" class="flex items-center text-sm text-green-600">
                                <span>{{ canziDiscount ? 'Canzi Commande' : 'Canzi' }} ({{ canziDiscountPercentage }}%) will be applied</span>
                            </div>
                            <div v-if="glovoDiscount" class="flex items-center text-sm text-green-600">
                                <span>{{ glovoDiscount ? 'Glovo Commande' : 'Glovo' }} ({{ glovoDiscountPercentage }}%) will be applied</span>
                            </div>
                            <div v-if="customDiscount" class="flex items-center text-sm text-green-600">
                                <span>Custom Discount ({{ customDiscountPercentage }}%) will be applied</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="rounded-xl border border-gray-100 bg-white p-5 text-black shadow-xs">
                        <h2 class="mb-4 flex items-center text-xl font-semibold">
                            <CreditCard class="mr-2 h-5 w-5 text-gray-500" />
                            Payment
                        </h2>

                        <div class="space-y-5">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Amount Received</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"></div>
                                    <input
                                        type="number"
                                        v-model="amountReceived"
                                        class="block w-full rounded-lg border-gray-300 py-2.5 pr-12 pl-8 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-500"
                                        placeholder="0.00"
                                        min="0"
                                        step="0.01"
                                    />
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500">DH</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">Quick Amounts</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-if="total > 0"
                                        @click="amountReceived = Math.ceil(total)"
                                        :class="[
                                            'relative rounded-lg bg-green-100 px-3 py-2 text-sm font-medium transition-colors',
                                            amountReceived === Math.ceil(total)
                                                ? 'bg-green-500 text-white'
                                                : 'border border-green-300 text-green-700 hover:bg-green-50',
                                        ]"
                                    >
                                        <div class="text-base">{{ Math.ceil(total) }} DH</div>
                                        <div class="text-xs">Total exact</div>
                                    </button>
                                    <button
                                        v-for="amount in [20, 50, 100, 200, 500, 1000]"
                                        :key="amount"
                                        @click="amountReceived = amount"
                                        :class="[
                                            'rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                            amountReceived === amount
                                                ? 'bg-green-500 text-white'
                                                : 'border border-gray-200 bg-white text-gray-600 hover:bg-gray-100',
                                        ]"
                                    >
                                        {{ amount }} DH
                                    </button>
                                </div>
                            </div>

                            <div v-if="amountReceived > 0" class="space-y-1">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Change</span>
                                    <span :class="change >= 0 ? 'font-medium text-green-500' : 'font-medium text-red-500'">
                                        {{ formatPrice(Math.max(0, change)) }}
                                    </span>
                                </div>
                                <div v-if="change < 0" class="flex items-center text-xs text-red-500">
                                    <AlertCircle class="mr-1 h-3.5 w-3.5" />
                                    Insufficient amount ({{ formatPrice(Math.abs(change)) }} more needed)
                                </div>
                            </div>

                            <button
                                @click="showConfirmModal = true"
                                :disabled="!canSubmitOrder"
                                :class="[
                                    'w-full rounded-lg px-4 py-3 text-sm font-semibold text-white transition-colors focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none',
                                    canSubmitOrder
                                        ? 'bg-gradient-to-r from-green-500 to-green-600 shadow-sm hover:from-green-600 hover:to-green-700'
                                        : 'cursor-not-allowed bg-gray-300',
                                ]"
                            >
                                Complete Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Confirm Modal -->
        <div v-if="showConfirmModal" class="fixed inset-0 z-50 transition-opacity duration-300 ease-out">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-all duration-300"></div>
            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    class="w-full max-w-md scale-100 transform rounded-2xl bg-white opacity-100 shadow-[0_0_50px_rgba(0,0,0,0.15)] transition-all duration-300"
                >
                    <div class="relative p-8">
                        <!-- Close button -->
                        <button @click="showConfirmModal = false" class="absolute top-4 right-4 text-gray-400 transition-colors hover:text-gray-500">
                            <X class="h-5 w-5" />
                        </button>
                        <h3 class="mb-6 text-xl font-semibold text-gray-900">Confirm Order</h3>
                        <div class="space-y-4">
                            <div class="border-b border-gray-100 pb-4">
                                <h4 class="mb-3 text-sm font-medium text-gray-600">Order Items</h4>
                                <div v-for="item in cart" :key="item.product.id" class="mb-2 flex justify-between">
                                    <div class="-mx-2 flex w-full justify-between rounded-lg p-2 transition-colors hover:bg-gray-50">
                                        <div class="text-sm text-gray-600">
                                            <div>{{ item.product.name }}</div>
                                            <div class="text-xs text-gray-500">{{ item.quantity }} x {{ formatPrice(item.product.price) }}</div>
                                        </div>
                                        <span class="text-sm font-medium">{{ formatPrice(item.product.price * item.quantity) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3 pt-2">
                                <h4 class="mb-1 text-sm font-medium text-gray-600">Order Summary</h4>
                                <div class="-mx-2 rounded-lg bg-gray-50 p-2">
                                    <div class="flex justify-between text-gray-900">
                                        <span class="text-sm">Subtotal</span>
                                        <span class="text-sm font-medium">{{ formatPrice(subtotal) }}</span>
                                    </div>
                                    <div v-if="canziDiscount || glovoDiscount || customDiscount" class="flex justify-between text-green-600">
                                        <span class="text-sm">Discount</span>
                                        <span class="text-sm font-medium">-{{ formatPrice(discountAmount) }}</span>
                                    </div>
                                    <div class="flex justify-between font-medium text-gray-900">
                                        <span class="text-sm">Total</span>
                                        <span class="text-sm">{{ formatPrice(total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button
                                @click="submitOrder"
                                class="flex-1 rounded-xl bg-green-600 px-6 py-3 font-medium text-white shadow-sm transition-colors duration-200 hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:outline-none disabled:opacity-50"
                                :disabled="processing"
                            >
                                <span class="flex items-center justify-center">
                                    <LoaderCircle v-if="processing" class="mr-2 -ml-1 h-4 w-4 animate-spin text-white" />
                                    {{ processing ? 'Processing...' : 'Confirm' }}
                                </span>
                            </button>
                            <button
                                @click="showConfirmModal = false"
                                class="flex-1 rounded-xl border bg-white px-6 py-3 font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50"
                                :disabled="processing"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 transition-opacity duration-300 ease-out">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-all duration-300"></div>
            <!-- Modal Container -->
            <div class="fixed inset-0 flex items-center justify-center p-4">
                <div
                    class="w-full max-w-md scale-100 transform rounded-2xl bg-white opacity-100 shadow-[0_0_50px_rgba(0,0,0,0.15)] transition-all duration-300"
                >
                    <div class="relative p-8">
                        <!-- Close button -->
                        <button @click="closeSuccessModal" class="absolute top-4 right-4 text-gray-400 transition-colors hover:text-gray-500">
                            <X class="h-5 w-5" />
                        </button>
                        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                            <Check class="h-6 w-6 text-green-600" />
                        </div>
                        <h3 class="mb-4 text-center text-lg font-medium text-gray-900">Order Complete!</h3>
                        <div class="mb-4 border-b border-gray-100 pb-4">
                            <h4 class="mb-3 text-sm font-medium text-gray-600">Order Details</h4>
                            <div v-for="item in orderDetails?.orderItems" :key="item.id" class="mb-2 flex justify-between">
                                <div class="-mx-2 flex w-full justify-between rounded-lg p-2 transition-colors hover:bg-gray-50">
                                    <span class="text-sm text-gray-600">{{ item.product.name }} × {{ item.quantity }}</span>
                                    <span class="text-sm font-medium">{{ formatPrice(item.price) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 pt-2">
                            <h4 class="mb-1 text-sm font-medium text-gray-600">Payment Summary</h4>
                            <div class="-mx-2 rounded-lg bg-green-50 p-2">
                                <div class="flex justify-between text-gray-900">
                                    <span class="font-medium">Total</span>
                                    <span class="font-medium">{{ formatPrice(orderDetails?.total) }}</span>
                                </div>
                            </div>
                            <div v-if="orderDetails?.discount > 0" class="flex justify-between text-green-600">
                                <span class="text-sm">Discount Applied</span>
                                <span class="text-sm font-medium">-{{ formatPrice(orderDetails?.discount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Amount Received</span>
                                <span class="text-sm font-medium">{{ formatPrice(orderDetails?.mnt_recu) }}</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span class="text-sm">Change</span>
                                <span class="text-sm font-medium">{{ formatPrice(orderDetails?.mnt_rendu) }}</span>
                            </div>
                        </div>
                        <button
                            @click="printReceipt"
                            class="mt-6 mb-3 w-full rounded-xl bg-blue-600 px-6 py-3 font-medium text-white shadow-sm transition-colors duration-200 hover:bg-blue-700"
                        >
                            <span class="flex items-center justify-center">
                                <Printer class="mr-2 h-5 w-5" />
                                Print Receipt
                            </span>
                        </button>
                        <button
                            @click="closeSuccessModal"
                            class="w-full rounded-xl bg-green-600 px-6 py-3 font-medium text-white shadow-sm transition-colors duration-200 hover:bg-green-700"
                        >
                            New Order
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Alert -->
        <div v-if="errorMessage" class="fixed right-4 bottom-4 z-40 rounded-md border-l-4 border-red-400 bg-red-50 p-4 shadow-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <AlertCircle class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ errorMessage }}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button @click="errorMessage = ''" class="text-red-400 hover:text-red-500">
                        <X class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { AlertCircle, Check, CreditCard, FileText, Image, LoaderCircle, Minus, Plus, Printer, ShoppingCart, Tag, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

interface Category {
    id: number;
    name: string;
    products?: Product[];
}

interface Product {
    id: number;
    name: string;
    price: number;
    image_url?: string;
    category?: Category;
}

interface CartItem {
    product: Product;
    quantity: number;
}

interface OrderDetails {
    id: number;
    total: number;
    mnt_recu: number;
    mnt_rendu: number;
    discount: number;
    discount_type?: string;
    orderItems: {
        id: number;
        product: Product;
        quantity: number;
        price: number;
    }[];
}

// Discount percentages
const canziDiscountPercentage = ref(15);
const glovoDiscountPercentage = ref(33);
const customDiscountPercentage = ref(0);
const customDiscount = ref(false);

const props = defineProps<{
    categories: Category[];
}>();

// State
const selectedCategory = ref<number | null>(null);
const cart = ref<CartItem[]>([]);
const amountReceived = ref<number>(0);
const showSuccessModal = ref<boolean>(false);
const showConfirmModal = ref<boolean>(false);
const orderDetails = ref<OrderDetails | null>(null);
const processing = ref<boolean>(false);
const errorMessage = ref<string>('');
const canziDiscount = ref<boolean>(false);
const glovoDiscount = ref<boolean>(false);

// Computed
const filteredProducts = computed(() => {
    if (selectedCategory.value === null) {
        return props.categories.flatMap((cat: Category) => cat.products || []);
    }
    const category = props.categories.find((c: Category) => c.id === selectedCategory.value);
    return category?.products || [];
});

const subtotal = computed(() => {
    return cart.value.reduce((sum: number, item: CartItem) => sum + item.product.price * item.quantity, 0);
});

const total = computed(() => {
    const discountAmount = canziDiscount.value
        ? (subtotal.value * canziDiscountPercentage.value) / 100
        : glovoDiscount.value
          ? (subtotal.value * glovoDiscountPercentage.value) / 100
          : customDiscount.value
            ? (subtotal.value * customDiscountPercentage.value) / 100
            : 0;
    return subtotal.value - discountAmount;
});

// New computed property to calculate actual discount amount
const discountAmount = computed(() => {
    if (canziDiscount.value) {
        return (subtotal.value * canziDiscountPercentage.value) / 100;
    } else if (glovoDiscount.value) {
        return (subtotal.value * glovoDiscountPercentage.value) / 100;
    } else if (customDiscount.value) {
        return (subtotal.value * customDiscountPercentage.value) / 100;
    }
    return 0;
});

const change = computed(() => {
    return amountReceived.value - total.value;
});

const canSubmitOrder = computed(() => {
    return cart.value.length > 0 && amountReceived.value >= total.value;
});

const activeDiscountType = computed(() => {
    if (canziDiscount.value) return `Canzi (${canziDiscountPercentage.value}%)`;
    if (glovoDiscount.value) return `Glovo (${glovoDiscountPercentage.value}%)`;
    if (customDiscount.value) return `Custom (${customDiscountPercentage.value}%)`;
    return null;
});

// Methods
const showError = (message: string) => {
    errorMessage.value = message;
    setTimeout(() => {
        errorMessage.value = '';
    }, 5000);
};

const toggleCustomDiscount = () => {
    customDiscount.value = !customDiscount.value;
    if (customDiscount.value) {
        canziDiscount.value = false;
        glovoDiscount.value = false;
    }
};

const toggleCanziDiscount = () => {
    canziDiscount.value = !canziDiscount.value;
    if (canziDiscount.value) {
        glovoDiscount.value = false;
        customDiscount.value = false;
    }
};

const toggleGlovoDiscount = () => {
    glovoDiscount.value = !glovoDiscount.value;
    if (glovoDiscount.value) {
        canziDiscount.value = false;
        customDiscount.value = false;
    }
};

const printReceipt = async () => {
    if (!orderDetails.value) {
        showError('Order details are not available for printing');
        return;
    }

    try {
        // Fetch the latest receipt data from the server
        const response = await axios.get(`/customer-order/receipt/${orderDetails.value.id}`);

        if (response.data.success) {
            const receipt = response.data.receipt;

            const receiptWindow = window.open('', '_blank', 'width=350,height=600,toolbar=no,scrollbars=no');
            if (receiptWindow) {
                receiptWindow.document.write(`
          <!DOCTYPE html>
          <html>
          <head>
            <title>Reçu #${receipt.order_id}</title>
            <style>
              body {
                margin: 0;
                padding: 10px;
                font-family: 'Courier New', monospace;
                font-size: 14px;
              }
              .receipt {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
              }
              .header, .footer {
                text-align: center;
                margin: 10px 0;
              }
              .divider {
                border-top: 1px dashed #000;
                margin: 10px 0;
                text-align: center;
              }
              .items {
                width: 100%;
                margin: 10px 0;
              }
              .item-row {
                display: flex;
                justify-content: space-between;
                margin: 2px 0;
              }
              .item-name {
                flex: 2;
              }
              .item-qty {
                flex: 1;
                text-align: center;
              }
              .item-price {
                flex: 1;
                text-align: right;
              }
              .summary {
                margin: 10px 0;
              }
              .total-row {
                font-weight: bold;
                margin: 5px 0;
              }
              .double-divider {
                border-top: 3px double #000;
                margin: 10px 0;
              }
              @media print {
                body { padding: 0; }
              }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="double-divider"></div>

              <div class="header">
                <h2>SALADAY</h2>
                ${canziDiscount.value ? '<p>Commande By Canzi</p>' : ''}
                ${glovoDiscount.value ? '<p>Commande By Glovo</p>' : ''}
                <p>instagram : @saladay.meeka</p>
                <p>téléphone : 06 03 82 29 54</p>
              </div>

              <div class="divider"></div>

              <div>
                <p>Caissier: ZAKARIA</p>
                <p>Date: ${receipt.date}</p>
                <p>Reçu #: REC-${receipt.order_id}</p>
              </div>

              <div class="divider"></div>

              <div class="items">
                <div class="item-row">
                  <span class="item-name"><strong>Produit</strong></span>
                  <span class="item-qty"><strong>Qnt</strong></span>
                  <span class="item-price"><strong>Prix</strong></span>
                </div>
                <div class="divider"></div>
                ${receipt.items
                    .map(
                        (item) => `
                  <div class="item-row">
                    <span class="item-name">${item.name}</span>
                    <span class="item-qty">${item.quantity}</span>
                    <span class="item-price">${formatPrice(item.price)}</span>
                  </div>
                `,
                    )
                    .join('')}
              </div>

              <div class="divider"></div>

              <div class="summary">
                <div class="item-row">
                  <span>Sous-total:</span>
                  <span>${formatPrice(receipt.subtotal)}</span>
                </div>
                ${
                    receipt.discount > 0
                        ? `
                <div class="item-row">
                  <span>Remise:</span>
                  <span>-${formatPrice(receipt.discount)}</span>
                </div>
                `
                        : ''
                }
                <div class="item-row">
                  <span>Montant reçu:</span>
                  <span>${formatPrice(receipt.amount_received)}</span>
                </div>
                <div class="item-row">
                  <span>Monnaie rendue:</span>
                  <span>${formatPrice(receipt.change)}</span>
                </div>
                <div class="item-row total-row">
                  <span>TOTAL:</span>
                  <span>${formatPrice(receipt.total)}</span>
                </div>
              </div>

              <div class="double-divider"></div>

              <div class="footer">
                <p>Merci de votre visite!</p>
              </div>

              <div class="double-divider"></div>
            </div>
            <script>
              window.onload = function() {
                setTimeout(function() {
                  window.print();
                  setTimeout(function() {
                    window.close();
                  }, 500);
                }, 200);
              }
            <\/script>
          </body>
          </html>
        `);
                receiptWindow.document.close();
            }
        } else {
            throw new Error('Failed to fetch receipt data');
        }
    } catch (error) {
        console.error('Printing error:', error);
        showError('Failed to generate receipt. Please try again.');
    }
};

const addToCart = (product: Product) => {
    const existingItem = cart.value.find((item: CartItem) => item.product.id === product.id);
    if (existingItem) {
        existingItem.quantity++;
        animateProduct(product.id); // Trigger animation for product
    } else {
        cart.value.push({
            product,
            quantity: 1,
        });
        animateProduct(product.id); // Trigger animation for product
    }
};

const animateProduct = (productId: number) => {
    const productElement = document.querySelector(`[data-product-id="${productId}"]`);
    if (productElement) {
        productElement.classList.add('animate-add');
        setTimeout(() => productElement.classList.remove('animate-add'), 500);
    }
};

const incrementQuantity = (item: CartItem) => {
    item.quantity++;
};

const decrementQuantity = (item: CartItem) => {
    if (item.quantity > 1) {
        item.quantity--;
    } else {
        cart.value = cart.value.filter((cartItem: CartItem) => cartItem.product.id !== item.product.id);
    }
};

const formatPrice = (price: number | undefined) => {
    if (price === undefined) return '0.00 DH ';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'MAD',
    }).format(price);
};

const submitOrder = async () => {
    if (!canSubmitOrder.value) return;

    try {
        processing.value = true;
        showConfirmModal.value = false;

        // Calculate the discount amount properly
        const discount = discountAmount.value;
        const discountType = activeDiscountType.value;

        // Log data being sent to backend
        console.log('Sending order data:', {
            items: cart.value.map((item: CartItem) => ({
                product_id: item.product.id,
                quantity: item.quantity,
            })),
            subtotal: subtotal.value,
            discount: discount,
            discount_type: discountType,
            total: total.value,
            mnt_recu: amountReceived.value,
        });

        const response = await axios.post('/customer-order', {
            items: cart.value.map((item: CartItem) => ({
                product_id: item.product.id,
                quantity: item.quantity,
            })),
            discount: discount,
            discount_type: discountType,
            mnt_recu: amountReceived.value,
        });

        console.log('Response received:', response.data);

        if (response.data.success) {
            orderDetails.value = response.data.order;
            showSuccessModal.value = true;
        } else {
            throw new Error(response.data.message || 'Order could not be completed');
        }
    } catch (error: any) {
        console.error('Order submission error:', error);
        showError(error.response?.data?.message || error.message || 'An error occurred while processing your order');
    } finally {
        processing.value = false;
    }
};

const closeSuccessModal = () => {
    showSuccessModal.value = false;
    cart.value = [];
    amountReceived.value = 0;
    orderDetails.value = null;
    canziDiscount.value = false;
    glovoDiscount.value = false;
};

const getProductQuantity = (productId: number) => {
    const item = cart.value.find((item) => item.product.id === productId);
    return item ? item.quantity : 0;
};

const incrementProductQuantity = (product: Product) => {
    const existingItem = cart.value.find((item) => item.product.id === product.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.value.push({
            product,
            quantity: 1,
        });
    }
    animateProduct(product.id);
};

const decrementProductQuantity = (product: Product) => {
    const existingItemIndex = cart.value.findIndex((item) => item.product.id === product.id);
    if (existingItemIndex !== -1) {
        if (cart.value[existingItemIndex].quantity > 1) {
            cart.value[existingItemIndex].quantity--;
        } else {
            cart.value.splice(existingItemIndex, 1);
        }
    }
};


const removeFromCart = (product: Product) => {
    cart.value = cart.value.filter((item) => item.product.id !== product.id);
};
</script>

<style scoped>
.aspect-w-1 {
    position: relative;
    padding-bottom: 100%;
}

.aspect-w-1 > * {
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

/* Quick amount buttons styling */
.grid-cols-3 button {
    min-height: 4rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 0.5rem;
    gap: 0.25rem;
}

.grid-cols-3 button > div:first-child {
    font-weight: 600;
    font-size: 1rem;
}

.grid-cols-3 button > div:last-child {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Add fade animation for transition-group */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Add animation for product addition */
.animate-add {
    animation: scale-up 0.5s ease-in-out;
}

@keyframes scale-up {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}
</style>
