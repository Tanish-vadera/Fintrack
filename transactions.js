// transactions.js

// Load from localStorage or fallback to default
let transactions = JSON.parse(localStorage.getItem("transactions")) || [
  {"date":"31 Aug 2025","description":"Grocery","category":"Food","amount":-1200},
  {"date":"30 Aug 2025","description":"Salary","category":"Income","amount":25000},
  {"date":"29 Aug 2025","description":"Movie","category":"Entertainment","amount":-500},
  {"date":"28 Aug 2025","description":"Electricity Bill","category":"Utilities","amount":-2000},
  {"date":"27 Aug 2025","description":"Freelance","category":"Income","amount":5000}
];

// Save to localStorage
function saveTransactions() {
  localStorage.setItem("transactions", JSON.stringify(transactions));
}

// Render function
function renderTransactions(targetId) {
  const tbody = document.getElementById(targetId);
  if (!tbody) return; // in case table not present
  tbody.innerHTML = "";
  transactions.forEach(tx => {
    const row = document.createElement("tr");
    row.innerHTML = `
      <td>${tx.date}</td>
      <td>${tx.description}</td>
      <td>${tx.category}</td>
      <td style="color:${tx.amount < 0 ? 'red':'green'}">
        ${tx.amount < 0 ? '-' : '+'}₹${Math.abs(tx.amount)}
      </td>`;
    tbody.appendChild(row);
  });
}
