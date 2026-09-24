// =====================================================
// 1. REMAINING INVENTORY
// =====================================================
new Chart(document.getElementById('inventoryChart'), {
    type: 'bar',
    data: {
        labels: inventoryCategories,
        datasets: [{
            label: 'Remaining Quantity',
            data: inventoryQuantity
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// =====================================================
// 2. SALES BY CATEGORY
// =====================================================
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: salesCategories,
        datasets: [{
            label: 'Total Sales',
            data: salesAmount
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// =====================================================
// 3. EXPENSES BY CATEGORY
// =====================================================
new Chart(document.getElementById('expenseChart'), {
    type: 'bar',
    data: {
        labels: expenseCategories,
        datasets: [{
            label: 'Total Expenses',
            data: expenseAmount
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// =====================================================
// 4. TRANSACTION TYPES
// =====================================================
new Chart(document.getElementById('transactionChart'), {
    type: 'bar',
    data: {
        labels: [
            'Sold',
            'Returned',
            'Damaged',
            'Lost'
        ],

        datasets: [{
            label: 'Quantity',
            data: [
                transactionTypes.Sold,
                transactionTypes.Returned,
                transactionTypes.Damaged,
                transactionTypes.Lost
            ]
        }]
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

// =====================================================
// 5. TOP CUSTOMERS
// =====================================================
new Chart(document.getElementById('customerChart'), {
    type: 'bar',
    data: {
        labels: customerNames,
        datasets: [{
            label: 'Total Spending',
            data: customerAmount
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// =====================================================
// 6. SLOW MOVING PRODUCTS
// =====================================================
new Chart(document.getElementById('slowChart'), {
    type: 'bar',
    data: {
        labels: slowProducts,
        datasets: [{
            label: 'Days Without Sale',
            data: slowDays
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

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

// =====================================================
// 8. INVENTORY VALUE BY CATEGORY
// =====================================================
new Chart(document.getElementById('valueChart'), {
    type: 'bar',
    data: {
        labels: inventoryValueCategories,
        datasets: [{
            label: 'Inventory Value',
            data: inventoryValues
        }]
    },

    options: {
        responsive: true,
        indexAxis: 'y',
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});