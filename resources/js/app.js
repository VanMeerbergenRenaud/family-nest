import './bootstrap';
import '../../vendor/masmerise/livewire-toaster/resources/js';

import ui from '@alpinejs/ui';
Alpine.plugin(ui);

import Chart from 'chart.js/auto';
window.Chart = Chart;
