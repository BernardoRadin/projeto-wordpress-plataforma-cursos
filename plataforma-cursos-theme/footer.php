</div>
<?php wp_footer(); ?>
</body>
<script>
    lucide.createIcons();

    $(document).ready(function() {
        const $input = $('.input-search');
        const $cards = $('.div-cards .card');
        var encontrados = 0;

        const $mensagem = $('<p class="mensagem-vazia">Nenhum curso encontrado.</p>').hide();
        $('.div-cards').after($mensagem);

        function filtrarCursos() {
            const termo = $input.val().toLowerCase().trim();

            $cards.each(function() {
            const titulo = $(this).find('.titulo-card').text().toLowerCase();
            const descricao = $(this).find('.descricao').text().toLowerCase();

            if (titulo.includes(termo) || descricao.includes(termo)) {
                $(this).show();
                encontrados++;
            } else {
                $(this).hide();
            }
            });

            if (encontrados === 0 && termo !== '') {
                $mensagem.show();
                } else {
                $mensagem.hide();
            }
        }

        $('.button-search').on('click', filtrarCursos);

        $('#input-search').on('keyup', function(e) {
            if (e.key === 'Enter') $('#button-search').click();
        });

        // Voltar lista de cursos
        $input.on('keyup', function() {
            if ($(this).val().trim() === '') {
                $cards.show();
                $mensagem.hide();
            }
        });

        // Perfil
        $('.user, .narrow').on('click', function() {
            $('.user-menu').toggleClass('active');
        });

        //Menu
        $('#toggleMenu').on('click', function() {
            if ($(window).width() < 500) {
                $('#menuLateral').addClass('active');
            } else {
                $('#menuLateral').toggleClass('collapsed');
                $('#container').toggleClass('collapsed');

                if ($('#menuLateral').hasClass('collapsed')) {
                    $('#text-cursos').fadeOut(300);
                    $('#text-meus-cursos').fadeOut(300);
                } else {
                    $('#text-cursos').fadeIn(300);
                    $('#text-meus-cursos').fadeIn(300);
                }
            }
        });

        $('#closeMenu').on('click', function() {
            $('#menuLateral').removeClass('active');
        });        

        $(window).on('resize', function() {
            if ($(window).width() < 500) {
                $('#menuLateral').removeClass('collapsed').removeClass('active').hide();
            } else {
                $('#menuLateral').show();
            }
        });

    });
</script>
</html>
