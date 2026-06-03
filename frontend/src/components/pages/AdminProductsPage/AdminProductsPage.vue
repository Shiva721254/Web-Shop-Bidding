<template>
  <div class="page-container">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="section-heading">Products</h1>
        <p class="text-gray-500 mt-1">{{ total }} product{{ total !== 1 ? 's' : '' }} total</p>
      </div>
      <button class="btn btn-primary" @click="openForm()">+ Add Product</button>
    </div>

    <div v-if="showForm" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showForm = false">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-5">{{ editing ? 'Edit Product' : 'New Product' }}</h2>
        <form @submit.prevent="saveProduct" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input v-model="form.title" required class="input" placeholder="Product title" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="input resize-none" placeholder="Product description"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <input v-model="form.category" class="input" placeholder="e.g. Electronics" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
              <select v-model="form.type" class="input">
                <option value="buy_now">Buy Now</option>
                <option value="auction">Auction</option>
              </select>
            </div>
          </div>
          <div v-if="form.type === 'buy_now'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Price (€) *</label>
            <input v-model.number="form.price" type="number" step="0.01" min="0.01" required class="input" placeholder="0.00" />
          </div>
          <div v-if="form.type === 'auction'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Starting Price (€) *</label>
            <input v-model.number="form.startingPrice" type="number" step="0.01" min="0.01" required class="input" placeholder="0.00" />
          </div>
          <div v-if="form.type === 'auction'">
            <label class="block text-sm font-medium text-gray-700 mb-1">Auction Ends At *</label>
            <input v-model="form.endsAt" type="datetime-local" required class="input" />
          </div>
          <p v-if="formError" class="text-red-600 text-sm">{{ formError }}</p>
          <div class="flex gap-3 pt-2">
            <button type="submit" :disabled="saving" class="btn btn-primary flex-1">
              {{ saving ? 'Saving...' : (editing ? 'Update Product' : 'Create Product') }}
            </button>
            <button type="button" class="btn btn-outline" @click="showForm = false">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-400 text-lg">Loading products...</p>
    </div>

    <div v-else class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">ID</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Title</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Type</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Price</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="products.length === 0">
              <td colspan="6" class="px-4 py-10 text-center text-gray-400">No products found.</td>
            </tr>
            <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 text-gray-400">#{{ product.id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 max-w-xs truncate">{{ product.title }}</td>
              <td class="px-4 py-3 text-gray-500">{{ product.category || '—' }}</td>
              <td class="px-4 py-3">
                <span :class="['badge', product.type === 'buy_now' ? 'badge-green' : 'badge-blue']">
                  {{ product.type === 'buy_now' ? 'Buy Now' : 'Auction' }}
                </span>
              </td>
              <td class="px-4 py-3 font-semibold text-gray-800">
                {{ formatPrice(product.price ?? product.startingPrice) }}
              </td>
              <td class="px-4 py-3">
                <div class="flex gap-2">
                  <button class="btn btn-outline text-xs py-1 px-3" @click="openForm(product)">Edit</button>
                  <button class="btn text-xs py-1 px-3 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200"
                    @click="deleteProduct(product)">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="totalPages > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <button v-for="p in totalPages" :key="p" @click="page = p; fetchProducts()"
          :class="['px-3 py-1 rounded text-sm font-medium', p === page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50']">
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { get, post, put, del } from '../../../utils/api.js'

const products   = ref([])
const loading    = ref(true)
const showForm   = ref(false)
const saving     = ref(false)
const editing    = ref(null)
const formError  = ref(null)
const page       = ref(1)
const total      = ref(0)
const totalPages = ref(0)
const limit      = 15

const emptyForm = () => ({ title: '', description: '', category: '', type: 'buy_now', price: null, startingPrice: null, endsAt: '' })
const form      = ref(emptyForm())

const formatPrice = (v) => v != null ? new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v) : '—'

function openForm(product = null) {
  editing.value   = product
  formError.value = null
  form.value = product
    ? { title: product.title, description: product.description || '', category: product.category || '',
        type: product.type, price: product.price, startingPrice: product.startingPrice, endsAt: '' }
    : emptyForm()
  showForm.value = true
}

async function fetchProducts() {
  loading.value = true
  try {
    const res  = await get(`/products?page=${page.value}&limit=${limit}`)
    const body = await res.json()
    products.value   = body.data ?? body
    total.value      = body.total ?? products.value.length
    totalPages.value = Math.ceil(total.value / limit)
  } finally {
    loading.value = false
  }
}

async function saveProduct() {
  saving.value    = true
  formError.value = null
  try {
    const payload = {
      title:         form.value.title,
      description:   form.value.description,
      category:      form.value.category,
      type:          form.value.type,
      price:         form.value.type === 'buy_now' ? form.value.price         : null,
      startingPrice: form.value.type === 'auction' ? form.value.startingPrice : null,
      endsAt:        form.value.type === 'auction' ? form.value.endsAt        : null,
    }
    let res, body
    if (editing.value) {
      res  = await put(`/products/${editing.value.id}`, payload)
      body = await res.json()
    } else {
      res  = await post('/products', payload)
      body = await res.json()
    }
    if (!res.ok) throw new Error(body.error || 'Save failed')
    showForm.value = false
    await fetchProducts()
  } catch (err) {
    formError.value = err.message
  } finally {
    saving.value = false
  }
}

async function deleteProduct(product) {
  if (!confirm(`Delete "${product.title}"? This cannot be undone.`)) return
  await del(`/products/${product.id}`)
  await fetchProducts()
}

onMounted(fetchProducts)
</script>
