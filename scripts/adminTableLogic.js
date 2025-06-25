document.addEventListener('DOMContentLoaded', function () {
  function searchStudents() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('studentsTable');
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

  function filterStudents() {
    const select = document.getElementById('filterSelect');
    const filter = select.value;
    const table = document.getElementById('studentsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
      const tdId = tr[i].getElementsByTagName('td')[1];
      if (tdId) {
        const idValue = tdId.textContent || tdId.innerText;
        tr[i].style.display = filter === '' || idValue.startsWith(filter) ? '' : 'none';
      }
    }
  }

  function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.getElementsByClassName('student-checkbox');
    for (let checkbox of checkboxes) {
      checkbox.checked = selectAll.checked;
    }
  }

  window.openEditModal = function (utmid) {
    const row = [...document.querySelectorAll('tr')].find(r =>
      r.querySelector('.student-id')?.innerText === utmid
    );
    if (!row) return;

    const cells = row.getElementsByTagName('td');
    document.getElementById('edit_utmid').value = utmid;
    document.getElementById('edit_first_name').value = cells[2].innerText.split(' ')[0];
    document.getElementById('edit_last_name').value = cells[2].innerText.split(' ')[1] || '';
    document.getElementById('edit_email').value = cells[3].innerText;

    document.getElementById('editModal').style.display = 'flex';
  };

  window.closeModal = function () {
    document.getElementById('editModal').style.display = 'none';
  };

  document.getElementById('editForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../backend/updateStudent.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.text())
      .then(data => {
        alert('Student updated successfully');
        location.reload();
      })
      .catch(error => console.error('Error:', error));
  });

  window.deleteStudent = function (studentId) {
    if (confirm('Are you sure you want to delete student ' + studentId + '?')) {
      fetch('../backend/deleteStudent.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'utmid=' + encodeURIComponent(studentId)
      })
        .then(res => res.text())
        .then(() => location.reload());
    }
  };

  // Expose global functions if needed
  window.searchStudents = searchStudents;
  window.filterStudents = filterStudents;
  window.toggleSelectAll = toggleSelectAll;
});
