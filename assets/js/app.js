console.log("Real-Time Product Ordering System loaded.");

document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.querySelector('input[name="image"]');
    const preview = document.getElementById('imagePreview');
    if (imageInput && preview) {
        imageInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('is-hidden');
        });
    }
});
