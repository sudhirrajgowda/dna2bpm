<!DOCTYPE html>
<html>
    <head>
        <title>DNA&sup2; | SGR | {sgr_nombre}</title>
        <meta charset="UTF-8" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{module_url}assets/jscript/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{module_url}assets/jscript/bootstrap/css/bootstrap-responsive.min.css" />
        
        <!--<link rel="stylesheet" href="{base_url}jscript/fontawesome/css/font-awesome.min.css" />-->
        <link rel="stylesheet" href="{module_url}assets/css/font-awesome-4.0.3/css/font-awesome.min.css" />
        <link rel="stylesheet" href="{module_url}assets/jscript/jquery-ui-1.10.2.custom/css/smoothness/jquery-ui-1.10.2.custom.min.css" />	
        <link rel="stylesheet" href="{module_url}assets/css/extra-icons.css" />	
        <link rel="stylesheet" href="{module_url}assets/jscript/fullcalendar/fullcalendar.css" />
        <link rel="stylesheet" href="{module_url}assets/jscript/datepicker/css/datepicker.css" />
        <link rel="stylesheet" href="{module_url}assets/css/sgr.css" />
        <!--/ Custom CSS -->
        {css}
        
    </head>
    <body>
        <!--/ NAVIGATION -->
        <div class="navbar navbar-inverse navbar-static-top ">
            <div class="navbar-inner barra_{rol}">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="{module_url}">SOCIEDADES DE GARANTIAS RECIPROCAS</a>

                    <div class="nav-collapse collapse">
                        <ul class="nav">
                        </ul>
                        <ul class="nav pull-right inline"> 
                            

   
                            <li><a  href="{base_url}user/logout"><i class="fa fa-power-off"></i> Salir</a></li>                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- CONTAINER -->
        {content}
        <!-- CONTAINER -->

        <script src="{module_url}assets/jscript/jquery.min.js"></script>
        <script src="{module_url}assets/jscript/bootstrap/js/bootstrap.min.js"></script>
        <script src="{module_url}assets/jscript/bootbox.min.js"></script>
        <script src="{module_url}assets/jscript/datepicker/js/bootstrap-datepicker.js"></script>
        <script src="{module_url}assets/jscript/modernizr.custom.22198.js"></script>
        <!-- Custom JS -->
        <script type="text/javascript">
            //-----declare global vars
            var globals = {inline_js};

            // La clase offline es agregada por el fallback del manifiesto
            var offline = $('.offline').length;

            $(document).ready(function() {
                if (offline) {
                    $('#status').css('color', '#f00');

                } else {
                    $('#status').css('color', '#059B28');
                }
            });



        </script>
        {js}
    </body>
</html>
