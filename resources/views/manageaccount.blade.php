<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="" crossorigin="anonymous" />
  <link rel="stylesheet" href="{{ asset('css/manageaccount.css') }}" />
</head>

<body>
  <div class="layout-wrapper">
    @include('sidebar')

    <div class="main-area">
      <div class="header-bar">
        <h1>
          {{ isset($title) ? $title : 'Manage Account' }}
        </h1>
      </div>

      <div class="main-content">
        <div class="card-table">
          <h2><i class="fa-solid fa-users"></i>Account</h2>
          <div class="table-header">
            <input id="searchEmployee" type="text" placeholder="Search..." />
            <button class="add-account-btn" type="button">Add Account</button>
          </div>

          <!-- Add Account Modal -->
          <div id="addAccountModal" class="modal">
            <div class="modal-content">
              <span class="close-btn" onclick="closeModal()">&times;</span>
              <h2>Add New Account</h2>
              <form id="addAccountForm" method="POST" action="{{ route('account.store') }}">
                @csrf
                <label for="name">Name</label>
                <input type="text" name="name" required />

                <label for="email">Email</label>
                <input type="email" name="email" required />

                <label for="role">Role</label>
                <select name="role" required>
                  <option value="hrd">HRD</option>
                  <option value="supervisor">Supervisor</option>
                  <option value="karyawan">Karyawan</option>
                </select>

                <label for="status">Status</label>
                <select name="status">
                  <option value="active">Active</option>
                  <option value="disable">Disable</option>
                </select>

                <label for="password">Password</label>
                <input type="password" name="password" required />

                <button type="submit">Create Account</button>
              </form>
            </div>
          </div>

          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Log in</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
            <tr>
                <td style="text-transform: uppercase">{{ $user->account_id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    @if($user->status === 'active')
                        <span class="status-active">Active</span>
                    @else
                        <span class="status-disable">Disable</span>
                    @endif
                </td>
                <td>
                    @php
                        $lastLogin = $user->last_login_at;
                        $online = method_exists($user,'isOnline') ? $user->isOnline(3) : false;
                    @endphp
                    @if(!$lastLogin)
                        <span class="never-login">Never Used</span>
                    @elseif($online)
                        <span class="status-active">Online</span>
                    @else
                        <span class="last-login">Last: {{ $lastLogin->diffForHumans() }}</span>
                    @endif
                </td>
                <td class="action">
                    @include('actiondrop')
                    @include('edit_modal')
                    @include('deleteacc')
                </td>
            </tr>
            @endforeach
            </tbody>
          </table>

          @if(method_exists($users, 'links'))
          <div class="pagination">
            <div class="pager-shell">
              @if($users->onFirstPage())
                <button disabled>&laquo;</button>
              @else
                <a href="{{ $users->previousPageUrl() }}"><button>&laquo;</button></a>
              @endif

              @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                  <button class="active">{{ $page }}</button>
                @else
                  <a href="{{ $url }}"><button>{{ $page }}</button></a>
                @endif

                @if($page < $users->lastPage())
                  @if($page + 1 < $users->currentPage() - 1 || $page + 1 > $users->currentPage() + 1)
                    <span class="ellipsis">...</span>
                    @php 
                      $page = $page < $users->currentPage() ? $users->currentPage() - 2 : $users->lastPage() - 1;
                    @endphp
                  @endif
                @endif
              @endforeach

              @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"><button>&raquo;</button></a>
              @else
                <button disabled>&raquo;</button>
              @endif
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <script>
      const modal = document.getElementById("addAccountModal");
      const addBtn = document.querySelector(".add-account-btn");

      addBtn.addEventListener("click", function () {
        modal.style.display = "block";
        document.body.classList.add('modal-open');
      });

      function closeModal() {
        modal.style.display = "none";
        document.body.classList.remove('modal-open');
      }

      // Disable close on outside click for add modal

      function openEditModal(userId) {
        document.getElementById(`editAccountModal-${userId}`).style.display = "block";
        document.body.classList.add('modal-open');
      }

      function closeEditModal(userId) {
        document.getElementById(`editAccountModal-${userId}`).style.display = "none";
        document.body.classList.remove('modal-open');
      }

      function openDeleteModal(userId) {
        document.getElementById(`deleteAccountModal-${userId}`).style.display = "block";
        document.body.classList.add('modal-open');
      }

      function closeDeleteModal(userId) {
        document.getElementById(`deleteAccountModal-${userId}`).style.display = "none";
        document.body.classList.remove('modal-open');
      }

      // Remove closing by clicking outside modals (no-op to override previous handlers)
      window.onclick = function () {};

      // Prevent ESC key from closing modals
      window.addEventListener('keydown', function(e){
        if(e.key === 'Escape'){
          e.preventDefault();
          e.stopPropagation();
        }
      }, true);

      // Client-side search (filter rows by ID / Name / Email)
      (function(){
        const searchInput = document.getElementById('searchEmployee');
        if(!searchInput) return;
        searchInput.addEventListener('input', function(e){
          const q = e.target.value.trim().toLowerCase();
          const tbody = document.querySelector('table tbody');
          if(!tbody) return;
          const rows = Array.from(tbody.querySelectorAll('tr'));
          rows.forEach(row => {
            const id = (row.children[0] && row.children[0].textContent || '').toLowerCase();
            const name = (row.children[1] && row.children[1].textContent || '').toLowerCase();
            const email = (row.children[2] && row.children[2].textContent || '').toLowerCase();
            if(!q || id.includes(q) || name.includes(q) || email.includes(q)){
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      })();

      // Dropdown action (titik tiga) toggle
      document.addEventListener('click', function(e) {
        // Tutup semua dropdown
        document.querySelectorAll('.action-dropdown').forEach(function(drop) {
          drop.style.display = 'none';
        });
        document.querySelectorAll('.action-menu').forEach(function(menu) {
          menu.classList.remove('open');
        });
        // Jika klik pada trigger
        if(e.target.classList.contains('action-trigger')) {
          const menu = e.target.closest('.action-menu');
          if(menu) {
            const dropdown = menu.querySelector('.action-dropdown');
            if(dropdown) {
              dropdown.style.display = 'block';
              menu.classList.add('open');
              e.stopPropagation();
            }
          }
        }
      });
      // Prevent dropdown from closing when clicking inside
      document.addEventListener('click', function(e) {
        if(e.target.closest('.action-dropdown')) {
          e.stopPropagation();
        }
      }, true);
  </script>
  @include('partials.footer')
</body>
</html>