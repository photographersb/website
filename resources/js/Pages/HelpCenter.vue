<template>
  <div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 max-w-4xl">
      <h1 class="text-4xl font-bold mb-8">Help Center</h1>

      <!-- Search Box -->
      <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for help..."
            class="w-full border rounded-lg px-4 py-3 pl-12 focus:outline-none focus:ring-2 focus:ring-burgundy"
          />
          <svg class="w-5 h-5 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <!-- FAQ Categories -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" @click="activeCategory = 'general'">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-burgundy-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-burgundy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold">General Questions</h3>
          </div>
          <p class="text-sm text-gray-600">Common questions about using Photographer SB</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" @click="activeCategory = 'booking'">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-burgundy-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-burgundy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold">Booking & Payments</h3>
          </div>
          <p class="text-sm text-gray-600">Learn about booking process and payments</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" @click="activeCategory = 'photographer'">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-burgundy-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-burgundy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold">For Photographers</h3>
          </div>
          <p class="text-sm text-gray-600">Information for photographers on the platform</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow cursor-pointer" @click="activeCategory = 'account'">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-12 h-12 bg-burgundy-50 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-burgundy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h3 class="text-lg font-bold">Account & Profile</h3>
          </div>
          <p class="text-sm text-gray-600">Manage your account and profile settings</p>
        </div>
      </div>

      <!-- FAQ Accordion -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6">Frequently Asked Questions</h2>

        <div class="space-y-4">
          <!-- General FAQs -->
          <div v-for="(faq, index) in filteredFAQs" :key="index" class="border-b pb-4">
            <button
              @click="toggleFAQ(index)"
              class="w-full text-left flex justify-between items-center py-2 hover:text-burgundy transition-colors"
            >
              <span class="font-medium">{{ faq.question }}</span>
              <svg
                class="w-5 h-5 transition-transform"
                :class="{ 'rotate-180': openFAQ === index }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div v-show="openFAQ === index" class="mt-2 text-gray-600 text-sm">
              {{ faq.answer }}
            </div>
          </div>
        </div>
      </div>

      <!-- Still Need Help -->
      <div class="mt-8 bg-burgundy text-white rounded-lg shadow-lg p-8 text-center">
        <h3 class="text-2xl font-bold mb-4">Still Need Help?</h3>
        <p class="mb-6 opacity-90">Our support team is here to assist you with any questions or concerns.</p>
        <router-link
          to="/contact"
          class="inline-block bg-white text-burgundy px-6 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors"
        >
          Contact Support
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const searchQuery = ref('')
const activeCategory = ref('all')
const openFAQ = ref(null)

const faqs = [
  // General
  { category: 'general', question: 'What is Photographer SB?', answer: 'Photographer SB is Bangladesh\'s leading photography marketplace connecting professional photographers with clients across the country.' },
  { category: 'general', question: 'How do I find a photographer?', answer: 'Simply use our search feature on the homepage. Filter by location, specialty, budget, and style to find photographers that match your needs.' },
  { category: 'general', question: 'Is Photographer SB free to use?', answer: 'Yes! Browsing photographers and sending inquiries is completely free for clients. Photographers pay a small commission only on completed bookings.' },
  
  // Booking
  { category: 'booking', question: 'How do I book a photographer?', answer: 'Browse photographers, select one you like, and click "Send Inquiry". Fill in your event details and budget. The photographer will respond with a quote. Once you agree, confirm the booking and make payment.' },
  { category: 'booking', question: 'What payment methods are accepted?', answer: 'We accept all major credit/debit cards, bKash, Nagad, and Rocket. All payments are processed securely through encrypted gateways.' },
  { category: 'booking', question: 'Can I cancel or reschedule a booking?', answer: 'Yes, cancellation and rescheduling policies depend on the photographer\'s terms. Please review these before confirming your booking. Contact the photographer directly for any changes.' },
  { category: 'booking', question: 'When will I receive my photos?', answer: 'Delivery timelines vary by photographer and package. Typical turnaround is 2-4 weeks for edited photos. Check with your photographer for specific timelines.' },
  
  // Photographer
  { category: 'photographer', question: 'How do I join as a photographer?', answer: 'Click "Join as Photographer" in the menu, complete the registration form, upload your portfolio and documents for verification. Once approved, you can start receiving bookings.' },
  { category: 'photographer', question: 'What are the commission fees?', answer: 'We charge a competitive 10-15% commission on completed bookings. There are no upfront fees or monthly subscriptions.' },
  { category: 'photographer', question: 'How do I get paid?', answer: 'Payments are transferred to your registered bank account or mobile wallet within 3-5 business days after project completion and client confirmation.' },
  { category: 'photographer', question: 'Can I set my own prices?', answer: 'Absolutely! You have full control over your pricing, packages, and service offerings. We recommend competitive pricing based on your experience and location.' },
  
  // Account
  { category: 'account', question: 'How do I create an account?', answer: 'Click "Sign Up" in the navigation menu. Choose your account type (Client or Photographer), fill in your details, and verify your email address.' },
  { category: 'account', question: 'I forgot my password. What should I do?', answer: 'Click "Forgot Password" on the login page. Enter your email address and we\'ll send you a password reset link.' },
  { category: 'account', question: 'How do I update my profile?', answer: 'Log in and go to your dashboard. Click on "Profile Settings" to update your information, photos, and preferences.' },
  { category: 'account', question: 'Is my personal information secure?', answer: 'Yes! We use industry-standard encryption and security measures to protect your data. We never share your information with third parties without your consent.' },
]

const filteredFAQs = computed(() => {
  let filtered = faqs
  
  if (activeCategory.value !== 'all') {
    filtered = filtered.filter(faq => faq.category === activeCategory.value)
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(faq =>
      faq.question.toLowerCase().includes(query) ||
      faq.answer.toLowerCase().includes(query)
    )
  }
  
  return filtered
})

const toggleFAQ = (index) => {
  openFAQ.value = openFAQ.value === index ? null : index
}
</script>
