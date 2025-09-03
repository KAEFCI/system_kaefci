<!-- DROPDOWN -->
<div class="dropdown">
    <div class="dropdown-toggle" onclick="toggleDropdown(this)">⋮</div>
    <div class="dropdown-menu">
        <button onclick="openEditModal('{{ $user->id }}')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Edit
        </button>
        <button onclick="openDeleteModal('{{ $user->id }}')">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 6h18"></path>
                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
            </svg>
            Delete
        </button>
    </div>
</div>

<!-- SCRIPT DROPDOWN -->
<script>
    function toggleDropdown(trigger) {
        const dropdown = trigger.parentElement.querySelector('.dropdown-menu');
        const allDropdowns = document.querySelectorAll('.dropdown-menu');

        // Tutup semua dropdown yang terbuka dan restore posisi jika dipindah
        allDropdowns.forEach(menu => {
            if (menu !== dropdown) {
                menu.classList.remove('show');
                if (menu._moved) {
                    try {
                        menu._origParent.insertBefore(menu, menu._nextSibling || null);
                    } catch (err) {
                        // ignore
                    }
                    menu._moved = false;
                }
            }
        });

        // Jika dropdown sudah terbuka, tutup dan kembalikan posisi
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            if (dropdown._moved) {
                dropdown._origParent.insertBefore(dropdown, dropdown._nextSibling || null);
                dropdown._moved = false;
            }
            return;
        }

        // Pindahkan dropdown ke body untuk menghindari clipping dari parent (overflow/transform)
        if (dropdown.parentElement !== document.body) {
            dropdown._origParent = dropdown.parentElement;
            dropdown._nextSibling = dropdown.nextSibling;
            document.body.appendChild(dropdown);
            dropdown._moved = true;
        }

        // Tampilkan dropdown
        dropdown.classList.add('show');
        dropdown.style.position = 'fixed';

        // Fungsi reposition untuk scroll/resize
        function reposition() {
            const rect = trigger.getBoundingClientRect();
            const dropdownWidth = dropdown.offsetWidth;
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            // Default posisi di bawah dan rata kanan tombol
            let left = rect.right - dropdownWidth;
            if (left < 8) left = 8;
            if (left + dropdownWidth > viewportWidth - 8) left = viewportWidth - dropdownWidth - 8;
            dropdown.style.left = `${left}px`;

            // Cek ruang vertikal
            if (rect.bottom + dropdown.offsetHeight + 10 > viewportHeight) {
                dropdown.style.top = `${rect.top - dropdown.offsetHeight - 10}px`;
                dropdown.classList.add('dropdown-up');
            } else {
                dropdown.style.top = `${rect.bottom + 5}px`;
                dropdown.classList.remove('dropdown-up');
            }
        }

        // Close handler: klik di luar atau saat resize/scroll ketika tidak lagi relevan
        function closeDropdown(e) {
            if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                dropdown.classList.remove('show');
                document.removeEventListener('click', closeDropdown);
                window.removeEventListener('resize', onResize);
                window.removeEventListener('scroll', onScroll, true);
                if (dropdown._moved) {
                    try {
                        dropdown._origParent.insertBefore(dropdown, dropdown._nextSibling || null);
                    } catch (err) {
                        // ignore
                    }
                    dropdown._moved = false;
                }
            }
        }

        // Lightweight handlers
        const onResize = () => reposition();
        const onScroll = () => reposition();

        // Attach listeners immediately (no delay)
        document.addEventListener('click', closeDropdown);
        window.addEventListener('resize', onResize);
        window.addEventListener('scroll', onScroll, true);

        // First positioning
        reposition();
    }

</script>

<!-- STYLE DROPDOWN -->
<style>
/* Dropdown container, pastikan di atas sidebar dan konten lain */
.dropdown {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto;
    z-index: 9999;
}

/* Tombol titik tiga */
.dropdown-toggle {
    cursor: pointer;
    font-size: 22px;
    padding: 4px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    transition: color 0.2s ease;
    background: transparent;
    margin: 0 auto;
    pointer-events: auto;
    z-index: 10001;
}

.dropdown-toggle:hover {
    color: var(--kfc-red);
}

/* Menu dropdown */
.dropdown-menu {
    display: none;
    position: fixed;
    background-color: #fff;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    min-width: 130px;
    box-shadow: 0 6px 24px rgba(0,0,0,0.18), 0 1.5px 4px rgba(0,0,0,0.10);
    padding: 4px;
    opacity: 0;
    visibility: hidden;
    transition: none;
    z-index: 10002;
    pointer-events: auto;
}

.dropdown-menu.show {
    display: block;
    opacity: 1;
    visibility: visible;
    animation: none;
}

/* Hapus animasi agar dropdown langsung muncul */
.dropdown-up {
    transform-origin: bottom center;
    animation: none !important;
}

/* Tombol dalam menu */
.dropdown-menu button {
    width: 100%;
    padding: 8px 12px;
    background: none;
    border: none;
    text-align: left;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: #444;
    cursor: pointer;
    transition: all 0.15s;
    border-radius: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    white-space: nowrap;
}

.dropdown-menu button svg {
    width: 15px;
    height: 15px;
    stroke: #666;
    stroke-width: 2;
}

.dropdown-menu button:hover svg {
    stroke: currentColor;
}

.dropdown-menu button {
    color: #666;
}

.dropdown-menu button:hover {
    background-color: #f8f8f8;
    color: var(--kfc-red);
}

.dropdown-menu button:last-child:hover {
    color: #dc2626;
}

.dropdown-menu button:last-child:hover svg {
    stroke: #dc2626;
}
</style>
