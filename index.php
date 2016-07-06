<?php ?>
<html>
    <head>
        <title>
            Web Scrapper
        </title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Latest compiled and minified cs -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">

        <style>

            html, body {
                height: 100%;
            }
            .container {
                /*background-image: url("background.jpg");*/
                width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center;
                padding-top: 100px;
            }

            .center {
                text-align: center;
            }

            .white {
                color: #000000;
            }

            p {
                padding-top: 15px;
                padding-bottom: 15px;
            }

            button {
                margin-top: 20px;
            }

            .alert {
                margin-top: 20px;
                display: none;
                text-align: center;
            }

            .results-table th, .results-table td {
                text-align: center;
            }

            .execution_time {
                color: #000000;
                font-weight: bold;
            }

        </style>

    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3 center">
                    <h1 class="center white">Web Scrapper</h1>
                    <p class="center white"> Enter the Url which is to be Scrapped</p>

                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control" name="site_url" id="site_url" placeholder= "Eg: http://reddit.com/r/Android"/>
                        </div>
                        <button id="scrap_me_btn" class="btn btn-success btn-lg">
                            Scrape Me
                        </button>
                    </form>
                </div>
            </div>

            <div class="row">

                <div id="success" class="alert alert-success"></div>
                <div id="fail" class="alert alert-danger">Couldn't find any data</div>
                <div id="warning" class="alert alert-warning">Please enter a url</div>
            </div>
        </div>

        <script   src="//code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script>

            $(function () {

                var _LoadingImageSrc = 'ajax_loader.gif';
                var loading = undefined;
                var loadingProgress = false;

                var preload = new Image();
                preload.src = _LoadingImageSrc;

                //Execute the following on clicking the submit button  
                $('#scrap_me_btn').on('click', function (e) {
                    
                    e.preventDefault();
                    $(".alert").hide();

                    //Fetching the url to be scrapped
                    var link = $("#site_url").val();

                    //Check if the user has submitted any url to be scrapped
                    if (link == "") {

                        //Display a warning message
                        $("#warning").fadeIn();
                        return false;
                    } else {

                        //Fadeout the submit button
                        $('#scrap_me_btn').fadeOut(300);

                        //Pass the url to scraper script and fetch the output from the script which is to be displayed
                        $.ajax({
                            url: 'scraper.php?site_url=' + link,
                            type: 'post',
                            beforeSend: function () {

                                //For displaying the progress bar
                                displayLoading();
                            },
                            success: function (data, status) {

                                //For removing the loading icon diplay
                                loadingProgress = true;

                                //Remove the progress bar on success
                                hideLoading();

                                if (data == "") {
                                    $("#fail").fadeIn();
                                } else {
                                    $("#success").html(data).fadeIn();
                                }
                            },
                            error: function (jqXHR, exception) {
                                console.log(xhr);
                                console.log("Details: " + desc + "\nError:" + err);

                                //Remove the progress bar on success
                                hideLoading();

                                var msg = '';
                                if (jqXHR.status === 0) {
                                    msg = 'Not connect.\n Verify Network.';
                                } else if (jqXHR.status == 404) {
                                    msg = 'Requested page not found. [404]';
                                } else if (jqXHR.status == 500) {
                                    msg = 'Internal Server Error [500].';
                                } else if (exception === 'parsererror') {
                                    msg = 'Requested JSON parse failed.';
                                } else if (exception === 'timeout') {
                                    msg = 'Time out error.';
                                } else if (exception === 'abort') {
                                    msg = 'Ajax request aborted.';
                                } else {
                                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                }
                                $("#fail").html(msg).fadeIn();
                            }
                        }); // end ajax call
                    }
                });

                /**
                 * Function to display the progress bar icon and the related text
                 * @author : Sreenath
                 */
                function displayLoading() {

                    if (loadingProgress) {
                        return;
                    }
                    var mainDiv = document.createElement('div');
                    var black = document.createElement('div');
                    var loadingDiv = document.createElement('div');
                    mainDivStyle = "position:fixed;" + "top:0px;"
                            + "left:0px; z-index: 1000;" + "width:100%;" + "height:100%;";
                    blackStyle = "position:absolute;" + "top:0px;" + "left:0px;"
                            + "width:100%;" + "height:100%;"
                            + "background-color:rgba(255,255,255,0.9);"
                    loadingDivStyle = "width:183px;" + "height:165px;" + "margin:auto;"
                            + "position:relative;" + "top:47%;";
                    mainDiv.setAttribute('style', mainDivStyle);
                    black.setAttribute('style', blackStyle);
                    loadingDiv.setAttribute('style', loadingDivStyle);
                    mainDiv.appendChild(black);
                    mainDiv.appendChild(loadingDiv);
                    document.body.appendChild(mainDiv);

                    $(loadingDiv)
                            .html(
                                    '<img src='
                                    + preload.src
                                    + ' style="width:50%;height:50%;"/><div style="color:#fff;font-size: 20px;">Processing...</div>');
                    loading = mainDiv;
                    loadingProgress = true;
                }

                /**
                 * Function to hide the loading icon
                 * @author : Sreenath
                 */
                function hideLoading() {

                    //Setting the flag
                    loadingProgress = false;

                    // Hide the loading icon
                    if (loading) {
                        document.body.removeChild(loading);
                        loading = undefined;
                    }
                }
            });
        </script>
    </body>
</html>