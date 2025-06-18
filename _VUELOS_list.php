<?php $script ='<script>
                        $(".remove-item-btn").on("click", function() {
						//$(this).closest("tr").addClass("d-none")
                        });
						
						//let table = new DataTable("#TBL_DISHES");
						
            </script>';?>



<?php
	
    //error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	session_start();
	
	//if ($_SESSION['LOGIN'] != 1){ header("Location: index.php");	}
	
	include_once './PHP/MYF1.php';
	
	//echo GET_TS();die;
	//require_once '/vendor/autoload.php';
	
	
	//echo arrayToHtmlTable($EVENTOS, ['ID','NAME','COMENSALES','FECHA','EVENT_LINK']);
	

	// Assuming your flight configurations are stored in a table named 'GFLIGHTS'
	// and it has at least an 'ID' column (primary key) and a 'Datos' column (TEXT/JSON type).
	$q = sprintf("SELECT * FROM %s ORDER BY id DESC", 'GFLIGHTS');  // Fetching from GFLIGHTS table
    $R1 = SQL_2_OBJ_V2($q, 2);
    //print_r($R1['DATA']); die;
    // --- TEMPORARY DEBUGGING START ---
    // echo "<pre>Debug R1 (Raw output from SQL_2_OBJ_V2):\n";
    // var_dump($R1);
    // echo "</pre>";
    // --- TEMPORARY DEBUGGING END ---

	$RESP['R1'] = $R1; //print_r($R1);
    $DB_DATA = null; // Initialize to null

    if (isset($R1['DATA']) && is_array($R1['DATA'])) {
        $DB_DATA = $R1['DATA'];
    } else {
        // This block will execute if $R1['DATA'] is not set or not an array
        // echo "<!-- Notice: R1['DATA'] was not set or not an array. Check SQL_2_OBJ_V2 output. -->";
        // error_log("Flight Schedules - R1['DATA'] not found or not an array. R1 value: " . print_r($R1, true));
        $DB_DATA = []; // Ensure $DB_DATA is an empty array to prevent errors later
    }
    // --- TEMPORARY DEBUGGING START ---
    // echo "<pre>Debug DB_DATA (Processed data for table):\n";
    // var_dump($DB_DATA);
    // echo "</pre>";
    // die; // Stop execution here to check the output
    // --- TEMPORARY DEBUGGING END ---

		
	
	header('Content-type: html; charset=utf-8');
?>


