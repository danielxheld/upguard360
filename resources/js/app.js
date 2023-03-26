import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
