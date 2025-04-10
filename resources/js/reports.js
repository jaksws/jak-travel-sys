/**
 * تحسينات نظام التقارير والإحصائيات
 */
class ReportingSystem {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        // إنشاء المخططات إذا وجدت العناصر المناسبة
        this.initRevenueChart();
        this.initServicePerformanceChart();
        this.initSubagentPerformanceChart();
        this.initPaymentMethodsChart();
        this.initComparisonChart();
    }

    initRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        // استخراج البيانات من البيانات المضمنة
        const chartData = JSON.parse(ctx.dataset.chartData || '{}');
        
        this.charts.revenue = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels || [],
                datasets: [{
                    label: chartData.label || 'الإيرادات',
                    data: chartData.data || [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.3,
                    borderWidth: 3,
                    pointRadius: 3,
                    fill: true
                }]
            },
            options: this.getChartOptions('الإيرادات الشهرية')
        });
    }

    // ... المزيد من المخططات الرسومية

    getChartOptions(title) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Tajawal' }
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: { family: 'Tajawal', size: 16 }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: { family: 'Tajawal' },
                    bodyFont: { family: 'Tajawal' },
                    rtl: true,
                    textDirection: 'rtl'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: { family: 'Tajawal' }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { family: 'Tajawal' }
                    }
                }
            }
        };
    }
}

// تهيئة نظام التقارير
document.addEventListener('DOMContentLoaded', () => {
    window.reportingSystem = new ReportingSystem();
});
