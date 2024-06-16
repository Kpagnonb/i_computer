document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', async (event) => {
            const id = event.target.getAttribute('data-id');
            const response = await fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await response.json();
            if (result.success) {
                // Mettre à jour le compteur du panier
                document.querySelector('.cart-count').textContent = result.cartCount;
            } else {
                alert('Erreur lors de l\'ajout au panier : ' + result.message);
            }
        });
    });

    // Récupérer et mettre à jour le compteur du panier au chargement de la page
    updateCartCount();

    async function updateCartCount() {
        const response = await fetch('get_cart_count.php');
        const result = await response.json();
        document.querySelector('.cart-count').textContent = result.cartCount;
    }
});
