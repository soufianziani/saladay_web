<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Order Management</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Products and Categories Section -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Categories -->
          <div class="bg-white rounded-lg shadow-sm p-4">
            <h2 class="text-lg font-medium mb-3">Categories</h2>
            <div class="flex space-x-2 overflow-x-auto pb-2">
              <button 
                @click="selectedCategory = null" 
                :class="[
                  'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap',
                  selectedCategory === null 
                    ? 'bg-green-600 text-white' 
                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                ]"
              >
                All
              </button>
              <button 
                v-for="category in categories" 
                :key="category.id"
                @click="selectedCategory = category.id"
                :class="[
                  'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap',
                  selectedCategory === category.id 
                    ? 'bg-green-600 text-white' 
                    : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                ]"
              >
                {{ category.name }}
              </button>
            </div>
          </div>

          <!-- Products Grid -->
          <div class="bg-white rounded-lg shadow-sm p-4">
            <h2 class="text-lg font-medium mb-4">Products</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
              <div 
                v-for="product in filteredProducts" 
                :key="product.id"
                @click="addToCart(product)"
                class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow cursor-pointer bg-white"
              >
                <div class="aspect-w-1 aspect-h-1 w-full">
                  <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="object-cover h-32 w-full">
                  <div v-else class="h-32 w-full bg-gray-200 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div>
                <div class="p-3">
                  <h3 class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</h3>
                  <div class="flex justify-between items-center mt-1">
                    <p class="text-sm font-medium text-gray-900">{{ formatPrice(product.price) }}</p>
                    <span class="text-xs text-gray-500">{{ product.category?.name }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Cart and Payment Section -->

<div class="space-y-6">
  <!-- Cart -->
  <div class="bg-white rounded-xl shadow-xs p-5 border border-gray-100">
    <h2 class="text-xl font-semibold mb-4 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
      </svg>
      Shopping Cart
    </h2>
    
    <div v-if="cart.length === 0" class="text-center py-8">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      <p class="mt-3 text-gray-500">Your cart is empty</p>
    </div>
    
    <div v-else class="space-y-4">
      <div v-for="item in cart" :key="item.product.id" class="flex justify-between items-center pb-4 border-b border-gray-100">
        <div class="flex items-center space-x-3">
          <div class="bg-gray-100 rounded-lg p-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
          </div>
          <div>
            <h3 class="text-sm font-medium text-gray-900">{{ item.product.name }}</h3>
            <p class="text-sm text-gray-500">{{ formatPrice(item.product.price) }}</p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <button @click.stop="decrementQuantity(item)" class="p-1.5 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 10a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1z" clip-rule="evenodd" />
            </svg>
          </button>
          <span class="text-sm font-medium w-6 text-center">{{ item.quantity }}</span>
          <button @click.stop="incrementQuantity(item)" class="p-1.5 rounded-full hover:bg-gray-100 text-gray-500 hover:text-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Discount -->
      <div class="pt-3">
        <button @click="applyDiscount = !applyDiscount" class="w-full flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
            </svg>
            <span class="text-sm font-medium">Apply Discount</span>
          </div>
          <div class="flex items-center">
            <span class="text-xs text-gray-500 mr-2">{{ applyDiscount ? 'On' : 'Off' }}</span>
            <div :class="['w-10 h-5 rounded-full flex items-center transition-colors duration-300', applyDiscount ? 'bg-green-500 justify-end' : 'bg-gray-200 justify-start']">
              <div class="w-4 h-4 rounded-full bg-white shadow-sm transform transition-transform duration-300 mx-0.5"></div>
            </div>
          </div>
        </button>
        
        <div v-if="applyDiscount" class="mt-3 space-y-3 bg-gray-50 p-3 rounded-lg">
          <div class="relative">
            <input
              type="number"
              v-model="discountPercentage"
              class="w-full pl-4 pr-12 py-2.5 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500"
              placeholder="Enter discount %"
              min="0"
              max="100"
            >
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
              <span class="text-gray-500 text-sm">%</span>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="percent in [10, 20, 30, 40, 50]"
              :key="percent"
              @click="discountPercentage = percent"
              :class="[
                'px-3 py-1.5 text-xs rounded-lg font-medium transition-colors',
                discountPercentage === percent
                  ? 'bg-green-500 text-white'
                  : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100'
              ]"
            >
              {{ percent }}%
            </button>
          </div>
        </div>
      </div>

      <!-- Summary -->
      <div class="space-y-3 pt-2">
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Subtotal</span>
          <span class="font-medium">{{ formatPrice(subtotal) }}</span>
        </div>
        <div v-if="applyDiscount" class="flex justify-between text-sm">
          <span class="text-gray-500">Discount ({{ discountPercentage }}%)</span>
          <span class="text-green-500">-{{ formatPrice(discountAmount) }}</span>
        </div>
        <div class="border-t border-gray-100 pt-3 flex justify-between text-base">
          <span class="font-semibold">Total</span>
          <span class="font-bold">{{ formatPrice(total) }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment -->
  <div class="bg-white rounded-xl shadow-xs p-5 border border-gray-100">
    <h2 class="text-xl font-semibold mb-4 flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
      </svg>
      Payment
    </h2>
    
    <div class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Amount Received</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <span class="text-gray-500">$</span>
          </div>
          <input
            type="number"
            v-model="amountReceived"
            class="block w-full pl-8 pr-12 py-2.5 text-sm rounded-lg border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500"
            placeholder="0.00"
            min="0"
            step="0.01"
          >
          <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
            <span class="text-gray-500">USD</span>
          </div>
        </div>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Amounts</label>
        <div class="grid grid-cols-3 gap-2">
          <button
            v-for="amount in [20, 50, 100, 200, 500, 1000]"
            :key="amount"
            @click="amountReceived = amount"
            :class="[
              'px-3 py-2 text-sm rounded-lg font-medium transition-colors',
              amountReceived === amount
                ? 'bg-green-500 text-white'
                : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-100'
            ]"
          >
            ${{ amount }}
          </button>
        </div>
      </div>

      <div v-if="amountReceived > 0" class="space-y-1">
        <div class="flex justify-between text-sm">
          <span class="text-gray-500">Change</span>
          <span :class="change >= 0 ? 'text-green-500 font-medium' : 'text-red-500 font-medium'">
            {{ formatPrice(Math.max(0, change)) }}
          </span>
        </div>
        <div v-if="change < 0" class="text-xs text-red-500 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          Insufficient amount ({{ formatPrice(Math.abs(change)) }} more needed)
        </div>
      </div>

      <button 
        @click="showConfirmModal = true" 
        :disabled="!canSubmitOrder"
        :class="[
          'w-full py-3 px-4 rounded-lg text-sm font-semibold text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
          canSubmitOrder ? 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 shadow-sm' : 'bg-gray-300 cursor-not-allowed'
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
    <div v-if="showConfirmModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Order</h3>
          <div class="space-y-4">
            <div class="border-b pb-4">
              <div v-for="item in cart" :key="item.product.id" class="flex justify-between mb-2">
                <span class="text-sm text-gray-600">{{ item.product.name }} × {{ item.quantity }}</span>
                <span class="text-sm font-medium">{{ formatPrice(item.product.price * item.quantity) }}</span>
              </div>
            </div>
            <div class="space-y-2">
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Subtotal</span>
                <span class="text-sm font-medium">{{ formatPrice(subtotal) }}</span>
              </div>
              <div v-if="applyDiscount" class="flex justify-between text-green-600">
                <span class="text-sm">Discount ({{ discountPercentage }}%)</span>
                <span class="text-sm font-medium">-{{ formatPrice(discountAmount) }}</span>
              </div>
              <div class="flex justify-between text-gray-900 font-medium">
                <span>Total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-gray-600">Amount Received</span>
                <span class="text-sm font-medium">{{ formatPrice(amountReceived) }}</span>
              </div>
              <div class="flex justify-between text-green-600">
                <span class="text-sm">Change</span>
                <span class="text-sm font-medium">{{ formatPrice(change) }}</span>
              </div>
            </div>
          </div>
          <div class="mt-6 flex gap-3">
            <button
              @click="submitOrder"
              class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
              :disabled="processing"
            >
              {{ processing ? 'Processing...' : 'Confirm' }}
            </button>
            <button
              @click="showConfirmModal = false"
              class="flex-1 bg-white text-gray-700 py-2 px-4 rounded-md border hover:bg-gray-50"
              :disabled="processing"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-center text-gray-900 mb-4">Order Complete!</h3>
          <div class="border-b pb-4 mb-4">
            <div v-for="item in orderDetails?.orderItems" :key="item.id" class="flex justify-between mb-2">
              <span class="text-sm text-gray-600">{{ item.product.name }} × {{ item.quantity }}</span>
              <span class="text-sm font-medium">{{ formatPrice(item.price) }}</span>
            </div>
          </div>
          <div class="space-y-2">
            <div class="flex justify-between text-gray-900">
              <span class="font-medium">Total</span>
              <span class="font-medium">{{ formatPrice(orderDetails?.total) }}</span>
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
            @click="getReceipt(orderDetails.id)"
            class="mt-6 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 mb-2"
          >
            Get Receipt
          </button>
          <button
            @click="closeSuccessModal"
            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700"
          >
            New Order
          </button>
        </div>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="fixed bottom-4 right-4 bg-red-50 border-l-4 border-red-400 p-4 shadow-md rounded-md">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700">{{ errorMessage }}</p>
        </div>
        <div class="pl-3 ml-auto">
          <button @click="errorMessage = ''" class="text-red-400 hover:text-red-500">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import axios from 'axios'