<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Flight Schedules</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Home
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Flight Schedules</li>

                </ul>
            </div>
				
            
            <?php 
                $T_PARAMS 					= [];
                $T_PARAMS['DB']['TABLE'] 	= 'GFLIGHTS';
                $T_PARAMS['DB']['COLS'] 	= [ 'user', 'from', 'to', 'year', 'month', 'day', 'enabled',  'Action'];
                
                $T_PARAMS['TABLE']['ID'] 	= 'TBL_1';
                $T_PARAMS['TABLE']['COLS'] 	= [ 'Usuario', 'Desde', 'Hasta', 'Año', 'Mes', 'Dia', 'Habilitado',  'Action'];
                $T_PARAMS['TABLE']['href'] 	= '_CURSOS_licencias.php';

                $T_PARAMS['TABLE']['SEARCH_BAR']	 	= true;
                $T_PARAMS['TABLE']['ADD_NEW']['TEXT'] 	= 'Add new';
                $T_PARAMS['TABLE']['ADD_NEW']['href'] 	= '_CURSOS_licencias.php';
                $T_PARAMS['TABLE']['ADD_NEW']['MISC_PROPS'] 	= 'data-bs-toggle="modal" data-bs-target="#Modal_1"';
                

                //echo DB_2_TABLE_V3($T_PARAMS );  

                include './_ELEMENTS/VUELOS/vuelos_list.php';
                
            ?>



            <!-- TABLE_SEARCHES Start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Flight List</h5>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#Modal_1" onclick="LOAD_ID('');">
                                    <iconify-icon icon="solar:add-circle-bold" class="me-1"></iconify-icon> Add New Flight
                                </button>
                            </div>
                            <div class="mt-3">
                                <input type="text" id="flightSearchInput" onkeyup="filterFlightsTable()" placeholder="Search flights..." class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="TBL_FLIGHT_SCHEDULES">
                                    <thead class="bg-primary-50">
                                        <tr>
                                            <th>User</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Year</th>
                                            <th>Month</th>
                                            <th>Enabled</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($DB_DATA) && is_array($DB_DATA)): ?>
                                            <?php foreach ($DB_DATA as $row): ?>
                                                <?php // $flightData now directly refers to $row as per SQL_2_OBJ_V2 output ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($row['user'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['from'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['to'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['year'] ?? 'N/A'); ?></td>
                                                        <td><?php echo htmlspecialchars($row['month'] ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php if (isset($row['enabled']) && $row['enabled']): ?>
                                                                <span class="badge bg-success-focus text-success-main">Yes</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger-focus text-danger-main">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-info-light" onclick="LOAD_ID('<?php echo $row['ID']; ?>')" data-bs-toggle="modal" data-bs-target="#Modal_1">
                                                                <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon> Edit
                                                            </button>
                                                            <!-- Add delete button here if needed -->
                                                        </td>
                                                    </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No flight schedules found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TABLE_SEARCHES finish -->
        </div>


        <!-- Modal Start -->
        <div class="modal fade" id="Modal_1" tabindex="-1" aria-labelledby="Modal_1_LBL" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
                <div class="modal-content radius-16 bg-base">
                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                        <h1 class="modal-title fs-5" id="Modal_1_LBL">Flight Configuration</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-24">
                        <form id="FRM_1" action="#" FRM="FRM_1" DB="GFLIGHTS" DB_ID="2" >
                            <div class="row">
                                <div class="col-md-6 mb-20">
                                    <label for="edit_id_field" class="form-label fw-semibold text-primary-light text-sm mb-8">🆔 Edit ID</label>
                                    <input type="text" id="edit_id_field" name="EDIT_ID" class="form-control radius-8" value="" FRM="FRM_1" DB="GFLIGHTS" readonly placeholder="ID">
                                </div>
                                <div class="col-md-6 mb-20 align-self-center">


                                    <div class="form-switch switch-primary d-flex align-items-center gap-3">
                                        <input class="form-check-input" type="checkbox" role="switch" id="flightEnabled" name="enabled" value="true" checked>
                                        <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="flightEnabled">Enabled</label>
                                    </div>
                                </div>



                            </div>
                        
                            <div class="row">
                                <div class="col-md-12 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">User ID<span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" placeholder="Enter User ID" name="user" required>
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">From (Airport Code)<span class="text-danger-600">*</span></label>
                                    <select class="form-select radius-8 searchable-airport-select" name="from" required>
                                        <option value="" disabled selected>Select Origin Airport</option>
                                        <option value="LHR">LHR - London Heathrow Airport, UK</option>
                                        <option value="CDG">CDG - Paris Charles de Gaulle Airport, France</option>
                                        <option value="AMS">AMS - Amsterdam Airport Schiphol, Netherlands</option>
                                        <option value="FRA">FRA - Frankfurt Airport, Germany</option>
                                        <option value="IST">IST - Istanbul Airport, Turkey</option>
                                        <option value="MAD">MAD - Madrid Barajas Airport, Spain</option>
                                        <option value="BCN">BCN - Barcelona El Prat Airport, Spain</option>
                                        <option value="MUC">MUC - Munich Airport, Germany</option>
                                        <option value="FCO">FCO - Rome Fiumicino Airport, Italy</option>
                                        <option value="ZRH">ZRH - Zurich Airport, Switzerland</option>
                                        <option value="CPH">CPH - Copenhagen Airport, Denmark</option>
                                        <option value="VIE">VIE - Vienna International Airport, Austria</option>
                                        <option value="DUB">DUB - Dublin Airport, Ireland</option>
                                        <option value="ARN">ARN - Stockholm Arlanda Airport, Sweden</option>
                                        <option value="OSL">OSL - Oslo Gardermoen Airport, Norway</option>
                                        <option value="HEL">HEL - Helsinki-Vantaa Airport, Finland</option>
                                        <option value="BRU">BRU - Brussels Airport, Belgium</option>
                                        <option value="LIS">LIS - Lisbon Airport, Portugal</option>
                                        <option value="ATH">ATH - Athens International Airport, Greece</option>
                                        <option value="JFK">JFK - John F. Kennedy International Airport, New York, USA</option>
                                        <option value="LAX">LAX - Los Angeles International Airport, USA</option>
                                        <option value="ORD">ORD - O'Hare International Airport, Chicago, USA</option>
                                        <option value="ATL">ATL - Hartsfield-Jackson Atlanta International Airport, USA</option>
                                        <option value="DFW">DFW - Dallas/Fort Worth International Airport, USA</option>
                                        <option value="DEN">DEN - Denver International Airport, USA</option>
                                        <option value="SFO">SFO - San Francisco International Airport, USA</option>
                                        <option value="MIA">MIA - Miami International Airport, USA</option>
                                        <option value="DXB">DXB - Dubai International Airport, UAE</option>
                                        <option value="HND">HND - Tokyo Haneda Airport, Japan</option>
                                        <option value="SIN">SIN - Singapore Changi Airport, Singapore</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">To (Airport Code)<span class="text-danger-600">*</span></label>
                                    <select class="form-select radius-8 searchable-airport-select" name="to" required>
                                        <option value="" disabled selected>Select Destination Airport</option>
                                        <option value="LHR">LHR - London Heathrow Airport, UK</option>
                                        <option value="CDG">CDG - Paris Charles de Gaulle Airport, France</option>
                                        <option value="AMS">AMS - Amsterdam Airport Schiphol, Netherlands</option>
                                        <option value="FRA">FRA - Frankfurt Airport, Germany</option>
                                        <option value="IST">IST - Istanbul Airport, Turkey</option>
                                        <option value="MAD">MAD - Madrid Barajas Airport, Spain</option>
                                        <option value="BCN">BCN - Barcelona El Prat Airport, Spain</option>
                                        <option value="MUC">MUC - Munich Airport, Germany</option>
                                        <option value="FCO">FCO - Rome Fiumicino Airport, Italy</option>
                                        <option value="ZRH">ZRH - Zurich Airport, Switzerland</option>
                                        <option value="CPH">CPH - Copenhagen Airport, Denmark</option>
                                        <option value="VIE">VIE - Vienna International Airport, Austria</option>
                                        <option value="DUB">DUB - Dublin Airport, Ireland</option>
                                        <option value="ARN">ARN - Stockholm Arlanda Airport, Sweden</option>
                                        <option value="OSL">OSL - Oslo Gardermoen Airport, Norway</option>
                                        <option value="HEL">HEL - Helsinki-Vantaa Airport, Finland</option>
                                        <option value="BRU">BRU - Brussels Airport, Belgium</option>
                                        <option value="LIS">LIS - Lisbon Airport, Portugal</option>
                                        <option value="ATH">ATH - Athens International Airport, Greece</option>
                                        <option value="JFK">JFK - John F. Kennedy International Airport, New York, USA</option>
                                        <option value="LAX">LAX - Los Angeles International Airport, USA</option>
                                        <option value="ORD">ORD - O'Hare International Airport, Chicago, USA</option>
                                        <option value="ATL">ATL - Hartsfield-Jackson Atlanta International Airport, USA</option>
                                        <option value="DFW">DFW - Dallas/Fort Worth International Airport, USA</option>
                                        <option value="DEN">DEN - Denver International Airport, USA</option>
                                        <option value="SFO">SFO - San Francisco International Airport, USA</option>
                                        <option value="MIA">MIA - Miami International Airport, USA</option>
                                        <option value="DXB">DXB - Dubai International Airport, UAE</option>
                                        <option value="HND">HND - Tokyo Haneda Airport, Japan</option>
                                        <option value="SIN">SIN - Singapore Changi Airport, Singapore</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Year<span class="text-danger-600">*</span></label>
                                    <input type="number" class="form-control radius-8" placeholder="e.g., 2025" name="year" min="<?php echo date('Y'); ?>" required>
                                </div>
                                <div class="col-4 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Month<span class="text-danger-600">*</span></label>
                                    <input type="number" class="form-control radius-8" placeholder="e.g., 8 for August" name="month" min="1" max="12" required>
                                </div>
                                <div class="col-4 mb-20">
                                    <label class="form-label fw-semibold text-primary-light text-sm mb-8">Day<span class="text-danger-600">*</span></label>
                                    <input type="number" class="form-control radius-8" placeholder="e.g., 1 for 1st" name="day" min="1" max="31" >
                                </div>
                                
                                
                            </div>

                            <!-- Separator and Schedule Details Section -->
                            <hr class="my-24">

                            <div class="mb-20"> <!-- Section for Alert Details -->
                                <h6 class="fw-semibold mb-16 d-flex align-items-center">
                                    <iconify-icon icon="mdi:whatsapp" class="icon me-1"></iconify-icon>
                                    <span>Alert Details</span>
                                </h6>
                                <div class="border p-16 radius-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Schedule Type<span class="text-danger-600">*</span></label>
                                            <select class="form-select radius-8" name="schedule_type" required>
                                                <option value="recurring" selected>Recurring</option>
                                                <option value="one-time">One-Time</option>
                                                <!-- Add other types if needed -->
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-20">                                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Timezone<span class="text-danger-600">*</span></label>
                                            <select class="form-select radius-8" name="schedule_timezone" required>
                                                <option value="America/Toronto">America/Toronto</option>
                                                <option value="America/Montreal">America/Montreal</option>
                                                <option value="America/Vancouver">America/Vancouver</option>
                                                <option value="America/Edmonton">America/Edmonton</option>
                                                <option value="America/Winnipeg">America/Winnipeg</option>
                                                <option value="America/Halifax">America/Halifax</option>
                                                <option value="America/New_York">America/New_York</option>
                                                <option value="America/Los_Angeles">America/Los_Angeles</option>
                                                <option value="America/Mexico_City">America/Mexico_City</option>
                                                <option value="Europe/London">Europe/London</option>
                                                <option value="Asia/Tokyo">Asia/Tokyo</option>
                                                <option value="Australia/Sydney">Australia/Sydney</option>
                                                <option value="UTC">UTC</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-20">
                                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Frequency Unit<span class="text-danger-600">*</span></label>
                                            <select class="form-select radius-8" name="schedule_frequency_unit" required>
                                                <option value="hour" >Hour</option>
                                                <option value="day" selected>Day</option>
                                                <option value="week">Week</option>
                                                <option value="month">Month</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-20">                                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Frequency Interval<span class="text-danger-600">*</span></label>
                                            <input type="number" class="form-control radius-8" placeholder="e.g., 1" name="schedule_frequency_interval" min="1" value="1" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-20">
                                            <label class="form-label fw-semibold text-primary-light text-sm mb-8">Time of Day (HH:MM)<span class="text-danger-600">*</span></label>
                                            <div id="times_of_day_container">
                                                <div class="row mb-2">
                                                    <div class="col">
                                                        <input type="time" class="form-control radius-8" name="schedule_time_of_day">
                                                    </div>
                                                </div>

                                            </div>
                                            <small class="form-text text-muted">Provide at least one time. For a more user-friendly experience with multiple time entries, consider implementing JavaScript to dynamically add/remove time fields.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                    <button type="button" class="border border-secondary text-secondary-emphasis bg-hover-secondary-200 text-md px-40 py-11 radius-8" data-bs-dismiss="modal">
										Close
									</button>
                                    <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8">
                                        Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-48 py-12 radius-8">
                                        Save
                                    </button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->

		
<?php include './partials/layouts/layoutBottom.php' ?>



<script>



        function LOAD_ID(id) { 
            clearForm('FRM_1');
            $('input[name="EDIT_ID"]').val(id);

            // Set Modal Title
            if (id && id !== '') {
                $('#Modal_1_LBL').text('Edit Flight Configuration');
            } else {
                $('#Modal_1_LBL').text('Add New Flight Configuration');
                // If Select2 is used for airport codes, ensure they are reset for new entries
                $('.searchable-airport-select').val(null).trigger('change');
            }
            $('input[name="EDIT_ID"]').trigger( "change" );
        }
        
        
        $(document).on('change','input[name="EDIT_ID"]', function LOAD(e) { 
            
            debugger;
            var TABLA   = $(this).attr('DB');
            var FORM    = $(this).attr('FRM');
            var ID      = $(this).val();
			var id = $('#' + FORM + ' input[name="EDIT_ID"]').val();
            
            OBJ = {};
            
            OBJ['ID'] 		= id;
            OBJ['TABLA'] 	= TABLA;
            OBJ['DB_ID'] 	= 2 ;
            var resp = POST_API(OBJ, 'API/get_data/for_gflights.php');
            
            // resp.PL is likely an array, get the first element if it exists
            if (resp && resp.PL ) {
                jsonToForm_v3(FORM, resp.PL);
            } else {
                console.warn('No data returned for ID:', id, 'from API. Response:', resp);
            }
        });
		
        $(document).on('submit','#FRM_1', function VALIDAR(e) { 
        //$('#formulario').on('submit', function (e) {
          if (e.isDefaultPrevented()) {
            // handle the invalid form...
          } else {
            // everything looks good!
            e.preventDefault(); //prevent submit
            SAVE_CHANGES(this);
            
          }
        });

        // Initialize Select2 for airport dropdowns
        $(document).ready(function() {
            $('.searchable-airport-select').select2({
                theme: "bootstrap-5", // Use this theme if you included the select2-bootstrap-5-theme CSS
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style', // Adjust width as needed
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('#Modal_1'), // Crucial for Select2 to work correctly inside a Bootstrap modal
                tags: true // Allows users to input values not in the list
            });

            // Clear Select2 selection when form is reset
            $('#FRM_1').on('reset', function() {
                $('.searchable-airport-select').val(null).trigger('change');
            });
        });

        function filterFlightsTable() {
            let input, filter, table, tbody, tr, td, i, j, txtValue, found;
            input = document.getElementById("flightSearchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("TBL_FLIGHT_SCHEDULES");
            tbody = table.getElementsByTagName("tbody")[0];
            tr = tbody.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                found = false;
                // Loop through all cells in a row, except the last one (Action column)
                for (j = 0; j < tr[i].cells.length - 1; j++) {
                    td = tr[i].cells[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break; 
                        }
                    }
                }
                if (found) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }

</script>