const monthNames = ["Januari", "Februari", "Maret", "April", "Mey", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

function renderCalendar() {
  const monthYearDisplay = document.getElementById('month-year');
  monthYearDisplay.innerHTML = `${monthNames[currentMonth]} ${currentYear}`;

  const datesContainer = document.getElementById('dates');
  datesContainer.innerHTML = ''; // Clear the previous month dates

  // Get first day of the month and number of days in the month
  const firstDay = new Date(currentYear, currentMonth, 1);
  const lastDay = new Date(currentYear, currentMonth + 1, 0);

  const daysInMonth = lastDay.getDate();
  const startDay = firstDay.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

  // Empty cells for previous month
  for (let i = 0; i < startDay; i++) {
    const emptyCell = document.createElement('div');
    emptyCell.classList.add('date', 'disabled');
    datesContainer.appendChild(emptyCell);
  }

  // Create cells for the current month
  for (let i = 1; i <= daysInMonth; i++) {
    const dateCell = document.createElement('div');
    dateCell.classList.add('date');
    dateCell.textContent = i;
    dateCell.onclick = () => alert(`Selected date: ${i} ${monthNames[currentMonth]} ${currentYear}`);
    datesContainer.appendChild(dateCell);
  }
}

function changeMonth(offset) {
  currentMonth += offset;

  if (currentMonth < 0) {
    currentMonth = 11;
    currentYear--;
  } else if (currentMonth > 11) {
    currentMonth = 0;
    currentYear++;
  }

  renderCalendar();
}

renderCalendar();
