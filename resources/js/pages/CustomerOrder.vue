<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Order Management</h1>
        <Link
          href="/orders"
          class="flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          Orders History
        </Link>
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
          <div class="bg-white rounded-xl text-black shadow-xs p-5 border border-gray-100">
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
                  <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
                    <img
                      v-if="item.product.image_url"
                      :src="item.product.image_url"
                      :alt="item.product.name"
                      class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
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

              <!-- Discount Toggles -->
              <div class="pt-3 space-y-3">
                <!-- Custom Discount -->
                <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <div class="flex items-center space-x-3">
                    <div class="h-6 w-6 flex items-center justify-center bg-green-100 rounded-full">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="space-y-1">
                      <span class="text-sm font-medium">Custom Discount</span>
                      <div class="flex items-center">
                        <input
                          type="number"
                          v-model="customDiscountPercentage"
                          :disabled="!customDiscount"
                          class="w-12 text-xs border border-gray-300 rounded p-1 mr-1"
                          min="0"
                          max="100"
                          step="1"
                        />
                        <span class="text-xs text-gray-500">%</span>
                      </div>
                    </div>
                  </div>
                  <div @click="toggleCustomDiscount" class="cursor-pointer">
                    <div :class="['w-10 h-5 rounded-full flex items-center transition-colors duration-300', customDiscount ? 'bg-green-500 justify-end' : 'bg-gray-200 justify-start']">
                      <div class="w-4 h-4 rounded-full bg-white shadow-sm transform transition-transform duration-300 mx-0.5"></div>
                    </div>
                  </div>
                </div>
                <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <div class="flex items-center space-x-3">
                    <img src="/assets/canzi.png" alt="Canzi" class="h-6 w-6 object-contain" />
                    <div class="space-y-1">
                      <span class="text-sm font-medium">{{ canziDiscount ? 'Canzi Commande' : 'Canzi' }}</span>
                      <div class="flex items-center">
                        <input type="number" v-model="canziDiscountPercentage" class="w-12 text-xs border border-gray-300 rounded p-1 mr-1" min="0" max="100" step="1" />
                        <span class="text-xs text-gray-500">%</span>
                      </div>
                    </div>
                  </div>
                  <div @click="toggleCanziDiscount" class="cursor-pointer">
                    <div :class="['w-10 h-5 rounded-full flex items-center transition-colors duration-300', canziDiscount ? 'bg-green-500 justify-end' : 'bg-gray-200 justify-start']">
                      <div class="w-4 h-4 rounded-full bg-white shadow-sm transform transition-transform duration-300 mx-0.5"></div>
                    </div>
                  </div>
                </div>
                
                <div class="flex justify-between items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                  <div class="flex items-center space-x-3">
                    <img src="/assets/glovo.png" alt="Glovo" class="h-6 w-6 object-contain" />
                    <div class="space-y-1">
                      <span class="text-sm font-medium">{{ glovoDiscount ? 'Glovo Commande' : 'Glovo' }}</span>
                      <div class="flex items-center">
                        <input type="number" v-model="glovoDiscountPercentage" class="w-12 text-xs border border-gray-300 rounded p-1 mr-1" min="0" max="100" step="1" />
                        <span class="text-xs text-gray-500">%</span>
                      </div>
                    </div>
                  </div>
                  <div @click="toggleGlovoDiscount" class="cursor-pointer">
                    <div :class="['w-10 h-5 rounded-full flex items-center transition-colors duration-300', glovoDiscount ? 'bg-green-500 justify-end' : 'bg-gray-200 justify-start']">
                      <div class="w-4 h-4 rounded-full bg-white shadow-sm transform transition-transform duration-300 mx-0.5"></div>
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
                <div v-if="canziDiscount" class="flex justify-between text-sm text-green-600">
                  <span class="text-gray-500">{{ canziDiscount ? 'Canzi Commande' : 'Canzi' }} - Remise ({{canziDiscountPercentage}}%)</span>
                  <span>-{{ formatPrice(canziDiscountAmount) }}</span>
                </div>
                <div v-if="glovoDiscount" class="flex justify-between text-sm text-green-600">
                  <span class="text-gray-500">{{ glovoDiscount ? 'Glovo Commande' : 'Glovo' }} - Remise ({{glovoDiscountPercentage}}%)</span>
                  <span>-{{ formatPrice(glovoDiscountAmount) }}</span>
                </div>
                <div v-if="customDiscount" class="flex justify-between text-sm text-green-600">
                  <span class="text-gray-500">Custom Discount ({{customDiscountPercentage}}%)</span>
                  <span>-{{ formatPrice(customDiscountAmount) }}</span>
                </div>
                <div class="border-t border-gray-100 pt-3 flex justify-between text-base">
                  <span class="font-semibold">Total</span>
                  <span class="font-bold">{{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment -->
          <div class="bg-white rounded-xl text-black shadow-xs p-5 border border-gray-100">
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
                    <span class="text-gray-500">DH</span>
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
                    {{ amount }} DH
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
    <div v-if="showConfirmModal" class="fixed inset-0 z-50 transition-opacity duration-300 ease-out">
      <!-- Backdrop -->
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-all duration-300"></div>
      <!-- Modal Container -->
      <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.15)] max-w-md w-full transform transition-all duration-300 scale-100 opacity-100">
        <div class="p-8 relative">
          <!-- Close button -->
          <button
            @click="showConfirmModal = false"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 transition-colors"
          >
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <h3 class="text-xl font-semibold text-gray-900 mb-6">Confirm Order</h3>
          <div class="space-y-4">
            <div class="border-b border-gray-100 pb-4">
              <h4 class="text-sm font-medium text-gray-600 mb-3">Order Items</h4>
              <div v-for="item in cart" :key="item.product.id" class="flex justify-between mb-2">
                <div class="flex justify-between hover:bg-gray-50 -mx-2 p-2 rounded-lg transition-colors w-full">
                  <div class="text-sm text-gray-600">
                    <div>{{ item.product.name }}</div>
                    <div class="text-xs text-gray-500">
                      {{ item.quantity }} x {{ formatPrice(item.product.price) }}
                    </div>
                  </div>
                  <span class="text-sm font-medium">{{ formatPrice(item.product.price * item.quantity) }}</span>
                </div>
              </div>
            </div>
            <div class="space-y-3 pt-2">
              <h4 class="text-sm font-medium text-gray-600 mb-1">Order Summary</h4>
              <div class="bg-gray-50 -mx-2 p-2 rounded-lg">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Subtotal</span>
                  <span class="text-sm font-medium">{{ formatPrice(subtotal) }}</span>
                </div>
              </div>
              <div v-if="canziDiscount" class="flex justify-between text-green-600">
                <span class="text-sm">{{ canziDiscount ? 'Canzi Commande' : 'Canzi' }} - Remise ({{canziDiscountPercentage}}%)</span>
                <span class="text-sm font-medium">-{{ formatPrice(canziDiscountAmount) }}</span>
              </div>
              <div v-if="glovoDiscount" class="flex justify-between text-green-600">
                <span class="text-sm">{{ glovoDiscount ? 'Glovo Commande' : 'Glovo' }} - Remise ({{glovoDiscountPercentage}}%)</span>
                <span class="text-sm font-medium">-{{ formatPrice(glovoDiscountAmount) }}</span>
              </div>
              <div v-if="customDiscount" class="flex justify-between text-green-600">
                <span class="text-sm">Custom Discount ({{customDiscountPercentage}}%)</span>
                <span class="text-sm font-medium">-{{ formatPrice(customDiscountAmount) }}</span>
              </div>
              <div class="flex justify-between text-gray-900 font-medium bg-green-50 -mx-2 p-2 rounded-lg">
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
              class="flex-1 bg-green-600 text-white py-3 px-6 rounded-xl font-medium shadow-sm hover:bg-green-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50"
              :disabled="processing"
            >
              <span class="flex items-center justify-center">
                <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Processing...' : 'Confirm' }}
              </span>
            </button>
            <button
              @click="showConfirmModal = false"
              class="flex-1 bg-white text-gray-700 py-3 px-6 rounded-xl font-medium border shadow-sm hover:bg-gray-50 transition-colors duration-200"
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
        <div class="bg-white rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.15)] max-w-md w-full transform transition-all duration-300 scale-100 opacity-100">
        <div class="p-8 relative">
          <!-- Close button -->
          <button
            @click="closeSuccessModal"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 transition-colors"
          >
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
          <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-6">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-center text-gray-900 mb-4">Order Complete!</h3>
          <div class="border-b border-gray-100 pb-4 mb-4">
            <h4 class="text-sm font-medium text-gray-600 mb-3">Order Details</h4>
            <div v-for="item in orderDetails?.orderItems" :key="item.id" class="flex justify-between mb-2">
              <div class="flex justify-between hover:bg-gray-50 -mx-2 p-2 rounded-lg transition-colors w-full">
                <span class="text-sm text-gray-600">{{ item.product.name }} × {{ item.quantity }}</span>
                <span class="text-sm font-medium">{{ formatPrice(item.price) }}</span>
              </div>
            </div>
          </div>
          <div class="space-y-3 pt-2">
            <h4 class="text-sm font-medium text-gray-600 mb-1">Payment Summary</h4>
            <div class="bg-green-50 -mx-2 p-2 rounded-lg">
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
  class="mt-6 w-full bg-blue-600 text-white py-3 px-6 rounded-xl font-medium shadow-sm hover:bg-blue-700 transition-colors duration-200 mb-3"
