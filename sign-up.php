<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">


<?php 
	
	session_start();

	if ($_SESSION['LOGIN'] == 1){
		header("Location: index.php");	
	}else{
		//header("Location: sign-in.php");	
	}

	include_once './PHP/MYF1.php';
	
	header('Content-type: html; charset=utf-8');
	
    
    if ( isset($_SESSION['SIGN_UP']['PROMPT']) ) {
        $PROMPT = $_SESSION['SIGN_UP']['PROMPT'];
        $_SESSION['SIGN_UP']['PROMPT'] = '';
    }
    if ( isset($_SESSION['SIGN_UP']['EMAIL']))   {$EMAIL    = $_SESSION['SIGN_UP']['EMAIL'];    $_SESSION['SIGN_UP']['EMAIL'] = '';}
    if ( isset($_SESSION['SIGN_UP']['PWD']))     {$PWD      = $_SESSION['SIGN_UP']['PWD'];      $_SESSION['SIGN_UP']['PWD'] = '';}
    if ( isset($_SESSION['SIGN_UP']['NAME']))     {$NAME     = $_SESSION['SIGN_UP']['NAME'];     $_SESSION['SIGN_UP']['NAME'] = '';}
    
?>


<?php include './partials/head.php' ?>


<body>

    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="assets/images/auth/auth-img.png" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <a href="index.php" class="mb-40 max-w-290-px">
                        <img src="assets/images/logo.png" alt="">
                    </a>
                    <h4 class="mb-12">Sign Up to your Account</h4>
                    <p class="mb-32 text-secondary-light text-lg">Welcome back! please enter your detail</p>
                    <?php echo $PROMPT; ?>
                </div>
                <form  method="post" action="API/Sign-up.php">
                    <div class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="f7:person"></iconify-icon>
                        </span>
                        <input type="text" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Username" name="username" value="<?php echo $NAME; ?>" required>
                    </div>
                    <div class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Email" name="email" value="<?php echo $EMAIL; ?>" required>
                    </div>
                    <div class="mb-20">
                        <div class="position-relative ">
                            <div class="icon-field">
                                <span class="icon top-50 translate-middle-y">
                                    <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                                </span>
                                <input type="password" class="form-control h-56-px bg-neutral-50 radius-12" id="your-password" placeholder="Password" name="password" value="<?php echo $PWD; ?>" required>
                            </div>
                            <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#your-password"></span>
                        </div>
                        <span class="mt-12 text-sm text-secondary-light">Your password must have at least 8 characters</span>
                    </div>
                    <div class="">
                        <div class="d-flex justify-content-between gap-2">
                            <div class="form-check style-check d-flex align-items-start">
                                <input class="form-check-input border border-neutral-300 mt-4" type="checkbox" value="" id="condition">
                                <label class="form-check-label text-sm" for="condition">
                                    By creating an account means you agree to the
                                    <a href="javascript:void(0)" class="text-primary-600 fw-semibold">Terms & Conditions</a> and our
                                    <a href="javascript:void(0)" class="text-primary-600 fw-semibold">Privacy Policy</a>
                                </label>
                            </div>

                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"> Sign Up</button>

                    <div class="mt-32 center-border-horizontal text-center">
                        <span class="bg-base z-1 px-4">Or sign up with</span>
                    </div>
                    <div class="mt-32 d-flex align-items-center gap-3">
                        <button type="button" class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50">
                            <iconify-icon icon="ic:baseline-facebook" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                            Google
                        </button>
                        <button type="button" class="fw-semibold text-primary-light py-16 px-24 w-50 border radius-12 text-md d-flex align-items-center justify-content-center gap-12 line-height-1 bg-hover-primary-50">
                            <iconify-icon icon="logos:google-icon" class="text-primary-600 text-xl line-height-1"></iconify-icon>
                            Google
                        </button>
                    </div>
                    <div class="mt-32 text-center text-sm">
                        <p class="mb-0">Already have an account? <a href="sign-in.php" class="text-primary-600 fw-semibold">Sign In</a></p>
                    </div>

                </form>
            </div>
        </div>
    </section>

    <?php $script = '<script>
                        // ================== Password Show Hide Js Start ==========
                        function initializePasswordToggle(toggleSelector) {
                            $(toggleSelector).on("click", function() {
                                $(this).toggleClass("ri-eye-off-line");
                                var input = $($(this).attr("data-toggle"));
                                if (input.attr("type") === "password") {
                                    input.attr("type", "text");
                                } else {
                                    input.attr("type", "password");
                                }
                            });
                        }
                        // Call the function
                        initializePasswordToggle(".toggle-password");
                        // ========================= Password Show Hide Js End ===========================
                    </script>';?>

    <?php include './partials/scripts.php' ?>



<script>
    $(document).ready(function() {


        // Call the function to hide the alert after 5000 milliseconds (5 seconds)
        hideAlertAfterTime($('.alert'), 5000);


    });
</script>

</body>

</html>