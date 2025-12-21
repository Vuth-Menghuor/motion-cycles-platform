<template>
  <div class="help-support-page">
    <div class="help-header">
      <h1 class="help-title">Help & Support</h1>
      <p class="help-subtitle">Find answers to common questions and get support</p>
    </div>

    <div class="help-content">
      <!-- FAQ Section -->
      <div class="help-section">
        <h2 class="section-title">Frequently Asked Questions</h2>

        <div class="faq-list">
          <div class="faq-item">
            <div class="faq-question" @click="toggleFaq(0)">
              <h3>How do I place an order?</h3>
              <Icon
                :icon="activeFaq === 0 ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                class="faq-icon"
              />
            </div>
            <div class="faq-answer" :class="{ 'faq-answer--open': activeFaq === 0 }">
              <p>
                To place an order, browse our products, add items to your cart, and proceed to
                checkout. You'll need to provide delivery information and payment details.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" @click="toggleFaq(1)">
              <h3>What payment methods do you accept?</h3>
              <Icon
                :icon="activeFaq === 1 ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                class="faq-icon"
              />
            </div>
            <div class="faq-answer" :class="{ 'faq-answer--open': activeFaq === 1 }">
              <p>
                We accept various payment methods including credit/debit cards, bank transfers, and
                digital wallets through our secure payment gateway.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" @click="toggleFaq(2)">
              <h3>How can I track my order?</h3>
              <Icon
                :icon="activeFaq === 2 ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                class="faq-icon"
              />
            </div>
            <div class="faq-answer" :class="{ 'faq-answer--open': activeFaq === 2 }">
              <p>
                You can track your order status by logging into your account and visiting the Orders
                section. We'll also send you email updates about your order progress.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" @click="toggleFaq(3)">
              <h3>What is your return policy?</h3>
              <Icon
                :icon="activeFaq === 3 ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                class="faq-icon"
              />
            </div>
            <div class="faq-answer" :class="{ 'faq-answer--open': activeFaq === 3 }">
              <p>
                We offer a 30-day return policy for unused items in their original packaging. Please
                contact our support team to initiate a return request.
              </p>
            </div>
          </div>

          <div class="faq-item">
            <div class="faq-question" @click="toggleFaq(4)">
              <h3>How do I add items to favorites?</h3>
              <Icon
                :icon="activeFaq === 4 ? 'mdi:chevron-up' : 'mdi:chevron-down'"
                class="faq-icon"
              />
            </div>
            <div class="faq-answer" :class="{ 'faq-answer--open': activeFaq === 4 }">
              <p>
                Click the heart icon on any product card to add it to your favorites. You can view
                all your favorite items in the Favorites section.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Section -->
      <div class="help-section">
        <h2 class="section-title">Contact Us</h2>

        <div class="contact-info">
          <div class="contact-item">
            <Icon icon="mdi:email" class="contact-icon" />
            <div class="contact-details">
              <h4>Email Support</h4>
              <p>support@motioncycle.com</p>
              <p>We typically respond within 24 hours</p>
            </div>
          </div>

          <div class="contact-item">
            <Icon icon="mdi:phone" class="contact-icon" />
            <div class="contact-details">
              <h4>Phone Support</h4>
              <p>+855 12 345 678</p>
              <p>Mon-Fri: 9:00 AM - 6:00 PM</p>
            </div>
          </div>

          <div class="contact-item">
            <Icon icon="mdi:map-marker" class="contact-icon" />
            <div class="contact-details">
              <h4>Visit Our Store</h4>
              <p>123 Bicycle Street, Phnom Penh, Cambodia</p>
              <p>Daily: 8:00 AM - 8:00 PM</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="help-section">
        <h2 class="section-title">Quick Actions</h2>

        <div class="quick-actions">
          <button class="action-btn" @click="goToOrders">
            <Icon icon="mdi:clipboard-list" class="action-icon" />
            <span>View My Orders</span>
          </button>

          <button class="action-btn" @click="goToFavorites">
            <Icon icon="mdi:heart" class="action-icon" />
            <span>View Favorites</span>
          </button>

          <button class="action-btn" @click="goToCart">
            <Icon icon="mdi:cart" class="action-icon" />
            <span>Go to Cart</span>
          </button>

          <button class="action-btn" @click="goToHome">
            <Icon icon="mdi:home" class="action-icon" />
            <span>Browse Products</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

// Reactive data for FAQ accordion
const activeFaq = ref(null)

// Get router instance
const router = useRouter()

// Function to toggle FAQ items
const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index
}

// Navigation functions
const goToOrders = () => router.push('/orders')
const goToFavorites = () => router.push('/favorites')
const goToCart = () => router.push('/checkout/cart')
const goToHome = () => router.push('/home')
</script>

<style scoped>
.help-support-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
  min-height: 80vh;
}

.help-header {
  text-align: center;
  margin-bottom: 3rem;
  padding-top: 2rem;
}

.help-title {
  font-family: 'Poppins', sans-serif;
  font-size: 2.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.help-subtitle {
  font-family: 'Poppins', sans-serif;
  font-size: 1.1rem;
  color: #6b7280;
  margin: 0;
}

.help-content {
  display: flex;
  flex-direction: column;
  gap: 3rem;
}

.help-section {
  background: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  border: 1px solid #e5e7eb;
}

.section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 1.5rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #3491fa;
}

/* FAQ Styles */
.faq-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.faq-item {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.faq-question {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: #f9fafb;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.faq-question:hover {
  background: #f3f4f6;
}

.faq-question h3 {
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  color: #1f2937;
  margin: 0;
}

.faq-icon {
  font-size: 1.25rem;
  color: #6b7280;
  transition: transform 0.2s ease;
}

.faq-answer {
  max-height: 0;
  overflow: hidden;
  transition:
    max-height 0.3s ease,
    padding 0.3s ease;
  background: #ffffff;
}

.faq-answer--open {
  max-height: 200px;
  padding: 0 1.5rem 1.5rem;
}

.faq-answer p {
  font-family: 'Poppins', sans-serif;
  font-size: 0.95rem;
  color: #4b5563;
  line-height: 1.6;
  margin: 0;
}

/* Contact Info Styles */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1.5rem;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.contact-icon {
  font-size: 1.5rem;
  color: #3491fa;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

.contact-details h4 {
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 0.25rem 0;
}

.contact-details p {
  font-family: 'Poppins', sans-serif;
  font-size: 0.9rem;
  color: #6b7280;
  margin: 0.25rem 0;
  line-height: 1.4;
}

/* Quick Actions Styles */
.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1.25rem;
  background: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: 'Poppins', sans-serif;
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
}

.action-btn:hover {
  border-color: #3491fa;
  background: #eff6ff;
  color: #3491fa;
}

.action-icon {
  font-size: 1.25rem;
  color: inherit;
}

/* Responsive Design */
@media (max-width: 768px) {
  .help-support-page {
    padding: 1rem;
  }

  .help-title {
    font-size: 2rem;
  }

  .help-content {
    gap: 2rem;
  }

  .help-section {
    padding: 1.5rem;
  }

  .quick-actions {
    grid-template-columns: 1fr;
  }

  .contact-item {
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }

  .contact-icon {
    align-self: center;
  }
}

@media (max-width: 480px) {
  .help-header {
    margin-bottom: 2rem;
    padding-top: 1rem;
  }

  .help-title {
    font-size: 1.75rem;
  }

  .help-subtitle {
    font-size: 1rem;
  }

  .section-title {
    font-size: 1.25rem;
  }

  .faq-question {
    padding: 1rem;
  }

  .faq-question h3 {
    font-size: 0.9rem;
  }
}
</style>