interface ReceiptItem {
  name: string;
  quantity: number;
  price: number;
  subtotal: number;
}

interface Receipt {
  order_id: number;
  date: string;
  items: ReceiptItem[];
  subtotal: number;
  discount: number;
  total: number;
  amount_received: number;
  change: number;
}

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

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
  orderItems: {
    id: number;
    product: Product;
    quantity: number;
    price: number;
  }[];
}

const props = defineProps<{
  categories: Category[];
}>();

// State
const selectedCategory = ref<number | null>(null)
const cart = ref<CartItem[]>([])
const amountReceived = ref<number>(0)
const showSuccessModal = ref<boolean>(false)
const showConfirmModal = ref<boolean>(false)
const orderDetails = ref<OrderDetails | null>(null)
const processing = ref<boolean>(false)
const errorMessage = ref<string>('')
const applyDiscount = ref<boolean>(false)
const discountPercentage = ref<number>(10)

// Computed
const filteredProducts = computed(() => {
  if (selectedCategory.value === null) {
    return props.categories.flatMap((cat: Category) => cat.products || [])
  }
  const category = props.categories.find((c: Category) => c.id === selectedCategory.value)
  return category?.products || []
})

const subtotal = computed(() => {
  return cart.value.reduce((sum: number, item: CartItem) => sum + (item.product.price * item.quantity), 0)
})