>
  <span class="flex items-center justify-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
    </svg>
    Print Receipt
  </span>
</button>
          <button
            @click="closeSuccessModal"
            class="w-full bg-green-600 text-white py-3 px-6 rounded-xl font-medium shadow-sm hover:bg-green-700 transition-colors duration-200"
          >
            New Order
          </button>
        </div>
       </div>
     </div>
   </div>

    <!-- Error Alert -->
    <div v-if="errorMessage" class="fixed bottom-4 right-4 z-40 bg-red-50 border-l-4 border-red-400 p-4 shadow-md rounded-md">
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
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

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
const canziDiscountPercentage = ref(15)
const glovoDiscountPercentage = ref(33)
const customDiscountPercentage = ref(0)
const customDiscount = ref(false)

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
const canziDiscount = ref<boolean>(false)
const glovoDiscount = ref<boolean>(false)

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

const customDiscountAmount = computed(() => {
  if (!customDiscount.value) return 0
  return subtotal.value * (customDiscountPercentage.value / 100)
})

const canziDiscountAmount = computed(() => {
  if (!canziDiscount.value) return 0
  return subtotal.value * (canziDiscountPercentage.value / 100)
})

const glovoDiscountAmount = computed(() => {
  if (!glovoDiscount.value) return 0
  return subtotal.value * (glovoDiscountPercentage.value / 100)
})

