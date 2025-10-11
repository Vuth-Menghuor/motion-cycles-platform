<template>
  <div class="footer-container">
    <!-- Newsletter Section - Absolutely positioned -->
    <div class="footer-newsletter-section">
      <div class="footer-newsletter">
        <div class="newsletter">
          <div class="newsletter-text">
            <h3 class="newsletter-title">
              Get updates on new gear and <br />
              discounts
            </h3>
            <p class="newsletter-subtitle">
              Stay up to date on new and exciting things available to you soon!
            </p>
          </div>
          <div class="newsletter-form">
            <input
              type="email"
              placeholder="Enter your email"
              class="email-input"
              v-model="email"
              @keyup.enter="subscribeNewsletter"
            />
            <small>
              We care about your data in our
              <a href="#" class="privacy-link">privacy policy</a>
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
      <!-- Footer Links -->
      <div class="footer-content">
        <!-- Logo / About -->
        <div class="footer-section">
          <h4 class="footer-logo">MOTION CYCLE</h4>
          <p class="footer-text">
            Design amazing digital experiences that create more happy in the world.
          </p>
        </div>

        <!-- Support -->
        <div class="footer-section">
          <h4 class="footer-title">Support</h4>
          <ul>
            <li><a href="#" @click="handleLinkClick('cancellation')">Cancellation Policy</a></li>
            <li><a href="#" @click="handleLinkClick('register')">Register Bike</a></li>
            <li><a href="#" @click="handleLinkClick('return')">Return Policy</a></li>
            <li><a href="#" @click="handleLinkClick('shipping')">Shipping Info</a></li>
            <li><a href="#" @click="handleLinkClick('support')">Support</a></li>
            <li><a href="#" @click="handleLinkClick('warranty')">Warranty</a></li>
          </ul>
        </div>

        <!-- Electric Bike -->
        <div class="footer-section">
          <h4 class="footer-title">Electric Bike</h4>
          <ul>
            <li><a href="#" @click="handleLinkClick('all-bikes')">All Electric Bike</a></li>
            <li><a href="#" @click="handleLinkClick('accessories')">Accessories</a></li>
            <li><a href="#" @click="handleLinkClick('gift-cards')">Gift Cards</a></li>
            <li><a href="#" @click="handleLinkClick('promotions')">Promotions</a></li>
          </ul>
        </div>

        <!-- Company -->
        <div class="footer-section">
          <h4 class="footer-title">Company</h4>
          <ul>
            <li><a href="#" @click="handleLinkClick('about')">About us</a></li>
            <li><a href="#" @click="handleLinkClick('contact')">Contact us</a></li>
            <li><a href="#" @click="handleLinkClick('blog')">Blog</a></li>
            <li><a href="#" @click="handleLinkClick('videos')">Videos</a></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="footer-section">
          <h4 class="footer-title">Contact</h4>
          <p>+1 (907) 555-0101</p>
          <p>curtis.weaver@example.com</p>
        </div>
      </div>

      <!-- Bottom Section -->
      <div class="footer-bottom">
        <p>© 2077 ElectroBike. All rights reserved.</p>
        <div class="social-icons">
          <a href="#" @click="handleSocialClick('twitter')">
            <Icon icon="mdi:twitter" />
          </a>
          <a href="#" @click="handleSocialClick('linkedin')">
            <Icon icon="mdi:linkedin" />
          </a>
          <a href="#" @click="handleSocialClick('facebook')">
            <Icon icon="ic:baseline-facebook" />
          </a>
          <a href="#" @click="handleSocialClick('github')">
            <Icon icon="mdi:github" />
          </a>
          <a href="#" @click="handleSocialClick('instagram')">
            <Icon icon="mdi:instagram" />
          </a>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

// Define the props for the footer component
defineProps({
  showNewsletter: { type: Boolean, default: true },
  companyName: { type: String, default: 'MOTION CYCLE' },
  companyDescription: {
    type: String,
    default: 'Design amazing digital experiences that create more happy in the world.',
  },
})

// Define the events the component can emit
const emit = defineEmits(['newsletter-subscribe', 'link-click', 'social-click'])