const discountAmount = computed(() => {
  if (!applyDiscount.value) return 0
  return subtotal.value * (discountPercentage.value / 100)
})

const total = computed(() => subtotal.value - discountAmount.value)

const change = computed(() => {
  return amountReceived.value - total.value
})

const canSubmitOrder = computed(() => {
  return cart.value.length > 0 && amountReceived.value >= total.value
})

// Methods
const showError = (message: string) => {
  errorMessage.value = message
  setTimeout(() => {
    errorMessage.value = ''
  }, 5000)
}

const getReceipt = async (orderId: number) => {
  try {
    const response = await axios.get<{success: boolean; receipt: Receipt}>(`/customer-order/${orderId}/receipt`);
    if (response.data.success) {
      const receipt: Receipt = response.data.receipt;
      const content = `
        <html>
          <head>
            <title>Order Receipt #${receipt.order_id}</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 20px; }
              .header { text-align: center; margin-bottom: 20px; }
              .items { margin: 20px 0; border-bottom: 1px solid #eee; }
              .item { padding: 10px 0; display: flex; justify-content: space-between; }
              .totals { margin-top: 20px; }
              .total-row { display: flex; justify-content: space-between; margin: 5px 0; }
            </style>
          </head>
          <body>
            <div class="header">
              <h2>Order Receipt</h2>
              <p>Order #${receipt.order_id}</p>
              <p>Date: ${receipt.date}</p>
            </div>
            <div class="items">
              ${receipt.items.map(item => `
                <div class="item">
                  <span>${item.name} x ${item.quantity}</span>
                  <span>${formatPrice(item.price)}</span>
                </div>
              `).join('')}
            </div>
            <div class="totals">
              <div class="total-row">
                <span>Subtotal:</span>
                <span>${formatPrice(receipt.subtotal)}</span>
              </div>
              ${receipt.discount > 0 ? `
                <div class="total-row">
                  <span>Discount:</span>
                  <span>-${formatPrice(receipt.discount)}</span>
                </div>
              ` : ''}
              <div class="total-row" style="font-weight: bold;">
                <span>Total:</span>
                <span>${formatPrice(receipt.total)}</span>
              </div>
              <div class="total-row">
                <span>Amount Received:</span>
                <span>${formatPrice(receipt.amount_received)}</span>
              </div>
              <div class="total-row">
                <span>Change:</span>
                <span>${formatPrice(receipt.change)}</span>
              </div>
            </div>
          </body>
        </html>
      `;
      
      const receiptWindow = window.open('', '_blank');
      receiptWindow?.document.write(content);
      receiptWindow?.document.close();
    }
  } catch (error: unknown) {
    const errorMessage = error instanceof Error ? error.message : 'Failed to generate receipt';
    showError(errorMessage);
  }
}

