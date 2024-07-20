    <!-- Floating Button and Menu -->
    <button class="floating-button" onclick="toggleMenu()">
        <i class="fas fa-plus"></i>
    </button>
    <div class="floating-menu" id="floatingMenu">
        <a href="cadastrar_cliente.php">
            <i class="fas fa-user-plus"></i> + Cliente
        </a>
        <a href="adicionar_proposta.php">
            <i class="fas fa-file-contract"></i> + Contrato
        </a>
        <button onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i> Voltar ao Topo
        </button>
    </div>
    <script>
        function toggleMenu() {
            var menu = document.getElementById('floatingMenu');
            menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'flex' : 'none';
        }

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('click', function(event) {
            var menu = document.getElementById('floatingMenu');
            var isClickInside = menu.contains(event.target) || event.target.matches('.floating-button');
            if (!isClickInside) {
                menu.style.display = 'none';
            }
        });
    </script>