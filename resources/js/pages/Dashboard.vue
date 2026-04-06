<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1>CashFlow Nexus</h1>
      <p>Smart Cash Flow Analysis & Forecasting</p>
    </header>

    <div class="dashboard-grid">
      <section class="form-section">
        <h2>Add Transaction</h2>
        <form @submit.prevent="addTransaction" class="transaction-form">
          <div class="form-group">
            <label>Date</label>
            <input 
              type="date" 
              v-model="form.date" 
              required
            >
          </div>

          <div class="form-group">
            <label>Amount</label>
            <input 
              type="number" 
              v-model.number="form.amount" 
              placeholder="0.00"
              step="0.01"
              required
            >
          </div>

          <div class="form-group">
            <label>Type</label>
            <select v-model="form.type" required>
              <option value="">Select type...</option>
              <option value="income">Income</option>
              <option value="expense">Expense</option>
            </select>
          </div>

          <div class="form-group">
            <label>Category</label>
            <input 
              type="text" 
              v-model="form.category"
              placeholder="e.g., Salary, Food, Transport"
            >
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea 
              v-model="form.description"
              placeholder="Optional notes..."
              rows="2"
            ></textarea>
          </div>

          <button type="submit" class="btn-primary">
            {{ loading ? 'Adding...' : 'Add Transaction' }}
          </button>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>
          <div v-if="success" class="success-message">
            Transaction added!
          </div>
        </form>
      </section>

      <section class="stats-section">
        <h2>Summary</h2>
        <div class="stats-grid">
          <div class="stat-card income">
            <div class="stat-label">Total Income</div>
            <div class="stat-value">{{ formatCurrency(totalIncome) }}</div>
            <div class="stat-count">{{ incomeCount }} transactions</div>
          </div>

          <div class="stat-card expense">
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value">{{ formatCurrency(totalExpenses) }}</div>
            <div class="stat-count">{{ expenseCount }} transactions</div>
          </div>

          <div class="stat-card balance">
            <div class="stat-label">Balance</div>
            <div class="stat-value" :class="totalBalance >= 0 ? 'positive' : 'negative'">
              {{ formatCurrency(totalBalance) }}
            </div>
            <div class="stat-count">Net</div>
          </div>
        </div>
      </section>

      <section class="chart-section">
        <h2>Cash Flow Over Time</h2>
        <div class="chart-container">
          <canvas id="cashFlowChart"></canvas>
        </div>
      </section>

      <section class="transactions-section">
        <h2>Recent Transactions</h2>
        <div v-if="cashFlows.length === 0" class="empty-state">
          <p>No transactions yet. Add one to get started!</p>
        </div>
        <table v-else class="transactions-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Category</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="transaction in cashFlows" :key="transaction.id" :class="transaction.type">
              <td>{{ formatDate(transaction.date) }}</td>
              <td>{{ transaction.category || '—' }}</td>
              <td>
                <span :class="`type-badge ${transaction.type}`">
                  {{ transaction.type === 'income' ? '' : '' }}
                  {{ transaction.type }}
                </span>
              </td>
              <td :class="`amount ${transaction.type}`">
                {{ transaction.type === 'income' ? '+' : '' }}{{ formatCurrency(transaction.amount) }}
              </td>
              <td>{{ transaction.description || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </section>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  name: 'Dashboard',
  data() {
    return {
      // Form data
      form: {
        date: new Date().toISOString().split('T')[0],
        amount: null,
        type: '',
        category: '',
        description: ''
      },

      // Data from API
      cashFlows: [],

      // UI state
      loading: false,
      error: null,
      success: false,

      // Chart instance
      chart: null
    }
  },

  // Computed properties = values that are calculated automatically
  computed: {
    // Calculate total income
    totalIncome() {
      return this.cashFlows
        .filter(cf => cf.type === 'income')
        .reduce((sum, cf) => sum + parseFloat(cf.amount), 0);
    },

    // Calculate total expenses
    totalExpenses() {
      return Math.abs(
        this.cashFlows
          .filter(cf => cf.type === 'expense')
          .reduce((sum, cf) => sum + parseFloat(cf.amount), 0)
      );
    },

    // Calculate balance (income - expenses)
    totalBalance() {
      return this.totalIncome - this.totalExpenses;
    },

    // Count income transactions
    incomeCount() {
      return this.cashFlows.filter(cf => cf.type === 'income').length;
    },

    // Count expense transactions
    expenseCount() {
      return this.cashFlows.filter(cf => cf.type === 'expense').length;
    }
  },

  methods: {
    // Fetch all transactions from API
    async fetchCashFlows() {
      try {
        const response = await fetch('/api/cash-flows');
        if (!response.ok) throw new Error('Failed to fetch');
        this.cashFlows = await response.json();
        this.updateChart();
      } catch (err) {
        this.error = 'Failed to load transactions';
        console.error(err);
      }
    },

    // Add new transaction
    async addTransaction() {
      // Validate form
      if (!this.form.date || !this.form.amount || !this.form.type) {
        this.error = 'Please fill all required fields';
        return;
      }

      this.loading = true;
      this.error = null;
      this.success = false;

      try {
        const response = await fetch('/api/cash-flows', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: JSON.stringify({
            date: this.form.date,
            amount: this.form.type === 'expense' ? -Math.abs(this.form.amount) : this.form.amount,
            type: this.form.type,
            category: this.form.category,
            description: this.form.description
          })
        });

        if (!response.ok) throw new Error('Failed to add transaction');

        this.success = true;
        // Reset form
        this.form = {
          date: new Date().toISOString().split('T')[0],
          amount: null,
          type: '',
          category: '',
          description: ''
        };

        // Refresh data
        await this.fetchCashFlows();

        // Clear success message after 3 seconds
        setTimeout(() => { this.success = false; }, 3000);
      } catch (err) {
        this.error = err.message || 'Failed to add transaction';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    // Update the chart with new data
    updateChart() {
      // Sort by date
      const sorted = [...this.cashFlows].sort((a, b) => new Date(a.date) - new Date(b.date));

      // Prepare data for chart
      const labels = sorted.map(cf => this.formatDate(cf.date));
      const data = sorted.map(cf => parseFloat(cf.amount));

      // Calculate running balance
      let balance = 0;
      const balances = data.map(amount => {
        balance += amount;
        return balance;
      });

      // Destroy old chart if exists
      if (this.chart) {
        this.chart.destroy();
      }

      // Create new chart
      const ctx = document.getElementById('cashFlowChart')?.getContext('2d');
      if (!ctx) return;

      this.chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Running Balance',
              data: balances,
              borderColor: '#007bff',
              backgroundColor: 'rgba(0, 123, 255, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#007bff',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
              pointRadius: 5,
              pointHoverRadius: 7
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: {
              display: true,
              position: 'top'
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return '$' + value.toFixed(2);
                }
              }
            }
          }
        }
      });
    },

    formatCurrency(value) {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(value);
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      });
    }
  },

  mounted() {
    this.fetchCashFlows();
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #323646 0%, #161616 100%);
  padding: 40px 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-header {
  text-align: center;
  color: white;
  margin-bottom: 40px;
}

.dashboard-header h1 {
  font-size: 3em;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.dashboard-header p {
  font-size: 1.2em;
  opacity: 0.9;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 30px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Form Section */
.form-section,
.stats-section,
.chart-section,
.transactions-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.form-section h2,
.stats-section h2,
.chart-section h2,
.transactions-section h2 {
  margin-bottom: 20px;
  color: #333;
  font-size: 1.5em;
}

.transaction-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-weight: 600;
  color: #555;
  font-size: 0.95em;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px;
  border: 2px solid #222222;
  border-radius: 6px;
  font-size: 1em;
  transition: border-color 0.3s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-primary {
  padding: 12px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1em;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:active {
  transform: translateY(0);
}

.error-message {
  padding: 12px;
  background: #fee;
  color: #c33;
  border-radius: 6px;
  border-left: 4px solid #c33;
}

.success-message {
  padding: 12px;
  background: #efe;
  color: #3c3;
  border-radius: 6px;
  border-left: 4px solid #3c3;
}

/* Stats Section */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 15px;
}

.stat-card {
  padding: 20px;
  border-radius: 8px;
  text-align: center;
  color: white;
}

.stat-card.income {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.expense {
  background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card.balance {
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-label {
  font-size: 0.9em;
  opacity: 0.9;
  margin-bottom: 8px;
}

.stat-value {
  font-size: 1.8em;
  font-weight: bold;
  margin-bottom: 8px;
}

.stat-value.negative {
  color: #ffcccc;
}

.stat-value.positive {
  color: #ccffcc;
}

.stat-count {
  font-size: 0.85em;
  opacity: 0.8;
}

/* Chart Section */
.chart-container {
  position: relative;
  height: 300px;
}

/* Transactions Section */
.chart-section {
  grid-column: 1 / -1;
}

.transactions-section {
  grid-column: 1 / -1;
}

.transactions-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
}

.transactions-table th {
  background: #f5f5f5;
  padding: 12px;
  text-align: left;
  font-weight: 600;
  color: #555;
  border-bottom: 2px solid #e0e0e0;
}

.transactions-table td {
  padding: 12px;
  border-bottom: 1px solid #e0e0e0;
}

.transactions-table tr:hover {
  background: #f9f9f9;
}

.transactions-table tr.income {
  border-left: 4px solid #667eea;
}

.transactions-table tr.expense {
  border-left: 4px solid #f5576c;
}

.type-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.9em;
  font-weight: 600;
}

.type-badge.income {
  background: #e3f2fd;
  color: #1976d2;
}

.type-badge.expense {
  background: #fce4ec;
  color: #c2185b;
}

.amount {
  font-weight: 600;
  font-family: 'Courier New', monospace;
}

.amount.income {
  color: #667eea;
}

.amount.expense {
  color: #f5576c;
}

.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .dashboard-header h1 {
    font-size: 2em;
  }

  .transactions-table {
    font-size: 0.9em;
  }
}
</style>