const total = computed(() => {
  return subtotal.value - canziDiscountAmount.value - glovoDiscountAmount.value - customDiscountAmount.value
})

const change = computed(() => {
  return amountReceived.value - total.value
})

const canSubmitOrder = computed(() => {
  return cart.value.length > 0 && amountReceived.value >= total.value
})

const activeDiscountType = computed(() => {
  if (canziDiscount.value) return `Canzi (${canziDiscountPercentage.value}%)`
  if (glovoDiscount.value) return `Glovo (${glovoDiscountPercentage.value}%)`
  if (customDiscount.value) return `Custom (${customDiscountPercentage.value}%)`
  return null
})

// Methods
const showError = (message: string) => {
  errorMessage.value = message
  setTimeout(() => {
    errorMessage.value = ''
  }, 5000)
}

const toggleCustomDiscount = () => {
  customDiscount.value = !customDiscount.value
  if (customDiscount.value) {
    canziDiscount.value = false
    glovoDiscount.value = false
  }
}

const toggleCanziDiscount = () => {
  canziDiscount.value = !canziDiscount.value
  if (canziDiscount.value) {
    glovoDiscount.value = false
    customDiscount.value = false
  }
}

const toggleGlovoDiscount = () => {
  glovoDiscount.value = !glovoDiscount.value
  if (glovoDiscount.value) {
    canziDiscount.value = false
    customDiscount.value = false
  }
}

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
            <h3>SNACK SALADAY</h3>
            ${canziDiscount.value ? '<p>Commande By Canzi</p>' : ''}
            ${glovoDiscount.value ? '<p>Commande By Glovo</p>' : ''}
            <p>instagram : @saladay.meeka</p>
            <p>téléphone : 07 00 12 29 54</p>
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
            ${receipt.items.map(item => `
              <div class="item-row">
                <span class="item-name">${item.name}</span>
                <span class="item-qty">${item.quantity}</span>
                <span class="item-price">${formatPrice(item.price)}</span>
              </div>
            `).join('')}
          </div>
          
          <div class="divider"></div>
          
          <div class="summary">
            <div class="item-row">
              <span>Sous-total:</span>
              <span>${formatPrice(receipt.subtotal)}</span>
            </div>
            ${receipt.discount > 0 ? `
            <div class="item-row">
              <span>Remise:</span>
              <span>-${formatPrice(receipt.discount)}</span>
            </div>
            ` : ''}
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
  if (price === undefined) return '$0.00'
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
      discount: canziDiscountAmount.value + glovoDiscountAmount.value + customDiscountAmount.value,
      discount_type: activeDiscountType.value,
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
  canziDiscount.value = false
  glovoDiscount.value = false
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
