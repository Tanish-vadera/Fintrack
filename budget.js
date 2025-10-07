// Default budgets if none in localStorage
const defaultBudgets = [
  { category: "Food", planned: 5000 },
  { category: "Rent", planned: 6000 },
  { category: "Entertainment", planned: 2000 },
  { category: "Utilities", planned: 1500 },
  { category: "Transportation", planned: 1000 },
  { category: "Other", planned: 500 }
];

// Get budgets from localStorage or initialize
let budgets = JSON.parse(localStorage.getItem("budgets")) || defaultBudgets;

// Get transactions
let transactions = JSON.parse(localStorage.getItem("transactions")) || [];

// Function to calculate spent amount per category
function getSpent(category) {
    return transactions
      .filter(t => t.category === category)
      .reduce((sum, t) => sum + Number(t.amount), 0);
}

// Render budget cards
function renderBudgets() {
    const container  = document.getElementById("budget-cards");
    container.innerHTML = "";

    budgets.forEach((b, idx) => {
        const spent = getSpent(b.category);
        const percent = Math.min(Math.round((spent / b.planned) * 100), 100);

        const card = document.createElement("div");
        card.className = "budget-card";
        card.innerHTML = `
            <h3>${b.category}</h3>
            <label>Planned: ₹<input type="number" value="${b.planned}" data-index="${idx}" class="planned-input"></label>
            <p>Spent: ₹${spent}</p>
            <div class="progress-bar">
                <div class="progress" style="width:${percent}%"></div>
            </div>
        `;
        container.appendChild(card);
    });

    const logoutBtn = document.getElementById('logout-btn');
    logoutBtn.addEventListener('click', () => {
      if(confirm("Are you sure you want to logout?")) {
        localStorage.clear();
        alert("Logged out successfully!");
        window.location.href = "index.html";
      }
    });

    // Add listener for planned input changes
    document.querySelectorAll(".planned-input").forEach(input => {
        input.addEventListener("change", e => {
            const index = e.target.dataset.index;
            budgets[index].planned = Number(e.target.value);
            localStorage.setItem("budgets", JSON.stringify(budgets));
            renderBudgets(); // re-render to update progress
        });
    });
}

// Initial render
renderBudgets();
