// =====================================================
// 7. MONTHLY SALES AND EXPENSES
// =====================================================
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [
            {
                label: 'Sales',
                data: monthlySales
            },
            {
                label: 'Expenses',
                data: monthlyExpenses
            }
        ]
    },

    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
