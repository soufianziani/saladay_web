<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Orders History</h1>
        <Link
          href="/customer-order"
          class="flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          New Order
        </Link>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="order in props.orders" :key="order.id">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.created_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatPrice(order.total) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="order.discount" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    -{{ formatPrice(order.discount) }}
                  </span>
                  <span v-else class="text-sm text-gray-500">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ formatPrice(order.mnt_recu) }}</div>
                  <div class="text-sm text-gray-500">Change: {{ formatPrice(order.mnt_rendu) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button @click="showOrderDetails(order)" class="text-indigo-600 hover:text-indigo-900">Details</button>
                  <button @click="printReceipt(order)" class="text-blue-600 hover:text-blue-900">Receipt</button>
                  <button @click="deleteOrder(order)" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Order Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showDetailsModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Order Details #{{ selectedOrder?.id }}</h3>
                
                <div class="mb-6">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Products</h4>
                  <div class="space-y-3">
                    <div v-for="item in selectedOrder?.orderItems" :key="item.id" class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                      <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200">
                        <img 
                          v-if="item.product.image_url" 
                          :src="item.product.image_url" 
                          :alt="item.product.name"
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center">
                          <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>
                      </div>
                      <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900">{{ item.product.name }}</h3>
                        <p class="text-sm text-gray-500">{{ formatPrice(item.price) }} × {{ item.quantity }}</p>
                      </div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ formatPrice(item.price * item.quantity) }}
                      </div>
                    </div>
                  </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                  <dl class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Subtotal</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatPrice(selectedOrder?.total + (selectedOrder?.discount || 0)) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Discount</dt>
                      <dd class="mt-1 text-sm text-green-600">-{{ formatPrice(selectedOrder?.discount || 0) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Amount Received</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatPrice(selectedOrder?.mnt_recu) }}</dd>
                    </div>
                    <div>
                      <dt class="text-sm font-medium text-gray-500">Change</dt>
                      <dd class="mt-1 text-sm text-gray-900">{{ formatPrice(selectedOrder?.mnt_rendu) }}</dd>
                    </div>
                    <div class="col-span-2">
                      <dt class="text-sm font-medium text-gray-900">Total</dt>
                      <dd class="mt-1 text-sm font-medium text-gray-900">{{ formatPrice(selectedOrder?.total) }}</dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              @click="printReceipt(selectedOrder)"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Print Receipt
            </button>
            <button
              @click="showDetailsModal = false"
              class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import axios from 'axios'

interface OrderItem {
  id: number
  product: {
    id: number
    name: string
    image_url?: string
  }
  quantity: number
  price: number
  subtotal: number
}

interface Order {
  id: number
  total: number
  discount?: number
  mnt_recu: number
  mnt_rendu: number
  created_at: string
  orderItems: OrderItem[]
}

const props = defineProps<{
  orders: Order[]
}>()

const showDetailsModal = ref(false)
const selectedOrder = ref<Order | null>(null)

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatPrice = (price: number | string | undefined) => {
  if (price === undefined) return '0.00 DH'
  const numPrice = typeof price === 'string' ? parseFloat(price) : price
  return `${numPrice.toFixed(2)} DH`
}

const showOrderDetails = (order: any) => {
  selectedOrder.value = order
  showDetailsModal.value = true
}

const printReceipt = async (order: any) => {
  try {
    const response = await axios.get(`/customer-order/receipt/${order.id}`)
    if (response.data.success) {
      const receipt = response.data.receipt
      
      const receiptWindow = window.open('', '_blank', 'width=350,height=600,toolbar=no,scrollbars=no')
      if (receiptWindow) {
        receiptWindow.document.write(`
          <!DOCTYPE html>
          <html>
          <head>
            <title>Reçu #${receipt.order_id}</title>
            <style>
              body { 
                margin: 0; 
                padding: 20px;
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
              .items {
                width: 100%;
                margin: 10px 0;
              }
              .item-row {
                display: flex;
                justify-content: space-between;
                margin: 3px 0;
              }
              .divider {
                border-top: 1px dashed #000;
                margin: 10px 0;
              }
              .total-row {
                font-weight: bold;
                margin: 5px 0;
              }
              @media print {
                body { padding: 0; }
              }
            </style>
          </head>
          <body>
            <div class="receipt">
              <div class="header">
                <h2>SNACK SALADAY</h2>
                ${receipt.discount_type?.includes('Canzi') ? '<p class="text-green-600 font-medium">Commande By Canzi</p>' : ''}
                ${receipt.discount_type?.includes('Glovo') ? '<p class="text-green-600 font-medium">Commande By Glovo</p>' : ''}
                <p>instagram : @saladay.meeka</p>
                <p>téléphone : 07 00 12 29 54</p>
                <p>Caissier: ZAKARIA</p>
                <p>Date: ${receipt.date}</p>
                <p>Reçu #: REC-${receipt.order_id}</p>
              </div>
              
              <div class="divider"></div>
              
              <div class="items">
                ${receipt.items.map(item => `
                  <div class="item-row">
                    <span>${item.name}</span>
                    <span>${item.quantity} × ${formatPrice(item.price)}</span>
                  </div>
                  <div class="item-row">
                    <span></span>
                    <span>${formatPrice(item.subtotal)}</span>
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
                <div class="divider"></div>
                <div class="item-row total-row">
                  <span>TOTAL:</span>
                  <span>${formatPrice(receipt.total)}</span>
                </div>
              </div>
              
              <div class="divider"></div>
              
              <div class="footer">
                <p>Merci de votre visite!</p>
              </div>
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
        `)
        receiptWindow.document.close()
      }
    }
  } catch (error) {
    console.error('Failed to print receipt:', error)
  }
}

const deleteOrder = async (order: Order) => {
  if (!confirm('Are you sure you want to delete this order?')) return
  
  try {
    await axios.delete(`/customer-order/${order.id}`)
    router.reload()
  } catch (error) {
    console.error('Failed to delete order:', error)
  }
}
</script>