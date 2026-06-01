import config from '../config.js'

function buildApiUrl(endpoint) {
  const baseUrl = config.apiDomain.replace(/\/$/, '')
  const path = endpoint.replace(/^\//, '')
  return `${baseUrl}/${path}`
}

function authHeaders() {
  const token = localStorage.getItem('token')
  return token ? { Authorization: `Bearer ${token}` } : {}
}

export async function get(endpoint, options = {}) {
  return fetch(buildApiUrl(endpoint), {
    method: 'GET',
    headers: { 'Content-Type': 'application/json', ...authHeaders(), ...options.headers },
    ...options,
  })
}

export async function post(endpoint, data, options = {}) {
  return fetch(buildApiUrl(endpoint), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...authHeaders(), ...options.headers },
    body: JSON.stringify(data),
    ...options,
  })
}

export async function put(endpoint, data, options = {}) {
  return fetch(buildApiUrl(endpoint), {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', ...authHeaders(), ...options.headers },
    body: JSON.stringify(data),
    ...options,
  })
}

export async function del(endpoint, options = {}) {
  return fetch(buildApiUrl(endpoint), {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json', ...authHeaders(), ...options.headers },
    ...options,
  })
}

export function getApiUrl(endpoint) {
  return buildApiUrl(endpoint)
}
