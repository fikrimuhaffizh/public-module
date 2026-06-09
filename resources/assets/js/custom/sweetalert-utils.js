/**
 * SweetAlert Utility Functions
 * Centralized functions to handle all SweetAlert calls in the application
 * Following DRY (Don't Repeat Yourself) principle
 */

// Delete Confirmation Alert
function showDeleteConfirmation(title = 'Are you sure?', text = 'This action cannot be undone!', confirmButtonText = 'Yes, delete it!') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel'
    });
}

// General Confirmation Alert
function showConfirmation(title = 'Are you sure?', text = '', confirmButtonText = 'Yes') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel'
    });
}

// Success Message
function showSuccessMessage(title = 'Success!', text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Error Message
function showErrorMessage(title = 'Error!', text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Info Message
function showInfoMessage(title = 'Info', text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'info',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Warning Message
function showWarningMessage(title = 'Warning!', text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
}

// Loading Message (not auto-closing)
function showLoadingMessage(title = 'Processing...', text = 'Please wait') {
    return Swal.fire({
        title: title,
        html: text,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Form error display (for validation errors)
function showFormErrors(errors) {
    let errorText = '';

    if (typeof errors === 'string') {
        errorText = errors;
    } else if (Array.isArray(errors)) {
        errorText = errors.join('<br>');
    } else if (typeof errors === 'object') {
        errorText = Object.values(errors).flat().join('<br>');
    } else {
        errorText = 'An error occurred while processing your request.';
    }

    return Swal.fire({
        title: 'Validation Error!',
        html: errorText,
        icon: 'error',
        timer: 5000,
        timerProgressBar: true
    });
}

// AJAX response handler
function handleAjaxResponse(response, successCallback = null, errorCallback = null) {
    if (response.success) {
        showSuccessMessage(response.title || 'Success!', response.message || '');
        if (successCallback && typeof successCallback === 'function') {
            successCallback(response);
        }
    } else {
        showErrorMessage(response.title || 'Error!', response.message || response.error || '');
        if (errorCallback && typeof errorCallback === 'function') {
            errorCallback(response);
        }
    }
}

// Bulk action confirmation
function showBulkActionConfirmation(actionName, count, itemType = 'item') {
    return Swal.fire({
        title: `Confirm Bulk ${actionName}`,
        text: `Are you sure you want to ${actionName.toLowerCase()} ${count} ${itemType}${count > 1 ? 's' : ''}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${actionName.toLowerCase()} them!`,
        cancelButtonText: 'Cancel'
    });
}

// General confirmation function
function confirmAction(title, text, confirmText = 'Yes', callback) {
    showConfirmation(title, text, confirmText).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

// Delete confirmation with axios (for use with controller-generated links)
function confirmDelete(url, title = 'Delete this item?', text = 'Are you sure you want to delete this item? This action cannot be undone.') {
    showDeleteConfirmation(title, text).then((result) => {
        if (result.isConfirmed) {
            axios.post(url, { _method: 'DELETE' })
                .then(function (response) {
                    if (response.data.success) {
                        showSuccessMessage(response.data.message || 'Item deleted successfully!');
                        // Reload the DataTable if it exists
                        if (typeof table !== 'undefined' && table.ajax) {
                            table.ajax.reload();
                        } else {
                            location.reload();
                        }
                    }
                })
                .catch(function (error) {
                    const errorMessage = error.response?.data?.message || 'An error occurred while deleting the item.';
                    showErrorMessage('Error!', errorMessage);
                });
        }
    });
}