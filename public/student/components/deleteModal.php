<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 5px;">
            <div class="modal-header bg-light border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2" style="font-size: 1.2rem;"></i>
                    Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-0 text-muted" style="font-size: 1.1rem; line-height: 1.5;">
                    Are you sure you want to delete this download? <br>
                    <strong class="text-danger">This action cannot be undone.</strong>
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center" style="border-radius: 0 0 15px 15px;">
                <button type="button" class="btn btn-success px-4 py-2 me-3" data-bs-dismiss="modal" style="border-radius: 25px; font-weight: 600;">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4 py-2" id="confirmDeleteBtn" style="border-radius: 25px; font-weight: 600;">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
