<div class="dropdown">
    <div class="dropdown-toggle" onclick="toggleDropdown(this)">&#8942;</div>
    <div class="dropdown-menu">
        <button onclick="openEditModal('{{ $user->id }}')">Edit</button>
        <button onclick="openDeleteModal('{{ $user->id }}')">Delete</button>
    </div>
</div>

<script>
    // Toggling dropdown per baris
    function toggleDropdown(trigger) {
        // Tutup dropdown lain
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            if (dropdown !== trigger.parentElement) {
                dropdown.classList.remove('show');
            }
        });

        // Toggle dropdown yang diklik
        trigger.parentElement.classList.toggle('show');
    }

    // Tutup dropdown kalau klik di luar
    window.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
</script>

<style>
.dropdown {
    position: relative;
    display: inline-block;
}

/* Tombol titik tiga */
.dropdown-toggle {
    cursor: pointer;
    font-size: 1.5rem;
    padding: 5px;
}

/* Menu dropdown */
.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 120%;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    min-width: 100px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    z-index: 100;
}

/* Aktifkan dropdown saat class .show aktif */
.dropdown.show .dropdown-menu {
    display: block;
}

/* Tombol dalam menu */
    .dropdown-menu button {
        width: 100%;
        padding: 10px 12px;
        background: none;
        border: none;
        text-align: left;
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        transition: background 0.2s;
    }

.dropdown-menu button:hover {
    background-color: #f2f2f2;
}

</style>