const addToCart = (product: Product) => {
  const existingItem = cart.value.find((item: CartItem) => item.product.id === product.id)
  if (existingItem) {
    existingItem.quantity++
  } else {
    cart.value.push({
      product,
      quantity: 1
    })
  }
}

const incrementQuantity = (item: CartItem) => {
  item.quantity++
}

const decrementQuantity = (item: CartItem) => {
  if (item.quantity > 1) {
    item.quantity--
  } else {
    cart.value = cart.value.filter((cartItem: CartItem) => cartItem.product.id !== item.product.id)
  }
}

const formatPrice = (price: number | undefined) => {
  if (!price) return '$0.00'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price)
}

const submitOrder = async () => {
  if (!canSubmitOrder.value) return;
  
  try {
    processing.value = true;
    showConfirmModal.value = false;

    const response = await axios.post('/customer-order', {
      items: cart.value.map((item: CartItem) => ({
        product_id: item.product.id,
        quantity: item.quantity
      })),
      discount: discountAmount.value,
      mnt_recu: amountReceived.value
    });

    if (response.data.success) {
      orderDetails.value = response.data.order;
      showSuccessModal.value = true;
    } else {
      throw new Error(response.data.message || 'Order could not be completed');
    }
  } catch (error: any) {
    console.error('Order submission error:', error);
    showError(
      error.response?.data?.message ||
      error.message ||
      'An error occurred while processing your order'
    );
  } finally {
    processing.value = false;
  }
}

const closeSuccessModal = () => {
  showSuccessModal.value = false
  cart.value = []
  amountReceived.value = 0
  orderDetails.value = null
  applyDiscount.value = false
  discountPercentage.value = 10
}
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
</style>