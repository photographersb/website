<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-burgundy via-[#8E0E3F] to-[#6F112D] text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2" />
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2" />
      </div>

      <div class="container mx-auto px-4 py-16 md:py-24 relative z-10 text-center">
        <div class="inline-block mb-4 px-6 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20">
          <p class="text-sm md:text-base font-medium flex items-center gap-2 justify-center">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path d="M10 3.5a1.5 1.5 0 013 0V4a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-.5a1.5 1.5 0 000 3h.5a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-.5a1.5 1.5 0 00-3 0v.5a1 1 0 01-1 1H6a1 1 0 01-1-1v-3a1 1 0 00-1-1h-.5a1.5 1.5 0 010-3H4a1 1 0 001-1V6a1 1 0 011-1h3a1 1 0 001-1v-.5z" />
            </svg>
            A Project by <a
              href="https://somogrobangladesh.com/"
              target="_blank"
              rel="noopener"
              class="underline hover:text-white/80 transition-colors"
            >Somogro Bangladesh</a>
          </p>
        </div>
        <h1 class="text-4xl md:text-6xl font-bold mb-4 tracking-tight">
          Get In Touch
        </h1>
        <p class="text-lg md:text-xl text-gray-100 max-w-3xl mx-auto">
          Have questions? We'd love to hear from you.
        </p>
      </div>
    </section>

    <div class="container mx-auto px-4 py-12 max-w-6xl">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contact Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-xl p-8 md:p-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">
              Send Us a Message
            </h2>
            
            <form
              class="space-y-6"
              @submit.prevent="submitForm"
            >
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium mb-2">Full Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy"
                  placeholder="Your name"
                >
              </div>

              <!-- Email -->
              <div>
                <label class="block text-sm font-medium mb-2">Email Address *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy"
                  placeholder="your@email.com"
                >
              </div>

              <!-- Phone -->
              <div>
                <label class="block text-sm font-medium mb-2">Phone Number</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy"
                  placeholder="+880 1XXXXXXXXX"
                >
              </div>

              <!-- Subject -->
              <div>
                <label class="block text-sm font-medium mb-2">Subject *</label>
                <select
                  v-model="form.subject"
                  required
                  class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy"
                >
                  <option value="">
                    Select a subject
                  </option>
                  <option value="general">
                    General Inquiry
                  </option>
                  <option value="support">
                    Technical Support
                  </option>
                  <option value="billing">
                    Billing Question
                  </option>
                  <option value="photographer">
                    Photographer Registration
                  </option>
                  <option value="partnership">
                    Business Partnership
                  </option>
                  <option value="feedback">
                    Feedback
                  </option>
                </select>
              </div>

              <!-- Message -->
              <div>
                <label class="block text-sm font-medium mb-2">Message *</label>
                <textarea
                  v-model="form.message"
                  required
                  rows="6"
                  class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-burgundy"
                  placeholder="Tell us how we can help you..."
                />
              </div>

              <!-- Submit Button -->
              <button
                type="submit"
                :disabled="submitting"
                class="w-full bg-burgundy text-white py-3 rounded-lg hover:bg-[#6F112D] transition-colors disabled:opacity-50 font-medium"
              >
                {{ submitting ? 'Sending...' : 'Send Message' }}
              </button>

              <!-- Success/Error Message -->
              <div
                v-if="message"
                :class="`p-4 rounded-lg ${error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'}`"
              >
                {{ message }}
              </div>
            </form>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="space-y-6">
          <!-- Contact Details -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border-t-4 border-burgundy">
            <h3 class="text-xl font-bold mb-6 text-gray-900">
              Contact Information
            </h3>
            
            <div class="space-y-5">
              <!-- Email -->
              <div class="flex gap-4 items-start group">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-burgundy to-red-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 mb-1">
                    Email
                  </p>
                  <a
                    href="mailto:support@photographersb.com"
                    class="text-sm text-burgundy hover:text-burgundy-dark font-medium hover:underline transition-colors"
                  >support@photographersb.com</a>
                </div>
              </div>

              <!-- Phone / WhatsApp -->
              <div class="flex gap-4 items-start group">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    ><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 mb-1">
                    Phone / WhatsApp
                  </p>
                  <a
                    href="tel:+8801767300900"
                    class="text-sm text-gray-700 hover:text-burgundy font-medium block mb-1 transition-colors"
                  >01767-300900</a>
                  <a
                    href="https://wa.me/8801767300900"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 text-xs text-white bg-green-500 hover:bg-green-600 px-3 py-1.5 rounded-full font-medium transition-colors"
                  >
                    <svg
                      class="w-3.5 h-3.5"
                      fill="currentColor"
                      viewBox="0 0 24 24"
                    ><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                    Chat on WhatsApp
                  </a>
                </div>
              </div>

              <!-- Address -->
              <div class="flex gap-4 items-start group">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 mb-1">
                    Address
                  </p>
                  <p class="text-sm text-gray-600">
                    Dhaka, Bangladesh
                  </p>
                </div>
              </div>

              <!-- Business Hours -->
              <div class="flex gap-4 items-start group">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                    <svg
                      class="w-6 h-6 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 mb-1">
                    Business Hours
                  </p>
                  <p class="text-sm text-gray-600 leading-relaxed">
                    Sun - Thu: 9 AM - 6 PM<br>Fri - Sat: Closed
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Social Links -->
          <div class="bg-gradient-to-br from-burgundy to-red-800 rounded-2xl shadow-xl p-6 text-white">
            <h3 class="text-xl font-bold mb-5">
              Follow Us
            </h3>
            <div class="space-y-3">
              <a
                href="https://www.facebook.com/thephotographersbd"
                target="_blank"
                class="flex items-center gap-3 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all group backdrop-blur-sm"
              >
                <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  ><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-white mb-0.5">Facebook</p>
                  <p class="text-xs text-white/80">@thephotographersbd</p>
                </div>
              </a>
              
              <a
                href="https://www.instagram.com/thephotographersbd"
                target="_blank"
                class="flex items-center gap-3 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all group backdrop-blur-sm"
              >
                <div class="w-11 h-11 bg-gradient-to-br from-purple-600 via-pink-600 to-orange-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  ><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" /></svg>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-white mb-0.5">Instagram</p>
                  <p class="text-xs text-white/80">@thephotographersbd</p>
                </div>
              </a>
              
              <a
                href="https://wa.me/8801767300900"
                target="_blank"
                class="flex items-center gap-3 p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all group backdrop-blur-sm"
              >
                <div class="w-11 h-11 bg-green-500 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                  <svg
                    class="w-6 h-6 text-white"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  ><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" /></svg>
                </div>
                <div class="flex-1">
                  <p class="font-bold text-white mb-0.5">WhatsApp</p>
                  <p class="text-xs text-white/80">01767-300900</p>
                </div>
              </a>
            </div>
          </div>

          <!-- FAQ Link -->
          <div class="bg-white rounded-2xl shadow-xl p-6 border-2 border-burgundy/20">
            <div class="flex items-center gap-3 mb-3">
              <div class="w-10 h-10 bg-burgundy/10 rounded-xl flex items-center justify-center">
                <svg
                  class="w-6 h-6 text-burgundy"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900">
                Need Quick Help?
              </h3>
            </div>
            <p class="text-sm mb-4 text-gray-600 leading-relaxed">
              Check our FAQ section for instant answers to common questions about services, pricing, and more.
            </p>
            <router-link
              to="/help"
              class="inline-flex items-center gap-2 bg-burgundy text-white px-5 py-2.5 rounded-xl font-medium hover:bg-burgundy-dark transition-colors shadow-lg hover:shadow-xl"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"
                />
              </svg>
              Visit Help Center
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../api'

const form = ref({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const submitting = ref(false)
const message = ref('')
const error = ref(false)

const submitForm = async () => {
  submitting.value = true
  error.value = false
  message.value = ''

  try {
    await api.post('/contact', {
      name: form.value.name,
      email: form.value.email,
      phone: form.value.phone,
      subject: form.value.subject,
      message: form.value.message
    })
    
    message.value = 'Thank you for contacting us! We\'ll get back to you within 24 hours.'
    error.value = false
    
    // Reset form
    form.value = {
      name: '',
      email: '',
      phone: '',
      subject: '',
      message: ''
    }
  } catch (err) {
    console.error('Contact form error:', err)
    message.value = 'Something went wrong. Please try again.'
    error.value = true
  } finally {
    submitting.value = false
  }
}
</script>