// Reactive data for email input
const email = ref('')

// Function to subscribe to newsletter
const subscribeNewsletter = () => {
  if (email.value.trim()) {
    // Check if email is not empty
    emit('newsletter-subscribe', email.value)
    email.value = '' // Clear input after subscribe
  }
}

// Function to handle footer link clicks
const handleLinkClick = (linkType) => emit('link-click', linkType)

// Function to handle social media link clicks
const handleSocialClick = (platform) => emit('social-click', platform)
</script>

<style scoped>
/* Footer */
.footer {
  margin-top: 8rem;
}

/* Footer Newsletter Section */
.footer-newsletter-section {
  position: absolute;
  left: 50%;
  width: 80%;
  transform: translateX(-50%);
  z-index: 10;
  top: -160px;
}

.footer-container {
  position: relative;
  background: #23272f;
  color: #fff;
  padding: 4rem 6rem;
  margin-top: 12rem;
}

.footer-newsletter {
  display: flex;
  align-items: center;
  font-family: 'Poppins', sans-serif;
  background: #14c9c9;
  height: 240px;
  width: 80%;
  color: #fff;
  border-radius: 26px;
  padding: 2rem 10rem;
  text-align: center;
  margin-bottom: 4rem;
}

/* Newsletter */
.newsletter {
  display: flex;
  justify-content: space-between;
  width: 90%;
}

.newsletter-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.newsletter-title {
  text-align: start;
  font-size: 2.2rem;
  font-weight: 600;
  margin: 0;
}

.footer-logo {
  font-size: 1.4rem;
  font-weight: bold;
  margin: 20px 0;
}

.newsletter-subtitle {
  margin: 1.5rem 0 1rem;
  font-size: 1.1rem;
}

.newsletter-form {
  display: flex;
  flex-direction: column;
  align-items: start;
}

.newsletter-form small {
  font-size: 15px;
  margin-top: 0.5rem;
}

.newsletter-form small a {
  color: white;
  text-decoration: underline;
}

.email-input {
  padding: 0.6rem 1.5rem;
  border: 1px solid #fff;
  color: white;
  background-color: transparent;
  border-radius: 14px;
  margin-bottom: 0.5rem;
  font-size: 15px;
  height: 30px;
  width: 360px;
  outline: none;
}

::placeholder {
  color: white;
  opacity: 1;
}

::-ms-input-placeholder {
  color: white;
}

/* Footer Content */
.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 2rem;
}

.footer-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #98a2b3;
}

.footer-text {
  font-size: 0.9rem;
  margin: 24px 0;
  color: #98a2b3;
}

.footer-section {
  font-family: 'Poppins', sans-serif;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin: 0.8rem 0;
}

.footer-section ul li a {
  color: #eaecf0;
  text-decoration: none;
  transition: all 0.3s ease;
  cursor: pointer;
}

.footer-section ul li a:hover {
  color: #14c9c9;
}

/* Footer Bottom */
.footer-bottom {
  font-family: 'Poppins', sans-serif;
  border-top: 1px solid #374151;
  color: #98a2b3;
  margin-top: 2rem;
  padding-top: 1rem;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  font-size: 1rem;
}

.social-icons a {
  color: #9ca3af;
  margin-left: 1.2rem;
  font-size: 2rem;
  text-decoration: none;
  transition: all 0.3s ease;
  cursor: pointer;
}

.social-icons a:hover {
  color: #fff;
}

/* Responsive Design */
@media (max-width: 768px) {
  .footer-container {
    padding: 2rem 1rem;
  }

  .footer-newsletter {
    padding: 1rem 2rem;
    height: auto;
    width: 95%;
  }

  .newsletter {
    flex-direction: column;
    text-align: center;
  }

  .newsletter-text {
    align-items: center;
    margin-bottom: 1rem;
  }

  .newsletter-title {
    text-align: center;
    font-size: 1.5rem;
  }

  .email-input {
    width: 100%;
    max-width: 300px;
  }

  .footer-bottom {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }

  .social-icons {
    justify-content: center;
  }

  .social-icons a {
    margin: 0 0.5rem;
  }
}
</style>
