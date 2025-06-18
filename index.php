

<?php 
	
	session_start();

	if ($_SESSION['LOGIN'] == 1){
		//header("Location: my-events.php");	
	}else{
		header("Location: sign-in.php");	
	}

	include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
	
	header('Content-type: html; charset=utf-8');
	
?>
	
<?php include './partials/layouts/layoutTop.php' ?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Hi <?php echo $_SESSION['USER']['NAME'] . '!'; ?></h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Home
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Blank Page</li>
			
        </ul>
    </div>
	

	
	<?php //print_r($_SESSION); ?>
</div>


<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
<script type="module">
	import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

	createChat({
		webhookUrl: 'https://merengueslabs.app.n8n.cloud/webhook/f4c60a80-d5d1-45be-82b3-66cf77e9e82e/chat',
		title: 'Prepenv',
		i18n: {
			locale: 'es',
			messages: {
				es: {
					welcome: {
						title: 'Bienvenido a Prepenv',
						subtitle: '¿Cómo puedo ayudarte hoy?',
						startButton: 'Comenzar conversación'
					},
					message: {
						placeholder: 'Escribe tu mensaje...',
						sendButton: 'Enviar'
					},
					history: {
						title: 'Historial',
						empty: 'No hay conversaciones recientes'
					},
					poweredBy: 'Desarrollado por'
				}
			}
		}
	});
</script>


<?php include './partials/layouts/layoutBottom.php' ?>