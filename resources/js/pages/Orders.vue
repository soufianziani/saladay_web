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

    <main class="max-w-7xl mx-auto px-4 text-black sm:px-6 lg:px-8 py-6">
      <!-- Filters -->
      <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
            <div class="relative">
              <input
                type="datetime-local"
                v-model="dateFrom"
                class="block w-full rounded-md border-gray-300 pl-3 pr-10 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
              />
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
            <div class="relative">
              <input
                type="datetime-local"
                v-model="dateTo"
                class="block w-full rounded-md border-gray-300 pl-3 pr-10 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"
                :min="dateFrom"
              />
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>
          <div class="flex items-end">
            <button
              @click="printFilteredReceipt"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
              :disabled="!dateFrom || !dateTo"
            >
              Print Filtered Receipt
            </button>
          </div>
        </div>
      </div>

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
              <tr v-for="order in filteredOrders" :key="order.id">
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
import { ref, computed } from 'vue'
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

// State
const showDetailsModal = ref(false)
const selectedOrder = ref<Order | null>(null)
const dateFrom = ref<string>(new Date().toISOString().slice(0, 16))
const dateTo = ref<string>(new Date().toISOString().slice(0, 16))

// Computed property for filtered orders
const filteredOrders = computed(() => {
  if (!dateFrom.value && !dateTo.value) {
    return props.orders
  }

  return props.orders.filter((order: Order) => {
    const orderDate = new Date(order.created_at)
    const fromDate = dateFrom.value ? new Date(dateFrom.value + ':00Z') : null
    const toDate = dateTo.value ? new Date(dateTo.value + ':00Z') : null

    const isAfterFrom = !fromDate || orderDate >= fromDate
    const isBeforeTo = !toDate || orderDate <= toDate

    return isAfterFrom && isBeforeTo
  })
})

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

const showOrderDetails = (order: Order) => {
  selectedOrder.value = order
  showDetailsModal.value = true
}

const printReceipt = async (order: Order) => {
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
                        (item: { name: string; quantity: number; price: number }) => `
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
                  <span>Mnt reçu:</span>
                  <span>${formatPrice(receipt.amount_received)}</span>
                </div>
                <div class="item-row">
                  <span>Mnt rendue:</span>
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
    }
  } catch (error) {
    console.error('Failed to print receipt:', error)
  }
}

const printFilteredReceipt = async () => {
  if (!dateFrom.value || !dateTo.value) return

  try {
    const response = await axios.post('/customer-order/filtered-receipt', {
      dateFrom: dateFrom.value,
      dateTo: dateTo.value
    })

    if (response.data.success) {
      const receipt = response.data.receipt

      const receiptWindow = window.open('', '_blank', 'width=350,height=600,toolbar=no,scrollbars=no')
      if (receiptWindow) {
        receiptWindow.document.write(`
          <!DOCTYPE html>
          <html>
          <head>
            <title>Filtered Orders Receipt</title>
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
                <p>Filtered Orders Report</p>
                <p>From: ${new Date(receipt.date_range.from).toLocaleDateString('fr-FR')} ${new Date(receipt.date_range.from).toLocaleTimeString('fr-FR')}</p>
                <p>To: ${new Date(receipt.date_range.to).toLocaleDateString('fr-FR')}   ${new Date(receipt.date_range.to).toLocaleTimeString('fr-FR')}</p>
                <p>Total Orders: ${receipt.order_count}</p>
              </div>

              <div class="divider"></div>

              <div class="items">
                <div class="item-row">
                  <span class="item-name"><strong>Product</strong></span>
                  <span class="item-qty"><strong>Qty</strong></span>
                  <span class="item-price"><strong>Total</strong></span>
                </div>
                <div class="divider"></div>
                ${receipt.products.map(item => `
                  <div class="item-row">
                    <span class="item-name">${item.name}</span>
                    <span class="item-qty">${item.quantity}</span>
                    <span class="item-price">${formatPrice(item.total)}</span>
                  </div>
                `).join('')}
              </div>

              <div class="divider"></div>

              <div class="summary">
                <div class="item-row total-row">
                  <span>TOTAL:</span>
                  <span>${formatPrice(receipt.total_amount)}</span>
                </div>
              </div>
              <div class="summary">
                <div class="item-row total-row">
                  <span>TOTAL Discount:</span>
                  <span>${formatPrice(receipt.total_discount)}</span>
                </div>
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
        `)
        receiptWindow.document.close()
      }
    }
  } catch (error) {
    console.error('Failed to print filtered receipt:', error)
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
