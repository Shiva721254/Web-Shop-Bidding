import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const items = ref(JSON.parse(localStorage.getItem('cart') || '[]'))

  const total     = computed(() => items.value.reduce((sum, i) => sum + i.price * i.quantity, 0))
  const itemCount = computed(() => items.value.reduce((sum, i) => sum + i.quantity, 0))

  function addItem(product) {
    const existing = items.value.find(i => i.productId === product.id)
    if (existing) {
      existing.quantity++
    } else {
      items.value.push({
        productId: product.id,
        title:     product.title,
        price:     product.price,
        quantity:  1,
      })
    }
    _persist()
  }

  function removeItem(productId) {
    items.value = items.value.filter(i => i.productId !== productId)
    _persist()
  }

  function updateQuantity(productId, quantity) {
    if (quantity <= 0) return removeItem(productId)
    const item = items.value.find(i => i.productId === productId)
    if (item) { item.quantity = quantity; _persist() }
  }

  function clear() {
    items.value = []
    _persist()
  }

  function _persist() {
    localStorage.setItem('cart', JSON.stringify(items.value))
  }

  return { items, total, itemCount, addItem, removeItem, updateQuantity, clear }
})
