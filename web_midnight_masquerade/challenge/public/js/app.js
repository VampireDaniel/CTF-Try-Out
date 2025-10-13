// Main Application Logic
class HalloweenRSVP {
  constructor() {
    // Constants
    this.MAX_FREE_GUESTS = 5;
    this.COIN_COST = 25;
    
    // State
    this.guestCount = 1;
    this.hostName = '';
    this.isSubmitting = false;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.init());
    } else {
      this.init();
    }
  }
  
  init() {
    console.log('🎃 Initializing Halloween RSVP App...');
    
    // Get DOM elements
    this.getElements();
    
    // Check if critical elements exist
    if (!this.validateElements()) {
      console.error('❌ Critical elements missing!');
      return;
    }
    
    // Setup event listeners
    this.setupEventListeners();
    
    // Initialize display
    this.updateGuestDisplay();
    
    console.log('✅ Halloween RSVP App initialized successfully!');
  }
  
  getElements() {
    // Form elements
    this.hostNameInput = document.getElementById('host-name');
    this.guestCountDisplay = document.getElementById('guest-count');
    this.guestLabel = document.getElementById('guest-label');
    this.incrementBtn = document.getElementById('increment-guests');
    this.decrementBtn = document.getElementById('decrement-guests');
    this.submitBtn = document.getElementById('submit-btn');
    this.halloweenForm = document.getElementById('halloween-form');
    
    // Display elements
    this.costInfo = document.getElementById('cost-info');
    this.totalCostDisplay = document.getElementById('total-cost');
    this.extraCountDisplay = document.getElementById('extra-count');
    this.errorDisplay = document.getElementById('error-display');
    this.rsvpFormDiv = document.getElementById('rsvp-form');
    this.successScreen = document.getElementById('success-screen');
    this.rankEmoji = document.getElementById('rank-emoji');
    this.rankText = document.getElementById('rank-text');
    
    // Modal elements
    this.paymentModal = document.getElementById('payment-modal');
    this.closeModalBtn = document.getElementById('close-modal');
    this.cancelPaymentBtn = document.getElementById('cancel-payment');
    this.processPaymentBtn = document.getElementById('process-payment');
    this.modalCost = document.getElementById('modal-cost');
  }
  
  validateElements() {
    const required = [
      this.hostNameInput,
      this.incrementBtn,
      this.decrementBtn,
      this.submitBtn,
      this.halloweenForm
    ];
    
    return required.every(element => {
      if (!element) {
        console.error('Missing element:', element);
        return false;
      }
      return true;
    });
  }
  
  setupEventListeners() {
    // Guest counter buttons
    this.incrementBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.incrementGuests();
    });
    
    this.decrementBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      this.decrementGuests();
    });
    
    // Host name input
    this.hostNameInput.addEventListener('input', (e) => {
      this.hostName = e.target.value;
      console.log('🎭 Captain name updated:', this.hostName);
    });
    
    // Form submission
    this.halloweenForm.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleFormSubmit();
    });
    
    // Modal controls
    this.closeModalBtn.addEventListener('click', () => {
      this.hidePaymentModal();
    });
    
    this.cancelPaymentBtn.addEventListener('click', () => {
      this.hidePaymentModal();
    });
    
    // Click outside modal to close
    this.paymentModal.addEventListener('click', (e) => {
      if (e.target === this.paymentModal) {
        this.hidePaymentModal();
      }
    });
  }
  
  incrementGuests() {
    if (this.guestCount < 15) {
      this.guestCount++;
      this.updateGuestDisplay();
      console.log('🔼 Crew count increased to:', this.guestCount);
    }
  }
  
  decrementGuests() {
    if (this.guestCount > 1) {
      this.guestCount--;
      this.updateGuestDisplay();
      console.log('🔽 Crew count decreased to:', this.guestCount);
    }
  }
  
  updateGuestDisplay() {
    // Update count display
    this.guestCountDisplay.textContent = this.guestCount;
    this.guestLabel.textContent = this.guestCount === 1 ? 'Matey' : 'Mateys';
    
    // Update buttons
    this.decrementBtn.disabled = this.guestCount <= 1;
    this.incrementBtn.disabled = this.guestCount >= 15;
    
    // Update cost info
    const extraGuests = Math.max(0, this.guestCount - this.MAX_FREE_GUESTS);
    const totalCost = extraGuests * this.COIN_COST;
    
    if (extraGuests > 0) {
      this.costInfo.classList.remove('hidden');
      this.totalCostDisplay.textContent = totalCost;
      this.extraCountDisplay.textContent = extraGuests;
      this.modalCost.textContent = totalCost;
    } else {
      this.costInfo.classList.add('hidden');
    }
    
    // Update guest rank
    window.updateGuestRank(this.guestCount, this.rankText, this.rankEmoji);
  }
  
  async handleFormSubmit() {
    console.log('🎃 Form submitted!');
    
    if (this.isSubmitting) return;
    
    // Clear previous errors
    window.showErrors([], this.errorDisplay);
    
    // Client-side validation
    const validationErrors = window.validateRSVP(this.hostName, this.guestCount);
    if (validationErrors.length > 0) {
      window.showErrors(validationErrors, this.errorDisplay);
      return;
    }
    
    const extraGuests = Math.max(0, this.guestCount - this.MAX_FREE_GUESTS);
    
    // Check if payment is required
    if (extraGuests > 0) {
      this.showPaymentModal();
      return;
    }
    
    // Proceed with free RSVP
    await this.submitRSVP();
  }
  
  showPaymentModal() {
    this.paymentModal.classList.remove('hidden');
    console.log('💰 Payment modal shown');
  }
  
  hidePaymentModal() {
    this.paymentModal.classList.add('hidden');
    console.log('💰 Payment modal hidden');
  }
  
  async submitRSVP() {
    this.isSubmitting = true;
    this.submitBtn.innerHTML = `
      <span class="text-2xl">🎃</span>
      <div class="flex-1 flex items-center justify-center gap-2">
        <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Preparing invitation...</span>
      </div>
      <span class="text-2xl">☠️</span>
    `;
    this.submitBtn.disabled = true;
    
    try {
      const extraGuests = Math.max(0, this.guestCount - this.MAX_FREE_GUESTS);
      
      const response = await fetch('/api/rsvp', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          hostName: this.hostName.trim(),
          attendeeCount: this.guestCount,
          depositPaid: extraGuests === 0
        }),
      });

      const result = await response.json();
      
      if (result.success) {
        window.showSuccessScreen({
          hostName: this.hostName,
          guestCount: this.guestCount,
          extraGuests: extraGuests,
          totalCoins: extraGuests * this.COIN_COST,
          rsvpId: result.rsvp.id
        }, this.successScreen, this.rsvpFormDiv);
      } else {
        window.showErrors(['Failed to register for the masquerade!'], this.errorDisplay);
      }
    } catch (err) {
      console.error('❌ RSVP Error:', err);
      window.showErrors(['Castle communication is down, try again later!'], this.errorDisplay);
    } finally {
      this.isSubmitting = false;
      this.submitBtn.innerHTML = `
        <span class="text-2xl">🎃</span>
        <span class="flex-1">Join the Adventure!</span>
        <span class="text-2xl">☠️</span>
      `;
      this.submitBtn.disabled = false;
    }
  }
}

// Initialize the app
new HalloweenRSVP();