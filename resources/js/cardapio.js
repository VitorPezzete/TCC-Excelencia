document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('product-modal');
    const modalName = document.getElementById('modal-name');
    const modalDescription = document.getElementById('modal-description');
    const modalPrice = document.getElementById('modal-price');
    const modalCategory = document.getElementById('modal-category');
    const modalImage = document.getElementById('modal-image');
    const modalImagePlaceholder = document.getElementById('modal-image-placeholder');
    const qtyDisplay = document.getElementById('qty-display');
    let qty = 1;

    document.querySelectorAll('.open-modal').forEach(card => {
        card.addEventListener('click', function () {
            const name = this.dataset.name;
            const description = this.dataset.description;
            const price = this.dataset.price;
            const category = this.dataset.category;
            const image = this.dataset.image;

            modalName.textContent = name;
            modalDescription.textContent = description;
            modalPrice.textContent = 'R$ ' + price;
            modalCategory.textContent = category;

            if (image) {
                modalImage.src = image;
                modalImage.alt = name;
                modalImage.classList.remove('hidden');
                modalImagePlaceholder.classList.add('hidden');
            } else {
                modalImage.classList.add('hidden');
                modalImagePlaceholder.classList.remove('hidden');
            }

            qty = 1;
            qtyDisplay.textContent = qty;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.getElementById('modal-close').addEventListener('click', closeModal);
    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.getElementById('qty-minus').addEventListener('click', function () {
        if (qty > 1) { qty--; qtyDisplay.textContent = qty; }
    });
    document.getElementById('qty-plus').addEventListener('click', function () {
        qty++; qtyDisplay.textContent = qty;
    });
});