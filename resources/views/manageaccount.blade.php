<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
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
          <div class="table-header">
            <input type="text" placeholder="Search..." />
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
            {{ $users->links() }}
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
    });

    function closeModal() {
      modal.style.display = "none";
    }

    window.onclick = function (event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    };

    function openEditModal(userId) {
      document.getElementById(`editAccountModal-${userId}`).style.display = "block";
    }

    function closeEditModal(userId) {
      document.getElementById(`editAccountModal-${userId}`).style.display = "none";
    }

    function openDeleteModal(userId) {
      document.getElementById(`deleteAccountModal-${userId}`).style.display = "block";
    }

    function closeDeleteModal(userId) {
      document.getElementById(`deleteAccountModal-${userId}`).style.display = "none";
    }

    // Optional: close modal when clicking outside any modal
    window.onclick = function (event) {
      document.querySelectorAll(".modal").forEach((modal) => {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      });
    };
  </script>
</body>
</html>