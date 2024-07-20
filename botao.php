<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Botão Flutuante</title>
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://adminlte.io/themes/v3/dist/css/adminlte.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-image: url('https://static.vecteezy.com/ti/vetor-gratis/p1/6998394-fundo-abstrato-azul-fundo-azul-design-fundo-futurista-abstrato-gratis-vetor.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            overflow: hidden;
        }
        .floating-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .floating-menu {
            position: fixed;
            bottom: 80px;
            left: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
        }
        .floating-menu a, .floating-menu button {
            padding: 10px 20px;
            text-decoration: none;
            color: black;
            display: flex;
            align-items: center;
            border: none;
            background: none;
            cursor: pointer;
        }
        .floating-menu a:hover, .floating-menu button:hover {
            background-color: #f0f0f0;
        }
        .floating-menu a i, .floating-menu button i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <button class="floating-button" onclick="toggleMenu()">
        <i class="fas fa-plus"></i>
    </button>
    <div class="floating-menu" id="floatingMenu">
        <a href="#">
            <i class="fas fa-user-plus"></i> + Cliente
        </a>
        <a href="#">
            <i class="fas fa-file-contract"></i> + Contrato
        </a>
        <button onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>Topo
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
</body>
</html>
