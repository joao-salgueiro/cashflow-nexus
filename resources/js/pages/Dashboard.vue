<template>
  <div class="dashboard">
    <h1>🚀 CashFlow Nexus</h1>
    <p>Welcome! Vue is working! ✨</p>
    
    <div class="counter">
      <h2>Counter: {{ count }}</h2>
      <button @click="increment">Add 1</button>
      <button @click="decrement">Remove 1</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      count: 0
    }
  },
  methods: {
    increment() {
      this.count++;
    },
    decrement() {
      this.count--;
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 40px;
  text-align: center;
  font-family: Arial, sans-serif;
}

h1 {
  color: #333;
  margin-bottom: 20px;
}

.counter {
  margin-top: 30px;
  padding: 20px;
  background: #f5f5f5;
  border-radius: 8px;
}

button {
  padding: 10px 20px;
  margin: 0 10px;
  font-size: 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background: #0056b3;
}
</style>