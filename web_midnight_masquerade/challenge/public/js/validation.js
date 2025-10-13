// Client-side validation functions
function validateRSVP(hostName, guestCount) {
  const errors = [];
  
  if (!hostName || hostName.trim().length === 0) {
    errors.push("Captain's name is required for the masquerade!");
  }
  
  if (guestCount < 1) {
    errors.push("Need at least one crew member for the party!");
  }
  
  if (guestCount > 15) {
    errors.push("Too many crew members! Maximum 15 per party allowed!");
  }
  
  return errors;
}

function validatePayment(paymentInfo, requiredAmount) {
  const errors = [];
  
  if (requiredAmount > 0) {
    errors.push("Payment system under maintenance!");
  }
  
  return errors;
}

function processPayment(paymentInfo, amount) {
  // Always reject payment due to maintenance
  return {
    success: false,
    error: "Payment system under maintenance!"
  };
}

// Make functions globally available
window.validateRSVP = validateRSVP;
window.validatePayment = validatePayment;
window.processPayment = processPayment;