// UI Helper Functions
function showErrors(errors, errorDisplay) {
  if (errors.length > 0) {
    errorDisplay.innerHTML = errors.map(error => `
      <div class="flex items-center gap-3 mb-2 last:mb-0">
        <span class="text-red-400">⚠️</span>
        <p class="text-red-200 font-bold">${error}</p>
      </div>
    `).join('');
    errorDisplay.classList.remove('hidden');
    errorDisplay.classList.add('payment-error');
    
    // Remove shake animation after it completes
    setTimeout(() => {
      errorDisplay.classList.remove('payment-error');
    }, 500);
  } else {
    errorDisplay.classList.add('hidden');
  }
}

function showSuccessScreen(data, successScreen, rsvpFormDiv) {
  const ticketClass = data.extraGuests > 0 ? 'flag-ticket' : 'ticket';
  const ticketEmoji = data.extraGuests > 0 ? '🎭' : '🎫';
  const ticketTitle = data.extraGuests > 0 ? 'VIP Masquerade Pass!' : 'Masquerade Invitation!';
  
  // Rick Roll QR Code URL
  const rickRollUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
  const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(rickRollUrl)}`;
  
  successScreen.innerHTML = `
    <div class="${ticketClass} rounded-3xl shadow-2xl p-12 text-center text-white">
      <div class="text-8xl mb-6 animate-bounce">${ticketEmoji}</div>
      <h2 class="text-4xl font-bold mb-4">${ticketTitle}</h2>
      <p class="text-xl mb-6">
        Captain ${data.hostName} and ${data.guestCount} mysterious crew member${data.guestCount !== 1 ? 's' : ''}
      </p>
      ${data.extraGuests > 0 ? `
        <p class="text-lg mb-4">
          Extra Crew: ${data.extraGuests} × 25 doubloons = ${data.totalCoins} doubloons
        </p>
        <p class="text-sm mb-4 text-orange-200">
          Payment required at the castle gates
        </p>
      ` : ''}
      
      <!-- QR Code Section -->
      <div class="bg-white bg-opacity-20 rounded-2xl p-6 mb-6 backdrop-blur-sm">
        <p class="text-lg font-bold mb-3 text-yellow-300">🎫 Your Digital Ticket</p>
        <div class="flex justify-center mb-3">
          <img src="${qrCodeUrl}" alt="Ticket QR Code" class="rounded-lg border-4 border-white shadow-lg" />
        </div>
        <p class="text-sm text-purple-200">Scan this QR code at the castle entrance</p>
        <p class="text-xs text-purple-300 mt-1">Ticket ID: #${data.rsvpId.toString().padStart(4, '0')}</p>
      </div>
      
      <div class="flex justify-center gap-4 text-4xl animate-pulse mb-4">
        🎃 🦇 👻 ☠️ 🌙
      </div>
      <p class="font-bold text-lg">
        The masquerade begins at midnight! 🌙
      </p>
      <p class="text-sm mt-2 text-purple-200">
        Ravencroft Castle awaits your crew's arrival...
      </p>
    </div>
  `;
  
  rsvpFormDiv.classList.add('hidden');
  successScreen.classList.remove('hidden');
}

function updateGuestRank(guestCount, rankText, rankEmoji) {
  let rank, emoji;
  
  if (guestCount >= 10) {
    rank = 'Pirate Captain! 🏴‍☠️';
    emoji = '🏴‍☠️';
  } else if (guestCount >= 7) {
    rank = 'First Mate! ⚓';
    emoji = '⚓';
  } else if (guestCount >= 5) {
    rank = 'Quartermaster! ⚔️';
    emoji = '⚔️';
  } else if (guestCount >= 3) {
    rank = 'Crew Leader! 👥';
    emoji = '👥';
  } else {
    rank = 'Solo Pirate ☠️';
    emoji = '☠️';
  }
  
  rankText.textContent = rank;
  rankEmoji.textContent = emoji;
}

// Make functions globally available
window.showErrors = showErrors;
window.showSuccessScreen = showSuccessScreen;
window.updateGuestRank = updateGuestRank;