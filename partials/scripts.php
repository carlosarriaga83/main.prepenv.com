    <!-- jQuery library js -->
    <script src="assets/js/lib/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js -->
    <script src="assets/js/lib/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Apex Chart js -->
    <script src="assets/js/lib/apexcharts.min.js"></script>
    <!-- Data Table js -->
    <script src="assets/js/lib/dataTables.min.js"></script>
    <script src="assets/js/lib/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/lib/dataTables.responsive.min.js"></script>
    <!-- Iconify Font js -->
    <script src="assets/js/lib/iconify-icon.min.js"></script>
    <!-- jQuery UI js -->
    <script src="assets/js/lib/jquery-ui.min.js"></script>
    <!-- Vector Map js -->
    <script src="assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
    <script src="assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
    <!-- Popup js -->
    <script src="assets/js/lib/magnifc-popup.min.js"></script>
    <!-- Slick Slider js -->
    <script src="assets/js/lib/slick.min.js"></script>
    <!-- prism js -->
    <script src="assets/js/lib/prism.js"></script>
    <!-- file upload js -->
    <script src="assets/js/lib/file-upload.js"></script>
    <!-- audioplayer -->
    <script src="assets/js/lib/audioplayer.js"></script>



    <!-- Alertas-->
    <script src="assets/js/lib/jquery-confirm.min.js"></script> 
    
    <!-- main js -->
    <script src="assets/js/app.js"></script>

    <?php echo (isset($script) ? $script   : '')?>
    
    
    
    <?php 
        
        
        
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);

    session_start();

    $id = $_SESSION['USER']['ID'];
    
    
    // OBTIENE INFO DEL USUARIO 
    
        $q = sprintf("SELECT * FROM Users WHERE id = '%s'   ", $id );  // echo $q . "\n";
        $R1 = SQL_2_OBJ_V2($q);
        //print_r($R1);die;
        
        $USER 	= $R1['PL'][0];
        
        
        $USER_ROLE_ID = $USER['ROLE_ID'];
    

    
    // OBTIENE INFO DEL ROL
    
        $q = sprintf("SELECT * FROM ROLES WHERE id = '%s'", $USER_ROLE_ID );  // echo $q . "\n";
        $R1 = SQL_2_OBJ_V2($q);
        //print_r($R1);die;
        
        $ROLES 	= $R1['PL'];  
    
    // NOMBRE DEL ROL 
    
        foreach($ROLES as $ROL){
            $ROL_ID_NAME[$ROL['ID']] = $ROL['NAME'];
        } 
    
    // ESCONDE COSAS DADO EL ROL DEL USUARIO 
     
        $REMOVALS = '';
        
        foreach($ROLES as $ROL){
        
            
        
            $ROL['ADMIN_VIEW']    == 'off' || !isset($ROL['ADMIN_VIEW'])    ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'admin_view'   ) : $REMOVALS = $REMOVALS;
            $ROL['ADMIN_ADD']     == 'off' || !isset($ROL['ADMIN_ADD'])     ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'admin_add'    ) : $REMOVALS = $REMOVALS;
            $ROL['ADMIN_EDIT']    == 'off' || !isset($ROL['ADMIN_EDIT'])    ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'admin_edit'   ) : $REMOVALS = $REMOVALS;
            $ROL['ADMIN_DELETE']  == 'off' || !isset($ROL['ADMIN_DELETE'])  ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'admin_delete' ) : $REMOVALS = $REMOVALS;
                       
            $ROL['CAN_VIEW']    == 'off' || !isset($ROL['CAN_VIEW'])    ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'view'   ) : $REMOVALS = $REMOVALS;
            $ROL['CAN_ADD']     == 'off' || !isset($ROL['CAN_ADD'])     ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'add'    ) : $REMOVALS = $REMOVALS; 
            $ROL['CAN_EDIT']    == 'off' || !isset($ROL['CAN_EDIT'])    ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'edit'   ) : $REMOVALS = $REMOVALS;
            $ROL['CAN_DELETE']  == 'off' || !isset($ROL['CAN_DELETE'])  ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'delete' ) : $REMOVALS = $REMOVALS;
               
        }
        
        //print_r($ROLES);die;
        
        $script1 .= '<script>' . $REMOVALS . '</script>';
    
    // ACCESO ILIMITADO PARA SA
    
        $USER_ROLE_ID == '777' ? $REMOVALS .= sprintf(' $("input").attr("readonly", false);' ) : $REMOVALS = $REMOVALS; 
        
        
        $USER_ROLE_ID != '777' ? $REMOVALS .= sprintf('$(".%s").addClass("d-none");', 'sa_only') : $REMOVALS = $REMOVALS; 
 
        
                
    
  
        $script1 .= '<script>' . $REMOVALS . '</script>';
        
        echo $script1;
    
    
    
    ?>
