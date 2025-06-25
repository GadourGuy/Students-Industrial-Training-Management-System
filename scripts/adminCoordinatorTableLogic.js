document.addEventListener('DOMContentLoaded', function () {
  // Search Coordinators
  function searchCoordinators() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('coordinatorsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
      const tdId = tr[i].getElementsByTagName('td')[1];
      const tdName = tr[i].getElementsByTagName('td')[2];
      const tdEmail = tr[i].getElementsByTagName('td')[3];

      if (tdId && tdName && tdEmail) {
        const idValue = tdId.textContent || tdId.innerText;
        const nameValue = tdName.textContent || tdName.innerText;
        const emailValue = tdEmail.textContent || tdEmail.innerText;

        tr[i].style.display =
          idValue.toUpperCase().includes(filter) ||
          nameValue.toUpperCase().includes(filter) ||
          emailValue.toUpperCase().includes(filter)
            ? ''
            : 'none';
      }
    }
  }

  // Select All checkbox
  function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.getElementsByClassName('coordinator-checkbox');
    for (let checkbox of checkboxes) {
      checkbox.checked = selectAll.checked;
    }
  }

  // Open Edit Modal
  window.openEditModal = function (utmid) {
    const row = [...document.querySelectorAll('tr')].find(r =>
      r.querySelector('.coordinator-id')?.innerText === utmid
    );
    if (!row) return;

    const cells = row.getElementsByTagName('td');
    document.getElementById('edit_utmid').value = utmid;
    document.getElementById('edit_first_name').value = cells[1].innerText.split(' ')[0];
    document.getElementById('edit_last_name').value = cells[0].innerText.split(' ')[1] || '';
    document.getElementById('edit_email').value = cells[2].innerText;

    document.getElementById('editModal').style.display = 'flex';
  };

  // Close Modal
  window.closeModal = function () {
    document.getElementById('editModal').style.display = 'none';
  };

  // Handle Edit Form Submission
  document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../backend/updateCoordinator.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.text())
      .then(data => {
        alert('Coordinator updated successfully');
        location.reload();
      })
      .catch(error => console.error('Error:', error));
  });

  // Delete Coordinator
  window.deleteCoordinator = function (utmid) {
    if (confirm('Are you sure you want to delete coordinator ' + utmid + '?')) {
      fetch('../backend/deleteCoordinator.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'utmid=' + encodeURIComponent(utmid)
      })
        .then(res => res.text())
        .then(() => location.reload());
    }
  };

  // Expose globally
  window.searchCoordinators = searchCoordinators;
  window.toggleSelectAll = toggleSelectAll;
});
