<template>
  <div class="stat-card" :class="`stat-card-${color}`">
    <div class="stat-header">
      <div class="stat-label">{{ title }}</div>
    </div>
    <div class="stat-content">
      <div class="stat-main">
        <span class="stat-prefix">{{ label }}</span>
        <span class="stat-value">{{ prefix }}{{ formattedValue }}</span>
      </div>
      <div class="stat-icon" :class="`icon-${icon}`">
        <component :is="iconComponent" />
      </div>
    </div>
    <div class="stat-actions">
      <button class="action-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
          <path
            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
          />
        </svg>
      </button>
      <button class="action-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
          <path
            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
          />
        </svg>
      </button>
      <button class="action-icon view-btn">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
          <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
          <path
            fill-rule="evenodd"
            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatCard',
  props: {
    title: {
      type: String,
      required: true,
    },
    value: {
      type: Number,
      required: true,
    },
    prefix: {
      type: String,
      default: '',
    },
    color: {
      type: String,
      default: 'blue',
      validator: (value) => ['blue', 'green', 'red'].includes(value),
    },
    icon: {
      type: String,
      default: 'package',
    },
  },
  computed: {
    formattedValue() {
      return this.value.toLocaleString()
    },
    label() {
      const labels = {
        blue: 'Product',
        green: 'Value',
        red: 'Customer',
      }
      return labels[this.color] || ''
    },
    iconComponent() {
      const icons = {
        package: 'PackageIcon',
        dollar: 'DollarIcon',
        users: 'UsersIcon',
      }
      return icons[this.icon] || 'PackageIcon'
    },
  },
  components: {
    PackageIcon: {
      template: `
        <svg width="60" height="60" viewBox="0 0 60 60" fill="currentColor" opacity="0.3">
          <path d="M30 5L10 15v20l20 10 20-10V15L30 5zm0 5l15 7.5v15L30 40l-15-7.5v-15L30 10z"/>
        </svg>
      `,
    },
    DollarIcon: {
      template: `
        <svg width="60" height="60" viewBox="0 0 60 60" fill="currentColor" opacity="0.3">
          <circle cx="30" cy="30" r="20" fill="none" stroke="currentColor" stroke-width="3"/>
          <path d="M30 20v20M25 25h7.5c1.38 0 2.5 1.12 2.5 2.5S33.88 30 32.5 30h-5c-1.38 0-2.5 1.12-2.5 2.5S26.12 35 27.5 35H35" stroke="currentColor" stroke-width="2" fill="none"/>
        </svg>
      `,
    },
    UsersIcon: {
      template: `
        <svg width="60" height="60" viewBox="0 0 60 60" fill="currentColor" opacity="0.3">
          <circle cx="25" cy="20" r="7"/>
          <path d="M15 40c0-5.5 4.5-10 10-10s10 4.5 10 10"/>
          <circle cx="40" cy="22" r="6"/>
          <path d="M45 40c0-4.4-3.6-8-8-8"/>
        </svg>
      `,
    },
  },
}
</script>

<style scoped>
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  position: relative;
  overflow: hidden;
}

.stat-card-blue {
  background: linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%);
  color: white;
}

.stat-card-green {
  background: linear-gradient(135deg, #66d9b8 0%, #4dd0a1 100%);
  color: white;
}

.stat-card-red {
  background: linear-gradient(135deg, #ef8080 0%, #f06292 100%);
  color: white;
}

.stat-header {
  margin-bottom: 20px;
}

.stat-label {
  font-size: 16px;
  font-weight: 500;
  opacity: 0.95;
}

.stat-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.stat-main {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.stat-prefix {
  font-size: 14px;
  opacity: 0.9;
}

.stat-value {
  font-size: 42px;
  font-weight: bold;
  line-height: 1;
}

.stat-icon {
  opacity: 0.3;
}

.stat-actions {
  display: flex;
  gap: 10px;
}

.action-icon {
  background: rgba(255, 255, 255, 0.25);
  border: none;
  color: white;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.action-icon:hover {
  background: rgba(255, 255, 255, 0.35);
}

.view-btn {
  margin-left: auto;
}
</style>
