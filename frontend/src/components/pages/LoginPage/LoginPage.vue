<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Log In</h1>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input v-model="email" type="email" required autocomplete="email"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="you@example.com" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input v-model="password" type="password" required autocomplete="current-password"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="••••••••" />
        </div>
        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
        <button type="submit" :disabled="loading"
          class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors font-medium">
          {{ loading ? 'Logging in...' : 'Log In' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Don't have an account?
        <RouterLink to="/register" class="text-blue-600 hover:underline font-medium">Register</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'

const router    = useRouter()
const route     = useRoute()
const authStore = useAuthStore()

const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref(null)

async function handleLogin() {
  loading.value = true
  error.value   = null
  try {
    await authStore.login(email.value, password.value)
    router.push(route.query.redirect || '/auctions')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
