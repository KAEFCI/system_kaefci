<div id="editAccountModal-{{ $user->id }}" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeEditModal('{{ $user->id }}')">&times;</span>
        <h2>Edit Account</h2>
        <form method="POST" action="{{ route('account.update', $user->id) }}">
            @csrf
            @method('PUT')
            <label>Name</label>
            <input type="text" name="name" value="{{ $user->name }}" required>

            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" required>

            <label>Role</label>
            @if($user->role == 'superadmin')
                <input type="text" name="role" value="superadmin" readonly style="background:#eee;">
            @else
                <select name="role" required>
                    <option value="hrd" {{ $user->role == 'hrd' ? 'selected' : '' }}>HRD</option>
                    <option value="supervisor" {{ $user->role == 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                    <option value="karyawan" {{ $user->role == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                </select>
            @endif

            <label>Status</label>
            <select name="status">
                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="disable" {{ $user->status == 'disable' ? 'selected' : '' }}>Disable</option>
            </select>

            <label>Password</label>
            <input type="password" name="password" placeholder="New password">

            <button type="submit">Update</button>
        </form>
    </div>
</div>

<style>

.modal-content form label {
    font-size: 16px;
}
/* Modal container */
.modal {
    display: none;
    position: fixed;
    z-index: 999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    transition: 0.3s ease-in-out;
}
/* Modal content box */
.modal-content {
    background-color: #ffffff;
    margin: 6% auto;
    padding: 30px;
    border-radius: 10px;
    width: 500px;
    max-width: 95%;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-in-out;
    position: relative;
    font-family: 'Poppins', sans-serif;
}
/* Close button (×) */
.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    font-weight: 600;
    color: #999;
    cursor: pointer;
    transition: color 0.2s ease;
}
.close-btn:hover {
    color: #E3002C;
}
/* Modal title */
.modal-content h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: #E3002C;
    font-weight: 600;
    text-align: left;
}
.modal-content form {
    text-align: left;
}
.modal-content form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #333;
    text-align: left;
}
.modal-content form input[type="text"],
.modal-content form input[type="email"],
.modal-content form input[type="password"],
.modal-content form select {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s ease;
    text-align: left;
}
.modal-content form input:focus,
.modal-content form select:focus {
    border-color: #E3002C;
    outline: none;
}
.modal-content form button[type="submit"] {
    background-color: #E3002C;
    color: #fff;
    padding: 12px 18px;
    font-size: 14px;
    font-weight: 500;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.2s ease;
}
.modal-content form button[type="submit"]:hover {
    background-color: #c00026;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
