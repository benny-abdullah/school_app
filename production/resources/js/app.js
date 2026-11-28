// Star Kids SDN Kebonagung - Application Client Assets
import 'bootstrap-icons/font/bootstrap-icons.css';
import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';
import Chart from 'chart.js/auto';
import Alpine from 'alpinejs';

// Initialize Alpine.js for Mobile Responsive Drawer & Micro-interactions
window.Alpine = Alpine;
Alpine.start();

// Expose Libraries on Global Window for Livewire & Alpine.js
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeSupportedFormats = Html5QrcodeSupportedFormats;
window.Chart = Chart;

// Web Audio API Synthesizer (Audio Feedback for Barcode Scan & Actions)
window.playAudioFeedback = (type) => {
  try {
    const AudioCtx = window.AudioContext || window.webkitAudioContext;
    if (!AudioCtx) return;
    const ctx = new AudioCtx();

    if (type === 'beep_880' || type === 'beep_success') {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sine';
      osc.frequency.setValueAtTime(880, ctx.currentTime);
      gain.gain.setValueAtTime(0.12, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.22);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.22);
    } else if (type === 'buzz_180' || type === 'buzz_warning') {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(180, ctx.currentTime);
      gain.gain.setValueAtTime(0.18, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.35);
    } else if (type === 'chime_pulang') {
      const notes = [523.25, 659.25, 783.99]; // C5, E5, G5
      notes.forEach((freq, idx) => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + idx * 0.12);
        gain.gain.setValueAtTime(0.12, ctx.currentTime + idx * 0.12);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + idx * 0.12 + 0.4);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(ctx.currentTime + idx * 0.12);
        osc.stop(ctx.currentTime + idx * 0.12 + 0.4);
      });
    }
  } catch (e) {
    console.warn('Audio feedback error:', e);
  }
};

// Global Toast Dispatcher
window.showToast = (message, type = 'info') => {
  window.dispatchEvent(new CustomEvent('toast-notify', { detail: { message, type } }));
};
