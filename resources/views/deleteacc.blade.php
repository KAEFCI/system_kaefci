<div id="deleteAccountModal-{{ $user->id }}" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeDeleteModal('{{ $user->id }}')">&times;</span>
        <h2>Delete Confirmation</h2>
        <p>Are you sure you want to delete <strong>{{ $user->name }}</strong>?</p>
        <form method="POST" action="{{ route('account.destroy', $user->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="danger">Yes, Delete</button>
        </form>
    </div>
</div>
