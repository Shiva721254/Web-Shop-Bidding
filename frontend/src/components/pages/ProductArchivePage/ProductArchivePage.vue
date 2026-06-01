<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Products</h1>
      <select
        v-model="typeFilter"
        class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
      >
        <option value="">All Types</option>
        <option value="buy_now">Buy Now</option>
        <option value="auction">Auction</option>
      </select>
    </div>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-500 text-lg">Loading products...</p>
    </div>

    <div v-else-if="error" class="flex flex-col items-center py-24 gap-4">
      <p class="text-red-600">{{ error }}</p>
      <button @click="fetchProducts" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
        Try Again
      </button>
    </div>

    <div v-else>
      <div v-if="products.length === 0" class="text-center py-16 text-gray-500">
        No products found.
      </div>
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="product in products"
          :key="product.id"
          class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex justify-between items-start mb-3">
            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
              {{ product.category }}
            </span>
            <span :class="['px-2 py-1 text-xs font-semibold rounded-full', product.type === 'buy_now' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700']">
              {{ product.type === 'buy_now' ? 'Buy Now' : 'Auction' }}
            </span>
          </div>
          <h2 class="text-lg font-bold text-gray-900 mb-2">{{ product.title }}</h2>
          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ product.description }}</p>
          <div class="flex justify-between items-center">
            <span class="text-xl font-bold text-blue-700">
              €{{ (product.price ?? product.startingPrice)?.toFixed(2) }}
            </span>
            <RouterLink
              v-if="product.type === 'auction'"
              :to="{ name: 'AuctionDetail', params: { id: product.id } }"
              class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium"
            >
              Bid Now
            </RouterLink>
            <button
              v-else
              class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors font-medium"
            >
              Add to Cart
            </button>
          </div>
        </div>
      </div>

      <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-10">
        <button
          v-for="p in totalPages"
          :key="p"
          @click="page = p; fetchProducts()"
          :class="['px-4 py-2 rounded-lg text-sm font-medium transition-colors', p === page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50']"
        >
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { get } from '../../../utils/api.js'

const products   = ref([])
const loading    = ref(true)
const error      = ref(null)
const typeFilter = ref('')
const page       = ref(1)
const total      = ref(0)
const limit      = 9
const totalPages = ref(0)

async function fetchProducts() {
  loading.value = true
  error.value   = null
  try {
    const params = new URLSearchParams({ page: page.value, limit })
    if (typeFilter.value) params.set('type', typeFilter.value)
    const res  = await get(`/products?${params}`)
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Failed to fetch products')
    products.value   = body.data ?? body
    total.value      = body.total ?? products.value.length
    totalPages.value = Math.ceil(total.value / limit)
  } catch (err) {
    error.value    = err.message
    products.value = []
  } finally {
    loading.value = false
  }
}

watch(typeFilter, () => { page.value = 1; fetchProducts() })
onMounted(fetchProducts)
</script>
