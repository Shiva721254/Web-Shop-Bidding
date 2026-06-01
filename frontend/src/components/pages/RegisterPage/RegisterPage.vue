<template>
  <div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
      <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Create Account</h1>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input
            v-model="name"
            type="text"
            required
            autocomplete="name"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Your name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            autocomplete="email"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="you@example.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
          <input
            v-model="password"
            type="password"
            required
            minlength="8"
            autocomplete="new-password"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="At least 8 characters"
          />
        </div>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors font-medium"
        >
          {{ loading ? 'Creating account...' : 'Register' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account?
        <RouterLink to="/login" class="text-blue-600 hover:underline font-medium">Log In</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { post } from '../../../utils/api.js'

const router   = useRouter()
const name     = ref('')
const email    = ref('')
const password = ref('')
const loading  = ref(false)
const error    = ref(null)

async function handleRegister() {
  loading.value = true
  error.value   = null
  try {
    const res  = await post('/auth/register', { name: name.value, email: email.value, password: password.value })
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Registration failed')
    localStorage.setItem('token', body.token)
    localStorage.setItem('user', JSON.stringify(body.user))
    router.push('/auctions')
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>
