document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('product-modal');
    const modalName = document.getElementById('modal-name');
    const modalDescription = document.getElementById('modal-description');
    const modalPrice = document.getElementById('modal-price');
    const modalCategory = document.getElementById('modal-category');
    const modalImage = document.getElementById('modal-image');
    const modalImagePlaceholder = document.getElementById('modal-image-placeholder');
    const qtyDisplay = document.getElementById('qty-display');
    const observacoesInput = document.getElementById('observacoes');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    
    let qty = 1;
    let currentProductId = null;

    document.querySelectorAll('.open-modal').forEach(card => {
        card.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const price = this.dataset.price;
            const category = this.dataset.category;
            const image = this.dataset.image;

            currentProductId = id;
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
            observacoesInput.value = '';
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

    addToCartBtn.addEventListener('click', function () {
        if (!currentProductId) return;

        addToCartBtn.disabled = true;
        addToCartBtn.innerHTML = '<span class="material-icons animate-spin">sync</span> Adicionando...';

        axios.post('/carrinho', {
            produto_id: currentProductId,
            quantidade: qty,
            observacoes: observacoesInput.value
        })
        .then(response => {
            if (response.data.success) {
                if (window.updateCartCount) {
                    window.updateCartCount(response.data.cart_count);
                }

                addToCartBtn.classList.remove('bg-secondary');
                addToCartBtn.classList.add('bg-green-600');
                addToCartBtn.innerHTML = '<span class="material-icons">check_circle</span> Adicionado!';

                setTimeout(() => {
                    closeModal();
                    setTimeout(() => {
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('bg-green-600');
                        addToCartBtn.classList.add('bg-secondary');
                        addToCartBtn.innerHTML = '<span class="material-icons">shopping_bag</span> Adicionar ao Pedido';
                    }, 500);
                }, 1000);
            }
        })
        .catch(error => {
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<span class="material-icons">shopping_bag</span> Adicionar ao Pedido';

            if (error.response && error.response.status === 401) {
                alert('Você precisa estar logado para adicionar itens ao carrinho.');
                window.location.href = '/login';
            } else {
                alert('Erro ao adicionar produto ao carrinho. Tente novamente.');
                console.error(error);
            }
        });
    });
});