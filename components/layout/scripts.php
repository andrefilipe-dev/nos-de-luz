
<!-----IMPORTAÇÃO DE BIBLIOTECAS JS----->
<script src="<?= BASE_URL ?>assets/vendor/jquery/dist/jquery.min.js"></script>

<script src="<?= BASE_URL ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="<?= BASE_URL ?>assets/vendor/jquery/src/jquery.mask.min.js"></script>

<script src="<?= BASE_URL ?>assets/vendor/aos/aos.js"></script>

<script src="<?= BASE_URL ?>assets/js/index.js"></script>



<script>

// ATUALIZAR ÍCONE DO CARRINHO

function updateCartBadge(quantity) {
    const count = Number(quantity ?? 0);

    document.querySelectorAll('.cart-badge').forEach(badge => {
        badge.textContent = count;
        badge.classList.toggle('d-none', count <= 0);
    });
}


// ADICIONAR PRODUTO AO CARRINHO

document.querySelectorAll('.btn-add-cart').forEach(button => {
    button.addEventListener('click', async () => {
        if (button.disabled) return;

        const originalText = 'Adicionar ao carrinho';

        button.disabled = true;
        button.textContent = 'Adicionando...';

        try {
            const response = await fetch(button.dataset.url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });

            // Verifica se o PHP redirecionou o usuário.
            if (response.redirected) {
                window.location.href = response.url;
                return;
            }

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Não foi possível adicionar o produto.'
                );
            }

            // Atualiza a notificação do carrinho.
            updateCartBadge(data.cartCount);

            // Atualiza o botão do produto.
            button.textContent = 'Adicionado ao carrinho';
            button.classList.remove('primary', 'secondary');
            button.classList.add('btn-cart-added');
            button.disabled = true;

        } catch (error) {
            console.error('Erro ao adicionar produto:', error);

            button.disabled = false;
            button.textContent = originalText;

            alert(error.message || 'Ocorreu um erro. Tente novamente.');
        }
    });
});

</script>
