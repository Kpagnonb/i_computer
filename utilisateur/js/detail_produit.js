document.addEventListener('DOMContentLoaded', () => {
    const images = document.querySelectorAll('.carousel-image');
    const thumbnails = document.querySelectorAll('.thumbnail');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const index = this.dataset.index;
            images.forEach((img, i) => {
                img.style.display = i == index ? 'block' : 'none';
            });
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            this.classList.add('active');
        });
    });

});

document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const type = this.dataset.type; // Assurez-vous que vous récupérez le type ici

        console.log("ID:", id);
        console.log("Type:", type);

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, type }) // Passez le type à add_to_cart.php
        })
        .then(response => response.json())
        .then(data => {
            console.log("Response data:", data);
            if (data.success) {
                document.querySelector('.cart-count').textContent = data.cartCount;
            } else {
                // 
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    });
});


