document.addEventListener('DOMContentLoaded', () => {
    // --- Configuration --- 
    const VIDEOS = [
        { id: 1, title: "Módulo 1: Introducción", src: "videos/1.mp4", duration: "5:00" },
        { id: 2, title: "Módulo 2: Conceptos Clave", src: "videos/2.mp4", duration: "7:30" },
        { id: 3, title: "Módulo 3: Temas Avanzados", src: "videos/3.mp4", duration: "10:15" },
        // Add more videos here. Ensure video files exist in a 'videos' folder
        // or provide full URLs. Example: { id: 4, title: "Module 4", src: "videos/video4.mp4" }
    ];
    const QUIZ_QUESTIONS_PATH = "questions.json"; // Path to your quiz JSON file
    const API_BASE_URL = ""; // Si los PHP están en el mismo dir, dejar vacío. Si no, ej: "api/"   
    const LOGIN_URL = `${API_BASE_URL}login.php`;
    const REGISTER_URL = `${API_BASE_URL}register.php`;
    const LOGOUT_URL = `${API_BASE_URL}logout.php`;
    const GET_PROGRESS_URL = `${API_BASE_URL}get_video_progress.php`;
    const UPDATE_PROGRESS_URL = `${API_BASE_URL}update_video_progress.php`;
    const CHECK_SESSION_URL = `${API_BASE_URL}check_session.php`;
    const SAVE_QUIZ_RESULT_URL = `${API_BASE_URL}save_quiz_result.php`;
    const GET_QUIZ_RESULT_URL = `${API_BASE_URL}get_quiz_result.php`;
    const CREATE_CERTIFICATE_URL = `${API_BASE_URL}create_and_save_certificate.php`;
    const FORGOT_PASSWORD_URL = `${API_BASE_URL}forgot_password.php`;
    const UPDATE_PASSWORD_URL = `${API_BASE_URL}update_password.php`;

    // --- DOM Elements ---
    const loginPage = document.getElementById('login-page');
    const registerPage = document.getElementById('register-page');
    const coursePage = document.getElementById('course-page');
    const quizPage = document.getElementById('quiz-page');
    const forgotPasswordPage = document.getElementById('forgot-password-page');
    const themeToggleButton = document.getElementById('theme-toggle-button');

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const loginError = document.getElementById('login-error');
    const registerError = document.getElementById('register-error');
    const registerSuccess = document.getElementById('register-success');
    const logoutButton = document.getElementById('logout-button');
    const forgotPasswordForm = document.getElementById('forgot-password-form');
    const forgotPasswordError = document.getElementById('forgot-password-error');
    const forgotPasswordSuccess = document.getElementById('forgot-password-success');


    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');

    const videoPlayer = document.getElementById('course-video');
    const currentVideoTitleEl = document.getElementById('current-video-title');
    const courseMainTitleEl = document.getElementById('course-main-title');
    const videoListUl = document.getElementById('video-list');
    const videoError = document.getElementById('video-error');

    const quizForm = document.getElementById('quiz-form');
    const submitQuizButton = document.getElementById('submit-quiz-button');
    const quizResultsEl = document.getElementById('quiz-results');
    const prevQuestionButton = document.getElementById('prev-question-button');
    const nextQuestionButton = document.getElementById('next-question-button');
    const questionCounterEl = document.getElementById('question-counter');
    const quizContentEl = document.getElementById('quiz-content'); // Added
    const quizLockedMessageEl = document.getElementById('quiz-locked-message');

    const showRegisterLink = document.getElementById('show-register-link');
    const showLoginLink = document.getElementById('show-login-link');
    const showForgotPasswordLink = document.getElementById('show-forgot-password-link');
    const backToLoginLink = document.getElementById('back-to-login-link');

    // --- Application State ---
    let currentUser = null; // Simulated user
    // let watchedVideos = new Set(); // This will be replaced by userVideoProgress
    let userVideoProgress = {}; // Stores progress fetched from DB: { videoId: { status: 'watched', last_watched_seconds: 120 }}
    let quizData = [];
    let currentQuestionIndex = 0;
    let userAnswers = {}; // To store answers like {0: "answerValue", 1: "anotherAnswerValue"}
    let currentVideoId = null;
    let lastReportedTime = 0; // For preventing fast-forward spam
    const PROGRESS_SAVE_INTERVAL = 15000; // Save video progress every 15 seconds
    let progressSaveTimer = null;

    // --- Functions ---

    // Theme Functions
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
            document.body.classList.remove('light-theme');
            if (themeToggleButton) themeToggleButton.textContent = 'Tema Claro';
        } else {
            document.body.classList.remove('dark-theme');
            document.body.classList.add('light-theme');
            if (themeToggleButton) themeToggleButton.textContent = 'Tema Oscuro';
        }
    }

    function toggleTheme() {
        if (document.body.classList.contains('dark-theme')) {
            applyTheme('light');
            localStorage.setItem('theme', 'light');
        } else {
            applyTheme('dark');
            localStorage.setItem('theme', 'dark');
        }
    }

    function loadTheme() {
        const preferredTheme = localStorage.getItem('theme') || 'dark'; // Default to dark
        applyTheme(preferredTheme);
    }

    // Helper function to shuffle an array (Fisher-Yates shuffle)
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]]; // Swap elements
        }
    }




    function showPage(pageId) {
        document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
        const pageToShow = document.getElementById(pageId);
        if (pageToShow) {
            pageToShow.classList.add('active');
        }
    }

    async function handleLogin(event) {
        event.preventDefault();
        const email = loginForm.email.value;
        const password = loginForm.password.value;
        const licenseKey = loginForm.licenseKey.value; // Get license key

        if (!email || !password || !licenseKey) {
            loginError.textContent = 'Por favor, ingrese correo, contraseña y clave de licencia.';
            return;
        }
        loginError.textContent = 'Iniciando sesión...';

        try {
            const response = await fetch(LOGIN_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, licenseKey }) // Send licenseKey
            });
            const data = await response.json();

            if (data.success && data.user) {
                currentUser = data.user;
                loginError.textContent = '';
                // localStorage.setItem('currentUser', JSON.stringify(currentUser)); // Session handles this now
                await loadUserQuizResultFromDB(); // Load quiz result
                await loadUserProgressFromDB(); // Load progress from DB
                initializeCoursePage();
                showPage('course-page');
            } else {
                loginError.textContent = data.message || 'Error en el inicio de sesión.';
            }
        } catch (error) {
            console.error('Login API error:', error);
            loginError.textContent = 'No se pudo conectar al servidor. Inténtelo más tarde.';
        }
    }

    async function handleRegister(event) {
        event.preventDefault();
        const fullName = registerForm.fullName.value;
        const phone = registerForm.phone.value;
        const email = registerForm.email.value;
        const password = registerForm.password.value;

        registerError.textContent = '';
        registerSuccess.textContent = '';
        registerSuccess.style.display = 'none';

        // Client-side validation (basic, server does more)
        if (!fullName || !phone || !email || !password) {
            registerError.textContent = 'Todos los campos son requeridos.';
            return;
        }
        if (!/^\d{10}$/.test(phone)) {
            registerError.textContent = 'Teléfono debe ser de 10 dígitos.';
            return;
        }
        if (password.length < 6) {
            registerError.textContent = 'Contraseña debe tener al menos 6 caracteres.';
            return;
        }

        try {
            const response = await fetch(REGISTER_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ fullName, phone, email, password })
            });
            const data = await response.json();

            if (data.success) {
                registerSuccess.textContent = data.message + " Por favor, inicia sesión.";
                registerSuccess.style.display = 'block';
                registerForm.reset();
                // setTimeout(() => showPage('login-page'), 2000); // Optionally switch to login
            } else {
                registerError.textContent = data.message || 'Error en el registro.';
            }
        } catch (error) {
            console.error('Register API error:', error);
            registerError.textContent = 'No se pudo conectar al servidor. Inténtelo más tarde.';
        }
    }

    async function handleForgotPassword(event) {
        event.preventDefault();
        const email = forgotPasswordForm.email.value;

        forgotPasswordError.textContent = '';
        forgotPasswordSuccess.textContent = '';
        forgotPasswordSuccess.style.display = 'none';

        if (!email) {
            forgotPasswordError.textContent = 'Por favor, ingrese su correo electrónico.';
            return;
        }
        forgotPasswordError.textContent = 'Procesando...'; // Indicate activity

        try {
            const response = await fetch(FORGOT_PASSWORD_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email })
            });
            const data = await response.json();

            // Always show a generic success message for security (to prevent email enumeration)
            forgotPasswordSuccess.textContent = data.message || 'Si tu correo está registrado, recibirás un enlace para restablecer tu contraseña.';
            forgotPasswordSuccess.style.display = 'block';
            forgotPasswordError.textContent = ''; // Clear any "Procesando..."
            forgotPasswordForm.reset();

        } catch (error) {
            console.error('Forgot Password API error:', error);
            forgotPasswordError.textContent = 'No se pudo conectar al servidor. Inténtelo más tarde.';
            forgotPasswordSuccess.style.display = 'none';
        }
    }

    async function handleActualResetPassword(event) {
        event.preventDefault();
        
        const form = document.getElementById('actual-reset-password-form');
        const tokenField = document.getElementById('reset-token-field');
        const errorEl = document.getElementById('reset-form-error');
        const successEl = document.getElementById('reset-form-success');
        const successActionsEl = document.getElementById('reset-success-actions');

        if (!form || !tokenField || !errorEl || !successEl) {
            console.error("Elementos del formulario de restablecimiento de contraseña no encontrados en la página actual.");
            // Si esta función se llama desde una página donde no existen, simplemente retorna.
            return;
        }

        const token = tokenField.value;
        const newPassword = form.new_password.value;
        const confirmPassword = form.confirm_password.value;

        errorEl.textContent = '';
        successEl.textContent = '';
        successEl.style.display = 'none';

        if (!token || !newPassword || !confirmPassword) {
            errorEl.textContent = 'Todos los campos son requeridos.';
            return;
        }
        if (newPassword.length < 6) {
            errorEl.textContent = 'La nueva contraseña debe tener al menos 6 caracteres.';
            return;
        }
        if (newPassword !== confirmPassword) {
            errorEl.textContent = 'Las contraseñas no coinciden.';
            return;
        }
        errorEl.textContent = 'Procesando...';

        try {
            const response = await fetch(UPDATE_PASSWORD_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token, new_password: newPassword, confirm_password: confirmPassword })
            });
            const data = await response.json();

            if (data.success) {
                successEl.textContent = data.message; // Mensaje como "Contraseña actualizada exitosamente."
                successEl.style.display = 'block';
                errorEl.textContent = '';
                form.reset();
                form.querySelectorAll('input, button').forEach(el => el.disabled = true);

                if (successActionsEl) {
                    const goToLoginButton = document.createElement('button');
                    goToLoginButton.textContent = 'Ir a Iniciar Sesión';
                    goToLoginButton.type = 'button';
                    goToLoginButton.onclick = () => { window.location.href = 'index.html'; };
                    successActionsEl.innerHTML = ''; // Limpiar acciones previas
                    successActionsEl.appendChild(goToLoginButton);
                    successActionsEl.style.display = 'block';
                }
            } else {
                errorEl.textContent = data.message || 'Error al restablecer la contraseña.';
                successEl.style.display = 'none';
            }
        } catch (error) {
            console.error('Update Password API error:', error);
            errorEl.textContent = 'No se pudo conectar al servidor. Inténtelo más tarde.';
            successEl.style.display = 'none';
        }
    }

    async function handleLogout() {
        await fetch(LOGOUT_URL); // Inform server to destroy session
        currentUser = null;
        userVideoProgress = {}; // Clear client-side progress
        // localStorage.removeItem('currentUser'); // No longer primary way to store user

        currentVideoId = null;
        videoPlayer.pause();
        videoPlayer.src = "";
        currentVideoTitleEl.textContent = "Selecciona un video para comenzar";
        loginForm.reset();
        registerForm.reset();
        registerError.textContent = '';
        registerSuccess.textContent = '';
        forgotPasswordForm.reset();
        forgotPasswordError.textContent = '';
        forgotPasswordSuccess.textContent = '';
        registerSuccess.style.display = 'none';
        showPage('login-page');
        updateVideoList(); 
        // Reset quiz state on logout
        updateProgressBarDisplay();
        quizForm.innerHTML = ''; 
        quizResultsEl.innerHTML = '';
        submitQuizButton.style.display = 'none';
        document.getElementById('quiz-navigation').style.display = 'none';
        const proceedButton = document.getElementById('proceed-to-quiz-button');
        if(proceedButton) proceedButton.remove();
    }

    function initializeCoursePage() {
        if (!currentUser) return;
        if (courseMainTitleEl && currentUser.fullName) {
            courseMainTitleEl.textContent = `Bienvenido al Curso, ${currentUser.fullName}!`;
        }
        // Progress is already loaded by loadUserProgressFromDB before calling this
        populateVideoList();
        updateProgressBarDisplay();
        checkIfQuizCanBeUnlocked();
        // Attempt to load the first playable video
        const firstUnwatchedOrInProgressVideo = VIDEOS.find(v => 
            !userVideoProgress[v.id] || userVideoProgress[v.id].status !== 'watched'
        );
        if (firstUnwatchedOrInProgressVideo) {
            const previousVideoIndex = VIDEOS.findIndex(v => v.id === firstUnwatchedOrInProgressVideo.id) - 1;
            if (previousVideoIndex < 0 || (userVideoProgress[VIDEOS[previousVideoIndex].id] && userVideoProgress[VIDEOS[previousVideoIndex].id].status === 'watched')) {
                 loadVideo(firstUnwatchedOrInProgressVideo.id, firstUnwatchedOrInProgressVideo.src, firstUnwatchedOrInProgressVideo.title);
            }
        }
    }

    async function loadUserProgressFromDB() {
        if (!currentUser) return;
        try {
            const response = await fetch(GET_PROGRESS_URL);
            const data = await response.json();
            if (data.success) {
                userVideoProgress = data.progress || {};
            } else {
                console.error("Error fetching progress:", data.message);
                userVideoProgress = {};
            }
        } catch (error) {
            console.error("API error fetching progress:", error);
            userVideoProgress = {};
        }
    }

    async function loadUserQuizResultFromDB() {
        if (!currentUser || !currentUser.id) {
            currentUser.quizResult = null; 
            return;
        }
        try {
            const response = await fetch(GET_QUIZ_RESULT_URL);
            const data = await response.json();
            if (data.success) {
                currentUser.quizResult = data.quizResult; // This can be an object or null
            } else {
                console.error("Error fetching quiz result:", data.message);
                currentUser.quizResult = null;
            }
        } catch (error) {
            console.error("API error fetching quiz result:", error);
            currentUser.quizResult = null;
        }
    }

    function populateVideoList() {
        videoListUl.innerHTML = '';
        let previousVideoWatched = true; // First video is always accessible initially

        VIDEOS.forEach((video, index) => {
            const li = document.createElement('li');
            li.textContent = `${video.title} (${video.duration || 'N/A'})`;
            li.dataset.videoId = video.id;
            li.dataset.videoSrc = video.src;

            const progress = userVideoProgress[video.id];

            if (progress && progress.status === 'watched') {
                li.classList.add('watched');
            } else if (progress && progress.status === 'in_progress') {
                li.classList.add('in-progress'); // Optional: style for in-progress
            }

            if (previousVideoWatched) {
                li.classList.add('accessible');
                li.addEventListener('click', () => {
                    loadVideo(video.id, video.src, video.title);
                    document.querySelectorAll('#video-list li').forEach(item => item.classList.remove('active-video'));
                    li.classList.add('active-video');
                });
            } else {
                li.classList.add('locked');
                li.title = "Debes completar el video anterior.";
            }
            
            // For the next iteration, check if the current video was watched
            previousVideoWatched = (progress && progress.status === 'watched');

            videoListUl.appendChild(li);
        });
    }

    function loadVideo(id, src, title) {
        currentVideoId = id;
        videoPlayer.src = src;
        currentVideoTitleEl.textContent = title;
        videoPlayer.currentTime = userVideoProgress[id]?.last_watched_seconds || 0;
        videoError.textContent = '';
        videoPlayer.play().catch(e => {
            console.error("Error playing video:", e);
            videoError.textContent = "No se pudo reproducir el video. Asegúrese de que el archivo exista y el formato sea compatible.";
        });
        lastReportedTime = videoPlayer.currentTime; // Initialize for anti-seek

        // Clear previous timer and start new one for saving progress
        if (progressSaveTimer) clearInterval(progressSaveTimer);
        progressSaveTimer = setInterval(saveCurrentVideoProgress, PROGRESS_SAVE_INTERVAL);
    }



    function updateVideoList() {
         document.querySelectorAll('#video-list li').forEach(li => {
            const videoId = parseInt(li.dataset.videoId);
            if (userVideoProgress[videoId] && userVideoProgress[videoId].status === 'watched') {
                li.classList.add('watched');
            } else {
                li.classList.remove('watched');
            }
            if (currentVideoId === videoId) {
                li.classList.add('active-video');
            } else {
                li.classList.remove('active-video');
            }
        });
    }

    function updateProgressBarDisplay() {
        const totalVideos = VIDEOS.length;
        const watchedCount = Object.values(userVideoProgress).filter(p => p.status === 'watched').length;
        const percentage = totalVideos > 0 ? (watchedCount / totalVideos) * 100 : 0;

        progressBar.style.width = `${percentage}%`;
        progressText.textContent = `${watchedCount}/${totalVideos} videos vistos`;
        // updateVideoList(); // This might be redundant if called elsewhere, or could be needed
    }

    async function saveCurrentVideoProgress(status = 'in_progress') {
        if (!currentUser || currentVideoId === null || videoPlayer.paused && status === 'in_progress') return;

        const currentTime = videoPlayer.currentTime;
        // Only update if significant progress or status change
        const existingProgress = userVideoProgress[currentVideoId];
        if (existingProgress && existingProgress.status === status && Math.abs(currentTime - (existingProgress.last_watched_seconds || 0)) < 5 && status !== 'watched') {
            return; // Avoid too frequent updates for minor time changes unless marking as watched
        }

        try {
            await fetch(UPDATE_PROGRESS_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    video_internal_id: currentVideoId,
                    status: status,
                    last_watched_seconds: currentTime
                })
            });
            // Update local state
            userVideoProgress[currentVideoId] = {
                ...(userVideoProgress[currentVideoId] || {}), // keep other potential fields
                video_internal_id: currentVideoId,
                status: status,
                last_watched_seconds: Math.max(userVideoProgress[currentVideoId]?.last_watched_seconds || 0, currentTime)
            };
            if (status === 'watched') {
                lastReportedTime = 0; // Reset for next video
                if (progressSaveTimer) clearInterval(progressSaveTimer);
            }
            updateProgressBarDisplay();
            populateVideoList(); // Re-render list to unlock next video
            checkIfQuizCanBeUnlocked();
        } catch (error) {
            console.error("Error saving progress:", error);
        }
    }


    async function handleVideoEnded() {
        if (currentVideoId !== null && currentUser) {
            await saveCurrentVideoProgress('watched'); // Mark as watched in DB

            const currentIndex = VIDEOS.findIndex(v => v.id === currentVideoId);
            const allWatched = VIDEOS.every(v => userVideoProgress[v.id]?.status === 'watched');
            if (currentIndex === VIDEOS.length - 1 && allWatched) {
                currentVideoTitleEl.textContent = "¡Todos los videos vistos! Proceda al cuestionario.";
            }
        }
    }

    function handleVideoTimeUpdate() {
        if (!videoPlayer.seeking) {
            lastReportedTime = Math.max(lastReportedTime, videoPlayer.currentTime);
        }
    }

    function handleVideoSeeking() {
        if (videoPlayer.currentTime > lastReportedTime + 5) { // Allow small jumps, prevent large ones
            videoPlayer.currentTime = lastReportedTime; // Snap back
        }
    }

    function checkIfQuizCanBeUnlocked() {
        const videoListContainer = document.getElementById('video-list-container');
        let proceedButton = document.getElementById('proceed-to-quiz-button');
        let downloadCertButton = document.getElementById('download-certificate-button-main'); // For course page
        let quizStatusMessageEl = document.getElementById('quiz-status-message');

        // Remove existing button and message to recreate cleanly
        if (proceedButton) proceedButton.remove();
        if (downloadCertButton) downloadCertButton.remove();
        if (quizStatusMessageEl) quizStatusMessageEl.remove();

        // Create message element if it doesn't exist in the container
        quizStatusMessageEl = document.createElement('p');
        quizStatusMessageEl.id = 'quiz-status-message';
        quizStatusMessageEl.style.marginTop = '10px';
        // quizStatusMessageEl will be appended only if there's a message to show.

        const allVideosWatched = VIDEOS.length > 0 && VIDEOS.every(v => userVideoProgress[v.id]?.status === 'watched');
        let quizApprovedWithCert = false; // Flag to track if quiz is approved and certificate exists

        if (currentUser && currentUser.quizResult) {
            const result = currentUser.quizResult;
            const score = parseFloat(result.percentage_score);

            if (score > 59 && result.certificate_path) { // Approved and certificate exists
                quizApprovedWithCert = true; // Set the flag
                quizStatusMessageEl.textContent = `¡Felicidades! Ya has aprobado este curso (Puntuación: ${score.toFixed(2)}%). Tu certificado está disponible.`;
                quizStatusMessageEl.className = 'success-message';
                if (videoListContainer) videoListContainer.appendChild(quizStatusMessageEl);

                downloadCertButton = document.createElement('button');
                downloadCertButton.id = 'download-certificate-button-main';
                downloadCertButton.textContent = 'Descargar Certificado';
                downloadCertButton.type = 'button';
                downloadCertButton.style.marginTop = '10px';
                downloadCertButton.onclick = function() { window.open(result.certificate_path, '_blank'); };
                if (videoListContainer) videoListContainer.appendChild(downloadCertButton);

                if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'none';

            } else if (score <= 59) { // Attempted and failed
                quizStatusMessageEl.textContent = 'Ya has intentado este cuestionario y no lo aprobaste. No se permiten más intentos.';
                quizStatusMessageEl.className = 'error-message';
                if (videoListContainer) videoListContainer.appendChild(quizStatusMessageEl);
                if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'none';
                return; // Do not show "Proceed to Quiz" button if failed and no more attempts
            }
            // If approved but no certificate_path, or other quizResult states, fall through to allVideosWatched check
        }

        // Show "Proceed to Quiz" button only if all videos are watched AND
        // the quiz is not already approved with a certificate, AND
        // the user hasn't failed with no more attempts (which is handled by the 'return' above).
        if (allVideosWatched && !quizApprovedWithCert) {
            proceedButton = document.createElement('button'); // Recreate
            proceedButton.id = 'proceed-to-quiz-button';
            proceedButton.type = 'button';
            proceedButton.textContent = (currentUser && currentUser.quizResult && currentUser.quizResult.percentage_score !== undefined) ? 'Ver/Revisar Cuestionario' : 'Ir al Cuestionario';
            proceedButton.style.marginTop = '10px';
            if (videoListContainer) videoListContainer.appendChild(proceedButton);
            
            proceedButton.onclick = () => {
                loadQuiz();
                showPage('quiz-section'); // Correct ID for the quiz page section
            };
            proceedButton.style.display = 'block';
            if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'none';
        } else {
            // Videos not watched, default quizLockedMessageEl will be shown by showPage logic or CSS
            if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'block';
            // Ensure no other status message is shown if videos are locked and quiz not yet accessible
            if (quizStatusMessageEl && quizStatusMessageEl.parentNode && !(currentUser && currentUser.quizResult)) {
                quizStatusMessageEl.remove();
            }
        }
    }

    async function loadQuiz() {
        if (currentUser && currentUser.quizResult && parseFloat(currentUser.quizResult.percentage_score) <= 59) {
            quizForm.innerHTML = `<p class="error-message">Ya has intentado este cuestionario y no lo aprobaste. No se permiten más intentos.</p>`;
            document.getElementById('quiz-navigation').style.display = 'none';
            submitQuizButton.style.display = 'none';
            if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'none'; 
            if (quizContentEl) quizContentEl.style.display = 'block'; 
            return;
        }
        try {
            // Quiz is being attempted, so hide "locked" message and show quiz content area
            if (quizLockedMessageEl) quizLockedMessageEl.style.display = 'none';
            if (quizContentEl) quizContentEl.style.display = 'block';

            quizForm.innerHTML = '<p>Cargando preguntas...</p>'; // Loading indicator

            const response = await fetch(QUIZ_QUESTIONS_PATH);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status} al cargar ${QUIZ_QUESTIONS_PATH}`);
            }
            quizData = await response.json();

            // Check if quizData is empty or not an array as expected
            if (!quizData || !Array.isArray(quizData) || quizData.length === 0) {
                throw new Error("El archivo de preguntas está vacío o no tiene el formato esperado (debe ser un array de preguntas).");
            }

            // Shuffle options for each question once when quizData is loaded
            quizData.forEach(question => {
                if (question.options && Array.isArray(question.options)) {
                    shuffleArray(question.options);
                }
            });

            currentQuestionIndex = 0;
            userAnswers = {};
            displayCurrentQuestion(); // New function to show one question
            
            document.getElementById('quiz-navigation').style.display = quizData.length > 0 ? 'block' : 'none';
            // submitQuizButton visibility is now handled by displayCurrentQuestion
            // submitQuizButton.style.display = 'block'; // Removed
            // submitQuizButton.disabled = false; // Will be enabled when appropriate
            quizResultsEl.innerHTML = '';
        } catch (error) { // Catch any error during the process
            console.error("Failed to load quiz:", error);
            const errorMessageText = error.message || String(error); // Get a more reliable error string
            // Display error within the quizForm, inside the now visible quizContentEl
            quizForm.innerHTML = `<p class="error-message">Error al cargar el cuestionario: ${errorMessageText}. Por favor, revise la consola para más detalles o contacte al administrador.</p>`;
            // quizLockedMessageEl should remain hidden as the quiz isn't "locked" by prerequisites, but failed to load.
            document.getElementById('quiz-navigation').style.display = 'none';
            submitQuizButton.style.display = 'none';
        }
    }

    function displayCurrentQuestion() {
        if (quizData.length === 0) {
            quizForm.innerHTML = "<p>No hay preguntas disponibles.</p>";
            document.getElementById('quiz-navigation').style.display = 'none';
            submitQuizButton.style.display = 'none';
            return;
        }

        quizForm.innerHTML = '';
        const q = quizData[currentQuestionIndex];

        const questionDiv = document.createElement('div');
        questionDiv.classList.add('quiz-question');
        // Use currentQuestionIndex for numbering, not the original index if questions were ever reordered
        questionDiv.innerHTML = `<p>${currentQuestionIndex + 1}. ${q.question}</p>`;

        const optionsDiv = document.createElement('div');
        optionsDiv.classList.add('quiz-options');

        // Options are now pre-shuffled when quizData is loaded,
        // so we use them directly here.

        q.options.forEach(option => {
            const label = document.createElement('label');
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = `question${currentQuestionIndex}`; // Unique name for the current question's options
            radio.value = option;
            radio.required = true; // HTML5 validation, JS will also check
            if (userAnswers[currentQuestionIndex] === option) {
                radio.checked = true; // Pre-select if already answered
            }
            label.appendChild(radio);
            label.appendChild(document.createTextNode(option));
            optionsDiv.appendChild(label);
        });
        questionDiv.appendChild(optionsDiv);
        quizForm.appendChild(questionDiv);

        // Update navigation
        questionCounterEl.textContent = `Pregunta ${currentQuestionIndex + 1} / ${quizData.length}`;
        prevQuestionButton.style.display = currentQuestionIndex > 0 ? 'inline-block' : 'none';
        nextQuestionButton.style.display = currentQuestionIndex < quizData.length - 1 ? 'inline-block' : 'none';
        submitQuizButton.style.display = currentQuestionIndex === quizData.length - 1 ? 'block' : 'none';
        submitQuizButton.disabled = false; // Enable submit button on last question
    }

    function saveCurrentAnswer() {
        const selectedOption = quizForm.querySelector(`input[name="question${currentQuestionIndex}"]:checked`);
        if (selectedOption) {
            userAnswers[currentQuestionIndex] = selectedOption.value;
            return true;
        }
        // If no option is selected, remove any previous answer for this question
        // delete userAnswers[currentQuestionIndex]; // Or keep it, depending on desired behavior for "unanswered"
        return false; // No answer selected
    }

    function handleNextQuestion() {
        if (saveCurrentAnswer()) { // Question must be answered to proceed
            if (currentQuestionIndex < quizData.length - 1) {
                currentQuestionIndex++;
                displayCurrentQuestion();
            }
        } else {
            alert('Por favor, seleccione una respuesta para continuar.');
        }
    }

    function handlePrevQuestion() {
        saveCurrentAnswer(); // Save current answer even if not mandatory for going back
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            displayCurrentQuestion();
        }
    }

    async function handleSubmitQuiz() { // Convertida a async
        // --- Inicio: Lógica del indicador de carga ---
        const originalButtonText = submitQuizButton.innerHTML;
        submitQuizButton.disabled = true;
        submitQuizButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
        // --- Fin: Lógica del indicador de carga ---
        saveCurrentAnswer(); // Ensure the last question's answer is saved
        let score = 0;
        let allAnswered = true;
        for (let i = 0; i < quizData.length; i++) {
            if (userAnswers[i] === undefined) {
                allAnswered = false;
                break;
            }
            if (userAnswers[i] === quizData[i].answer) {
                score++;
            }
        }

        if (!allAnswered) {
            quizResultsEl.textContent = 'Por favor, responda todas las preguntas antes de enviar.';
            quizResultsEl.className = 'error-message';
            // Restaurar botón si hay error de validación
            submitQuizButton.disabled = false;
            submitQuizButton.innerHTML = originalButtonText;
            return;
        }

        const percentage = (score / quizData.length) * 100;
        quizResultsEl.innerHTML = `<h3>Resultados del Cuestionario</h3>
                                   <p>Obtuviste ${score} de ${quizData.length} (${percentage.toFixed(2)}%).</p>`;

        let certificatePath = null; // Para almacenar la ruta del certificado

        try {
            // 1. Guardar el resultado del quiz en la base de datos PRIMERO.
            // certificatePath se pasará como null inicialmente, ya que aún no se ha generado.
            // El backend (create_and_save_certificate.php) decidirá si se genera o no.
            if (currentUser && currentUser.id) {
                await saveQuizResultToDB(score, quizData.length, percentage, null); // Guardar resultado sin ruta de certificado aún
                currentUser.quizResult = { // Actualizar estado local preliminarmente
                    score: score, total_questions: quizData.length,
                    percentage_score: parseFloat(percentage.toFixed(2)),
                    certificate_path: null, // Se actualizará si el backend lo genera
                    submission_date: new Date().toISOString()
                };
            }

            // 2. Ahora llamar a create_and_save_certificate.php
            // Este script leerá el resultado recién guardado para determinar la aprobación
            // y enviar el correo correspondiente (aprobado con certificado o no aprobado).
            const certResponse = await fetch(CREATE_CERTIFICATE_URL, { method: 'POST' });
            const certData = await certResponse.json();

            if (percentage > 59) { // Updated condition: pass if greater than 59%
                quizResultsEl.className = 'success';
                quizResultsEl.innerHTML += "<p>¡Felicidades, has aprobado!</p>";

                if (certData.success && certData.filePath) { // Certificado generado y correo de éxito enviado (o intentado)
                    certificatePath = certData.filePath; // Guardamos la ruta para el botón de descarga
                    quizResultsEl.innerHTML += `<p>${certData.message || 'Certificado procesado.'} ${certData.email_status || ''}</p>`;
                    
                    const downloadButton = document.createElement('button');
                    downloadButton.id = 'download-certificate-button';
                    downloadButton.textContent = 'Descargar Certificado';
                    downloadButton.type = 'button';
                    downloadButton.style.marginTop = '10px';
                    downloadButton.onclick = function() { window.open(certificatePath, '_blank'); };
                    quizResultsEl.appendChild(downloadButton);
                    console.log("Certificado generado en:", certificatePath);
                    // Actualizar la ruta del certificado en el estado local si se generó
                    if (currentUser && currentUser.quizResult) {
                        currentUser.quizResult.certificate_path = certificatePath;
                    }

                } else if (!certData.success && certData.message.includes("no fue aprobado")) { // Caso de no aprobación manejado por el PHP
                    quizResultsEl.className = 'failure'; // Asegurar clase de fallo
                    quizResultsEl.innerHTML += `<p>${certData.message} ${certData.email_status || ''}</p>`;
                    // No se genera certificado, certificatePath sigue siendo null
                }
                else { // Otro error durante la generación del certificado o envío de correo de éxito
                    quizResultsEl.innerHTML += `<p class="error-message">Error en el proceso del certificado: ${certData.message || 'Error desconocido.'} ${certData.email_status || ''}</p>`;
                    console.error("Error en proceso de certificado:", certData.message, certData.email_status);
                }
            } else { // El usuario no aprobó según el cálculo de JS (percentage <= 59)
                quizResultsEl.className = 'failure';
                quizResultsEl.innerHTML += "<p>No aprobaste. Por favor, revisa el material.</p>";
                // El script PHP create_and_save_certificate.php ya fue llamado y habrá manejado
                // el envío del correo de no aprobación basado en el resultado que leyó de la BD.
                // Solo mostramos el mensaje que retornó.
                if (certData && certData.message) {
                     quizResultsEl.innerHTML += `<p>${certData.message} ${certData.email_status || ''}</p>`;
                }
            }
        } catch (error) {
            quizResultsEl.innerHTML += `<p class="error-message">Ocurrió un error de conexión o procesamiento. Inténtalo de nuevo.</p>`;
            console.error("Error en handleSubmitQuiz:", error);
        } finally {
            // --- Inicio: Restaurar botón ---
            submitQuizButton.disabled = true; // Mantener deshabilitado después del intento
            submitQuizButton.innerHTML = originalButtonText; // O un texto como "Resultados Mostrados"
            if (percentage <= 59 || certificatePath === null && percentage > 59) { // Si no aprobó o aprobó pero no hay certificado
                // No mostrar botón de descarga, y posiblemente ocultar/cambiar el botón de submit
            }
            if (percentage <= 59) {
                prevQuestionButton.style.display = 'none';
                nextQuestionButton.style.display = 'none';
                questionCounterEl.style.display = 'none';
                checkIfQuizCanBeUnlocked(); // Actualiza el estado en la página del curso
            }
            // --- Fin: Restaurar botón ---
        }
    }

    async function saveQuizResultToDB(score, totalQuestions, percentage, certificatePath = null) {
        if (!currentUser || !currentUser.id) {
            console.error("No hay usuario logueado para guardar el resultado del cuestionario.");
            return;
        }
        try {
            const response = await fetch(SAVE_QUIZ_RESULT_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    // user_id is handled by the session on the server-side for security
                    score: score,
                    total_questions: totalQuestions,
                    percentage: parseFloat(percentage.toFixed(2)), // Ensure it's a number
                    certificate_path: certificatePath
                })
            });
            const data = await response.json();
            if (data.success) {
                console.log("Resultado del cuestionario guardado exitosamente.");
            } else {
                console.error("Error al guardar el resultado del cuestionario:", data.message || "Error desconocido del servidor.");
            }
        } catch (error) {
            console.error("Error de red o API al guardar el resultado del cuestionario:", error);
        }
    }

    // --- Event Listeners ---
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
    if (forgotPasswordForm) forgotPasswordForm.addEventListener('submit', handleForgotPassword);
    if (logoutButton) logoutButton.addEventListener('click', handleLogout);

    if (videoPlayer) {
        videoPlayer.addEventListener('ended', handleVideoEnded);
        videoPlayer.addEventListener('timeupdate', handleVideoTimeUpdate);
        videoPlayer.addEventListener('seeking', handleVideoSeeking);
        videoPlayer.addEventListener('error', () => {
            if (videoPlayer.src && videoPlayer.src !== window.location.href && videoError) { // Added check for videoError
                 videoError.textContent = `Error al cargar el video. Compruebe si el archivo "${videoPlayer.src.split('/').pop()}" existe en la carpeta 'videos' y tiene un formato compatible.`;
            }
        });
    }
    if (submitQuizButton) submitQuizButton.addEventListener('click', handleSubmitQuiz);
    if (prevQuestionButton) prevQuestionButton.addEventListener('click', handlePrevQuestion);
    if (nextQuestionButton) nextQuestionButton.addEventListener('click', handleNextQuestion);

    // Listener for the form on reset_password_form.php
    const actualResetPasswordFormGlobal = document.getElementById('actual-reset-password-form');
    if (actualResetPasswordFormGlobal) {
        actualResetPasswordFormGlobal.addEventListener('submit', handleActualResetPassword);
    }

    if (showRegisterLink) showRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        showPage('register-page');
    });
    if (showLoginLink) showLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        showPage('login-page');
    });
    if (showForgotPasswordLink) showForgotPasswordLink.addEventListener('click', (e) => {
        e.preventDefault();
        showPage('forgot-password-page');
    });
    if (backToLoginLink) backToLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        showPage('login-page');
    });
    
    if (themeToggleButton) {
        themeToggleButton.addEventListener('click', toggleTheme);
    }



    // --- Initial Setup ---
    async function checkUserSession() {
        // Si estamos en la página dedicada de restablecimiento de contraseña,
        // no ejecutamos la lógica de checkUserSession que redirige a login o course page.
        // El formulario de restablecimiento tiene su propio manejo de eventos.
        if (document.getElementById('actual-reset-password-page')) {
            // Aún así, nos aseguramos de que elementos del quiz no relevantes estén ocultos
            // por si acaso script.js se carga en un contexto inesperado.
            const quizNav = document.getElementById('quiz-navigation');
            if (quizNav) quizNav.style.display = 'none';
            if (submitQuizButton) submitQuizButton.style.display = 'none';
            return; // Salimos temprano para no interferir con reset_password_form.php
        }

        loadTheme(); // Load theme preference or default

        try {
            const response = await fetch(CHECK_SESSION_URL);
            const data = await response.json();
            if (data.loggedIn && data.user) {
                currentUser = data.user;
                await loadUserQuizResultFromDB(); // Load quiz result
                await loadUserProgressFromDB();
                initializeCoursePage();
                showPage('course-page');
            } else {
                showPage('login-page');
            }
        } catch (error) {
            console.error("Error checking session:", error);
            showPage('login-page'); // Fallback to login if session check fails
        }
        // Ensure quiz navigation and submit button are hidden on initial load if not on quiz page
        const quizNav = document.getElementById('quiz-navigation');
        if (quizNav) {
            quizNav.style.display = 'none';
        }
        if (submitQuizButton) { // Check if submitQuizButton exists
            submitQuizButton.style.display = 'none';
        }
    }

    loadTheme(); // Initial theme load for all pages (including reset_password_form.php if script is loaded)
    checkUserSession();
    // submitQuizButton.style.display = 'none'; // Already handled in checkUserSession
});