<template>
  <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-burgundy to-burgundy-dark text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
          Become a Sponsor
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-100">
          Partner with Photographer SB to support photography excellence
        </p>
        <a
          href="#contact"
          class="inline-flex items-center px-8 py-3 border-2 border-white rounded-lg text-lg font-semibold hover:bg-white hover:text-burgundy transition-colors"
        >
          Get Started
          <svg
            class="ml-2 w-5 h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 8l4 4m0 0l-4 4m4-4H3"
            />
          </svg>
        </a>
      </div>
    </div>

    <!-- Why Sponsor Section -->
    <div class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Why Sponsor Us?
          </h2>
          <p class="text-lg text-gray-600 max-w-3xl mx-auto">
            Join leading brands in supporting the photography community while gaining valuable exposure
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="bg-burgundy bg-opacity-10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg
                class="w-8 h-8 text-burgundy"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">
              Brand Visibility
            </h3>
            <p class="text-gray-600">
              Reach thousands of photographers and photography enthusiasts across Bangladesh
            </p>
          </div>

          <div class="text-center p-6">
            <div class="bg-burgundy bg-opacity-10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg
                class="w-8 h-8 text-burgundy"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">
              Community Support
            </h3>
            <p class="text-gray-600">
              Help nurture talent and support the growth of Bangladesh's photography industry
            </p>
          </div>

          <div class="text-center p-6">
            <div class="bg-burgundy bg-opacity-10 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg
                class="w-8 h-8 text-burgundy"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                />
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-2">
              Competition Presence
            </h3>
            <p class="text-gray-600">
              Feature your brand in photography competitions and events throughout the year
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Current Sponsors Section -->
    <div
      v-if="sponsors.length > 0"
      class="py-16 bg-gray-50"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Our Valued Sponsors
          </h2>
          <p class="text-lg text-gray-600">
            Thank you to our amazing sponsors who make everything possible
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <div
            v-for="sponsor in sponsors"
            :key="sponsor.id" 
            class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-center hover:shadow-md transition-shadow"
          >
            <a
              v-if="sponsor.website"
              :href="sponsor.website"
              target="_blank"
              class="block"
            >
              <img
                v-if="sponsor.logo"
                :src="sponsor.logo"
                :alt="sponsor.name" 
                class="h-20 w-full object-contain"
              >
              <span
                v-else
                class="text-lg font-semibold text-gray-900"
              >{{ sponsor.name }}</span>
            </a>
            <div v-else>
              <img
                v-if="sponsor.logo"
                :src="sponsor.logo"
                :alt="sponsor.name" 
                class="h-20 w-full object-contain"
              >
              <span
                v-else
                class="text-lg font-semibold text-gray-900"
              >{{ sponsor.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sponsorship Benefits -->
    <div class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Sponsorship Benefits
          </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Logo Placement
              </h3>
              <p class="text-gray-600">
                Featured on our website and competition pages
              </p>
            </div>
          </div>

          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Social Media Mentions
              </h3>
              <p class="text-gray-600">
                Regular shoutouts across our social channels
              </p>
            </div>
          </div>

          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Event Recognition
              </h3>
              <p class="text-gray-600">
                Acknowledged at all photography events and competitions
              </p>
            </div>
          </div>

          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Networking Opportunities
              </h3>
              <p class="text-gray-600">
                Connect with photographers and industry professionals
              </p>
            </div>
          </div>

          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Email Marketing
              </h3>
              <p class="text-gray-600">
                Featured in our newsletters to thousands of subscribers
              </p>
            </div>
          </div>

          <div class="flex items-start">
            <svg
              class="w-6 h-6 text-green-500 mr-3 flex-shrink-0 mt-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900 mb-1">
                Custom Packages
              </h3>
              <p class="text-gray-600">
                Flexible sponsorship tiers to match your budget
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Section -->
    <div
      id="contact"
      class="py-16 bg-gray-50"
    >
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
          <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
              Ready to Partner With Us?
            </h2>
            <p class="text-lg text-gray-600">
              Get in touch to discuss sponsorship opportunities
            </p>
          </div>

          <form
            class="space-y-6"
            @submit.prevent="submitInquiry"
          >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                <input
                  v-model="form.company_name"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                <input
                  v-model="form.contact_person"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                >
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                >
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
              <textarea
                v-model="form.message"
                rows="5"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-burgundy focus:border-transparent"
                placeholder="Tell us about your sponsorship interests..."
              />
            </div>

            <div class="text-center">
              <button
                type="submit"
                :disabled="submitting"
                class="inline-flex items-center px-8 py-3 bg-burgundy text-white rounded-lg font-semibold hover:bg-burgundy-dark transition-colors disabled:opacity-50"
              >
                <span v-if="!submitting">Send Inquiry</span>
                <span v-else>Sending...</span>
              </button>
            </div>
          </form>

          <div
            v-if="submitMessage"
            :class="submitSuccess ? 'text-green-600' : 'text-red-600'" 
            class="mt-4 text-center font-medium"
          >
            {{ submitMessage }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const sponsors = ref([]);
const form = ref({
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    message: ''
});
const submitting = ref(false);
const submitMessage = ref('');
const submitSuccess = ref(false);

onMounted(() => {
    fetchSponsors();
});

const fetchSponsors = async () => {
    try {
        const response = await api.get('/sponsors');
        sponsors.value = response.data;
    } catch (error) {
        console.error('Error fetching sponsors:', error);
    }
};

const submitInquiry = async () => {
    submitting.value = true;
    submitMessage.value = '';
    
    try {
        await api.post('/sponsor-inquiry', form.value);
        submitSuccess.value = true;
        submitMessage.value = 'Thank you! We will contact you shortly.';
        
        // Reset form
        form.value = {
            company_name: '',
            contact_person: '',
            email: '',
            phone: '',
            message: ''
        };
    } catch (error) {
        submitSuccess.value = false;
        submitMessage.value = 'Error sending inquiry. Please try again.';
        console.error('Error submitting inquiry:', error);
    } finally {
        submitting.value = false;
    }
};
</script>
